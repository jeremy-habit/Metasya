<?php

namespace MagicMonkey\Metasya;

use Exception;
use MagicMonkey\Metasya\Schema\Schema;
use MagicMonkey\Metasya\Tasker\ReaderTasker;
use MagicMonkey\Metasya\Tasker\WriterTasker;
use MagicMonkey\Metasya\Tasker\EraserTasker;

/**
 * Class MetadataHelper
 */
class MetadataHelper
{

  /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
  /* ### CONSTANTS ### */
  /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */

  /**
   *
   */
  const DS = DIRECTORY_SEPARATOR;
  /**
   *
   */
  const EXIFTOOL_PATH = "vendor" . self::DS . "magicmonkey" . self::DS . "metasya" . self::DS . "exiftool" . self::DS;

  /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
  /* ### ATTRIBUTES & CONSTRUCTORS ### */
  /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */

  private $stateHelper;

  /**
   * @var boolean $useProvidedExiftool
   */
  private $useProvidedExiftool;

  /**
   * MetadataHelper constructor.
   * @param string $filePath
   * @param bool $useProvidedExiftool
   */
  public function __construct($filePath, $useProvidedExiftool = true)
  {
    $this->filePath = $filePath;
    $this->useProvidedExiftool = $useProvidedExiftool;
    $this->schemas = array();
    $this->initialize_Taskers();
  }

  /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
  /* ### PRIVATE FUNCTIONS ### */
  /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */

  /**
   * Initialize taskers with the $filePath.
   * Useful when a new MetadataManger is created or when the $filePath is changed.
   */
  private function initialize_Taskers()
  {
    unset($this->reader);
    unset($this->writer);
    unset($this->eraser);
    $exiftoolPath = ($this->useProvidedExiftool) ? $this->generate_Full_Exiftool_Path() : "";
    $this->reader = new ReaderTasker($this->filePath, $exiftoolPath);
    $this->writer = new WriterTasker($this->filePath, $exiftoolPath);
    $this->eraser = new EraserTasker($this->filePath, $exiftoolPath);
  }

  /**
   * Concatenates the EXIFTOOL_PATH const with the result of the function determine_OS in order generate the path to exiftool exe
   * @return string
   */
  private function generate_Full_Exiftool_Path()
  {
    return self::EXIFTOOL_PATH . $this->determines_OS() . self::DS;
  }

