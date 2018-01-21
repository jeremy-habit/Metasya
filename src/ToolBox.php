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
   * @var ToolBox $instance : it is private in order to implement the singleton pattern
   */
  private static $instance;

  /**
   * ToolBox constructor.
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
   * @return ToolBox
   */
  public static function getInstance()
  {
    if (!(self::$instance instanceof self)) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  /**
   * List files according one or several extensions
   *
   * @param $folderPath
   * @param array $lstExtensions
   * @return array
   */
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
   * Determine the operating system (windows or unix)
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
   * Allow to remove a directory and it's content recursively
   *
   * @param $dir
   */
  public function recursiveRmdir($dir)
  {
    $bannedValue = array("*", "..", ".");
    if (!in_array($dir, $bannedValue) && is_dir($dir)) {
      $dirAndFiles = array_diff(scandir($dir), $bannedValue);
      foreach ($dirAndFiles as $dirOrFile) {
        if ($dirOrFile != "." && $dirOrFile != "..") { /* normally impossible thanks to array_diff above but ... */
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
   * Check if a string starts with an other one
   *
   * @param $haystack
   * @param $needle
   * @return bool
   */
  public function startsWith($haystack, $needle)
  {
    $length = strlen($needle);
    return (substr($haystack, 0, $length) === $needle);
  }

  /**
   * Check if a string ends with an other one
   *
   * @param $haystack
   * @param $needle
   * @return bool
   */
  public function endsWith($haystack, $needle)
  {
    $length = strlen($needle);

    return $length === 0 ||
      (substr($haystack, -$length) === $needle);
  }

  /**
   * Return json file content as array
   *
   * @param $jsonFilePath
   * @return mixed|array
   */
  public function getJsonFileAsArray($jsonFilePath)
  {
    if (file_exists($jsonFilePath)) {
      $stringifiedJson = file_get_contents($jsonFilePath);
      return json_decode($stringifiedJson, true);
    }
    return null;
  }

  /**
   * Convert any object to array recursively.
   *
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
