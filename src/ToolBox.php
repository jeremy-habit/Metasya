<?php

namespace MagicMonkey\Metasya;

/**
 * Class ToolBox
 * @package MagicMonkey\Metasya
 */
class ToolBox
{

  const DS = DIRECTORY_SEPARATOR;

  /**
   * $instance is private in order to implement the singleton pattern
   */
  private static $instance;

  /**
   * private constructor
   */
  private function __construct()
  {

  }

  /**
   * Deactivate the cloning
   */
  private function __clone()
  {
  }

  /**
   * Method to reach the UNIQUE instance of the class.
   *
   * @return $this
   */
  public static function getInstance()
  {
    if (!(self::$instance instanceof self)) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  public function lsFiles($folderPath, $lstExtensions = array('txt'))
  {
    $stringifiedExtensionsList = "";
    for ($i = 0; $i < sizeof($lstExtensions); $i++) {
      $stringifiedExtensionsList .= $lstExtensions[$i];
      if (($i + 1) < sizeof($lstExtensions)) { // it's not the last
        $stringifiedExtensionsList .= ",";
      }
    }
    return glob($folderPath . self::DS . '*.{' . $stringifiedExtensionsList . '}', GLOB_BRACE);
  }

  /**
   * Determines the operating system (windows or unix)
   * @return string
   */
  public function determinesOS()
  {
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
      return "windows";
    }
    return "unix";
  }

  /**
   * @param $dir
   */
  public function recursiveRmdir($dir)
  {
    $bannedValue = array("*", "..", ".");
    if (!in_array($dir, $bannedValue) && is_dir($dir)) {
      $dirAndFiles = array_diff(scandir($dir), $bannedValue);
      foreach ($dirAndFiles as $dirOrFile) {
        if ($dirOrFile != "." && $dirOrFile != "..")  { /* normally impossible thanks to array_diff above but ... */
          if (is_dir($dir . self::DS . $dirOrFile))
            $this->recursiveRmdir($dir . self::DS . $dirOrFile);
          else
            unlink($dir . self::DS . $dirOrFile);
        }
      }
      rmdir($dir);
    }
  }

  /**
   * Replace the multiple whitespaces by one whitespace.
   * @param $text
   * @return mixed
   */
  public function trimMultipleWhitespaces($text)
  {
    return trim(preg_replace("/ {2,}/", " ", $text));
  }

  /**
   * Check if a string is json or not (true or false)
   * @param $text
   * @return bool
   */
  public function isJson($text)
  {
    json_decode($text);
    return (json_last_error() == JSON_ERROR_NONE);
  }

  /**
   * Return json file content as array
   * @param $jsonFilePath
   * @return null|array
   */
  public function extractJsonFromFile($jsonFilePath)
  {
    if (file_exists($jsonFilePath)) {
      $stringifiedJson = file_get_contents($jsonFilePath);
      return json_decode($stringifiedJson, true);
    }
    return null;
  }

  /**
   * Convert any object to array recursively.
   * @param $obj object
   * @return array
   */
  public function convertObjectToArray($obj)
  {
    if (is_object($obj)) $obj = (array)$obj;
    if (is_array($obj)) {
      $newArray = [];
      foreach ($obj as $key => $value) {
        $newArray[$key] = $this->convertObjectToArray($value);
      }
    } else $newArray = $obj;
    return $newArray;
  }

}
