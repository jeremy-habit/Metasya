<?php

namespace PHPMetadataManager\Inheritance;

use Exception;
use PHPMetadataManager\InterfaceRepository\TaskerInterface;

/**
 * Class AbstractTasker
 * @package PHPMetadataManager\Inheritance
 */
abstract class AbstractTasker implements TaskerInterface
{
  /**
   * @var string $filePath
   */
  protected $filePath;

  /**
   * AbstractTasker constructor.
   * @param $filePath
   */
  public function __construct($filePath)
  {
    $this->filePath = $filePath;
  }

  /**
   * Replace the multiple whitespaces by one whitespace.
   * @param $stringifiedCmd
   * @return mixed
   */
  private function trim_Multiple_Whitespaces($stringifiedCmd)
  {
    return trim(preg_replace("/ {2,}/", " ", $stringifiedCmd));
  }


  /**
   * Check if a string is json or not (true or false)
   * @param $string
   * @return bool
   */
  protected function isJson($string)
  {
    json_decode($string);
    return (json_last_error() == JSON_ERROR_NONE);
  }

  /**
   * Return json file content as array
   * @param $jsonFilePath
   * @return null|array
   */
  protected function extractJsonFromFile($jsonFilePath)
  {
    if (file_exists($jsonFilePath)) {
      $stringifiedJson = file_get_contents($jsonFilePath);
      return json_decode($stringifiedJson, true)[0];
    }
    return null;
  }

  /**
   * Execute a stringified command with exiftool and return its result.
   * @param $stringifiedCmd
   * @param bool $jsonOutput
   * @return array|null|string
   */
  protected function execute($stringifiedCmd, $jsonOutput = false)
  {
    /*return $this->trim_Multiple_Whitespaces("exiftool " . (($jsonOutput) ? "-json " : null) . $stringifiedCmd . " " . $this->filePath);*/
    try {
      if (file_exists($this->filePath)) {
        $cmdResult = shell_exec($this->trim_Multiple_Whitespaces("exiftool " . (($jsonOutput) ? "-json " : null) . $stringifiedCmd . " " . $this->filePath));
        if ($cmdResult == null) {
          if (!$jsonOutput) {
            return ['exiftoolMessage' => trim($cmdResult), 'success' => false];
          } else {
            return null;
          }
        } else {
          if ($this->isJson($cmdResult)) {
            return $this->convert_Object_To_Array(json_decode($cmdResult)[0]);
          } else {
            return ['exiftoolMessage' => trim($cmdResult), 'success' => true];
          }
        }
      }
      return "Error : file \" " . $this->filePath . " \" not found !";
    } catch (Exception $exception) {
      return $exception->getMessage();
    }
  }

  /**
   * Convert any object to array recursively.
   * @param $obj
   * @return array
   */
  protected function convert_Object_To_Array($obj)
  {
    if (is_object($obj)) $obj = (array)$obj;
    if (is_array($obj)) {
      $newArray = [];
      foreach ($obj as $key => $value) {
        $newArray[$key] = $this->convert_Object_To_Array($value);
      }
    } else $newArray = $obj;
    return $newArray;
  }

  /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
  /* ### GETTERS & SETTERS ### */
  /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */

  /**
   * @return mixed
   */
  public function getFilePath()
  {
    return $this->filePath;
  }


}