  /**
   * Determines the operating system (windows or unix)
   * @return string
   */
  private function determines_OS()
  {
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
      return "windows";
    }
    return "unix";

  }

  /**
   * Return the version of exiftool (provided version or installed/local version)
   * @param bool $providedExiftoolVersion
   * @return string
   */
  private function get_Exiftool_Version($providedExiftoolVersion = true)
  {
    try {
      $cmd = ($providedExiftoolVersion) ? $this->generate_Full_Exiftool_Path() . "exiftool -ver" : "exiftool -ver";
      return shell_exec(escapeshellcmd($cmd));
    } catch (Exception $exception) {
      return $exception->getMessage();
    }
  }

  /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
  /* ### PUBLIC FUNCTIONS ### */
  /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */

  /* -- Version Start -- */

  /**
   * Return the installed/local exiftool version
   * @return string
   */
  public function getLocalExiftoolVersion()
  {
    return $this->get_Exiftool_Version(false);
  }

  /**
   * Return the provided exiftool version
   * @return string
   */
  public function getProvidedExiftoolVersion()
  {
    return $this->get_Exiftool_Version();
  }

  /**
   * Return the used exiftool version
   * @return array
   */
  public function getUsedExiftoolVersion()
  {
    return [
      ($this->useProvidedExiftool) ? "Provided" : "Local" => $this->get_Exiftool_Version($this->useProvidedExiftool)
    ];
  }

  /**
   * Return an array containing the user, the provided and the used exiftool version
   * @return array
   */
  public function getExiftoolVersionsInfo()
  {
    $versionsInfo = [
      "Local" => $this->getLocalExiftoolVersion(),
      "Provided" => $this->getProvidedExiftoolVersion(),
      "Used" => $this->getUsedExiftoolVersion()
    ];
    return $versionsInfo;
  }

  /* -- Version End -- */

  /* -- Schema Start -- */

  /**
   * @param $shortcut
   * @param $nameSpace
   * @param null $description
   * @return Schema
   */
  public function createSchema($shortcut, $nameSpace, $description = null)
  {
    $newSchema = new Schema($shortcut, $nameSpace, $description);
    return $newSchema;
  }

  /**
   * @param $schema
   * @return bool
   */
  public function addSchema($schema)
  {
    if ($schema instanceof Schema) {
      array_push($this->schemas, $schema);
      return true;
    }
    return false;
  }


  /**
   * @param $schema
   * @return bool
   */
  public function removeSchema($schema)
  {
    if (($key = array_search($schema, $this->schemas, true)) !== FALSE) {
      unset($this->schemas[$key]);
      return true;
    }
    return false;
  }

  /* -- Schema End -- */

  /**
   * Allows the user to execute any commands with the used exiftool version
   * @param $stringifyCmd
   * @return string
   */
  public function execute($stringifyCmd)
  {
    try {
      $cmd = ($this->useProvidedExiftool) ? $this->generate_Full_Exiftool_Path() . "exiftool " . $stringifyCmd : "exiftool " . $stringifyCmd . " 2>&1";
      return shell_exec($cmd);
    } catch (Exception $exception) {
      return $exception->getMessage();
    }
  }


  /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
  /* ### TASKERS SHORTCUTS FUNCTIONS ### */
  /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */

  /* EraserTasker */

  /**
   * @param string $targetedMetadata
   * @param null $excludedMetadata
   * @param bool $overwrite
   * @return array|null|string
   */
  public function remove($targetedMetadata = "all", $excludedMetadata = null, $overwrite = true)
  {
    return $this->eraser->remove($targetedMetadata, $excludedMetadata, $overwrite);
  }

  /* ReaderTasker */

  /**
   * @param string $selectedMetadata
   * @param null $excludedMetadata
   * @return array|null|string
   */
  public function read($selectedMetadata = "all", $excludedMetadata = null)
  {
    return $this->reader->read($selectedMetadata, $excludedMetadata);
  }

  /**
   * @param string $selectedMetadata
   * @param int $num
   * @param null $excludedMetadata
   * @return array|null|string
   */
  public function readWithPrefix($selectedMetadata = "all", $num = 0, $excludedMetadata = null)
  {
    return $this->reader->readWithPrefix($selectedMetadata, $num, $excludedMetadata);
  }

  /**
   * @param string $selectedMetadata
   * @param int $num
   * @param null $excludedMetadata
   * @return array|null|string
   */
  public function readByGroup($selectedMetadata = "all", $num = 0, $excludedMetadata = null)
  {
    return $this->reader()->readByGroup($selectedMetadata, $num, $excludedMetadata);
  }

  /* WriterTasker */

  /**
   * @param null $targetedMetadata
   * @param bool $replace
   * @param bool $overwrite
   * @return array|bool|null|string
   */
  public function write($targetedMetadata = null, $replace = true, $overwrite = true)
  {
    return $this->writer->write($targetedMetadata, $replace, $overwrite);
  }

  /**
   * @param null $jsonFilePath
   * @param bool $replace
   * @param bool $overwrite
   * @return array|bool|null|string
   */
  public function writeFromJsonFile($jsonFilePath = null, $replace = true, $overwrite = true)
  {
    return $this->writer->writeFromJsonFile($jsonFilePath, $replace, $overwrite);
  }

  /**
   * @param $json
   * @param bool $replace
   * @param bool $overwrite
   * @return array|bool|null|string
   */
  public function writeFromJson($json, $replace = true, $overwrite = true)
  {
    return $this->writer->writeFromJson($json, $replace, $overwrite);
  }

  /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
  /* ### GETTERS & SETTERS ### */
  /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */

  /**
   * @return ReaderTasker
   */
  public function reader()
  {
    return $this->reader;
  }

  /**
   * @return WriterTasker
   */
  public function writer()
  {
    return $this->writer;
  }

  /**
   * @return EraserTasker
   */
  public function eraser()
  {
    return $this->eraser;
  }

  /**
   * @return mixed
   */
  public function getFilePath()
  {
    return $this->filePath;
  }

  /**
   * @param mixed $filePath
   */
  public function setFilePath($filePath)
  {
    $this->filePath = $filePath;
    $this->initialize_Taskers();
  }

  /**
   * @param boolean $useProvidedExiftool
   */
  public function setUseProvidedExiftool($useProvidedExiftool)
  {
    $this->useProvidedExiftool = $useProvidedExiftool;
    $this->initialize_Taskers();
  }

  /**
   * @return mixed
   */
  public function getSchemas()
  {
    return $this->schemas;
  }

  /**
   * @param mixed $schemas
   */
  public function setSchemas($schemas)
  {
    $this->schemas = $schemas;
  }


}