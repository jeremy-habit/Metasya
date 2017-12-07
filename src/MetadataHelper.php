<?php

namespace MagicMonkey\Metasya;

use Exception;
use MagicMonkey\Metasya\Schema\SchemataManager;
use MagicMonkey\Metasya\Tasker\EraserTasker;
use MagicMonkey\Metasya\Tasker\ReaderTasker;
use MagicMonkey\Metasya\Tasker\WriterTasker;

/**
 * Class MetadataHelper
 */
class MetadataHelper
{

  /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
  /* ### CONSTANTS ### */
  /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */

  const EXIFTOOL_PATH = "vendor" . ToolBox::DS . "magicmonkey" . ToolBox::DS . "metasya" . ToolBox::DS . "exiftool" . ToolBox::DS;

  /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
  /* ### ATTRIBUTES & CONSTRUCTORS ### */
  /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */

  /**
   * @var string $filePath
   */
  private $filePath;

  /**
   * @var
   */
  private $displayErrors;

  /**
   * @var
   */
  private $toolBox;

  /**
   * @var
   */
  private $schemataManager;

  /**
   * @var boolean $useProvidedExiftool
   */
  private $useProvidedExiftool;

  /**
   * @var
   */
  private $exiftoolPath;

  /**
   * MetadataHelper constructor.
   * @param string $filePath
   * @param bool $useProvidedExiftool
   * @param bool $displayErrors
   */
  public function __construct($filePath, $useProvidedExiftool = true, $displayErrors = true)
  {
    $this->displayErrors = $displayErrors;
    $this->toolBox = ToolBox::getInstance();
    $this->schemataManager = SchemataManager::getInstance();
    $this->filePath = $filePath;
    $this->useProvidedExiftool = $useProvidedExiftool;
    $this->set_Exiftool_Path();
  }

  /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
  /* ### PRIVATE FUNCTIONS ### */
  /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */

  /**
   * Set the exiftool path according the value of $useProvidedExiftool
   */
  public function set_Exiftool_Path()
  {
    $this->exiftoolPath = ($this->useProvidedExiftool) ? $this->generate_Full_Exiftool_Path() : "";
  }

  /**
   * Concatenates the EXIFTOOL_PATH const with the result of the function determine_OS in order generate the path to exiftool exe
   * @return string
   */
  private function generate_Full_Exiftool_Path()
  {
    return self::EXIFTOOL_PATH . $this->toolBox->determinesOS() . ToolBox::DS;
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
    $eraser = new EraserTasker($this);
    return $eraser->remove($targetedMetadata, $excludedMetadata, $overwrite);
  }

  /* ReaderTasker */

  /**
   * @param string $selectedMetadata
   * @param null $excludedMetadata
   * @return array|null|string
   */
  public function read($selectedMetadata = "all", $excludedMetadata = null)
  {
    $reader = new ReaderTasker($this);
    return $reader->read($selectedMetadata, $excludedMetadata);
  }

  /**
   * @param string $selectedMetadata
   * @param int $num
   * @param null $excludedMetadata
   * @return array|null|string
   */
  public function readWithPrefix($selectedMetadata = "all", $num = 0, $excludedMetadata = null)
  {
    $reader = new ReaderTasker($this);
    return $reader->readWithPrefix($selectedMetadata, $num, $excludedMetadata);
  }

  /**
   * @param string $selectedMetadata
   * @param int $num
   * @param null $excludedMetadata
   * @return array|null|string
   */
  public function readByGroup($selectedMetadata = "all", $num = 0, $excludedMetadata = null)
  {
    $reader = new ReaderTasker($this);
    return $reader->readByGroup($selectedMetadata, $num, $excludedMetadata);
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
    $writer = new WriterTasker($this);
    return $writer->write($targetedMetadata, $replace, $overwrite);
  }

  /**
   * @param null $jsonFilePath
   * @param bool $replace
   * @param bool $overwrite
   * @return array|bool|null|string
   */
  public function writeFromJsonFile($jsonFilePath = null, $replace = true, $overwrite = true)
  {
    $writer = new WriterTasker($this);
    return $writer->writeFromJsonFile($jsonFilePath, $replace, $overwrite);
  }

  /**
   * @param $json
   * @param bool $replace
   * @param bool $overwrite
   * @return array|bool|null|string
   */
  public function writeFromJson($json, $replace = true, $overwrite = true)
  {
    $writer = new WriterTasker($this);
    return $writer->writeFromJson($json, $replace, $overwrite);
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

  /**
   * @param mixed $filePath
   */
  public function setFilePath($filePath)
  {
    $this->filePath = $filePath;
  }

  /**
   * @return mixed
   */
  public function getSchemataManager()
  {
    return $this->schemataManager;
  }

  /**
   * @param mixed $schemataManager
   */
  public function setSchemataManager($schemataManager)
  {
    $this->schemataManager = $schemataManager;
  }

  /**
   * @return mixed
   */
  public function getToolBox()
  {
    return $this->toolBox;
  }

  /**
   * @param mixed $toolBox
   */
  public function setToolBox($toolBox)
  {
    $this->toolBox = $toolBox;
  }

  /**
   * @return mixed
   */
  public function getExiftoolPath()
  {
    return $this->exiftoolPath;
  }

  /**
   * @param boolean $useProvidedExiftool
   */
  public function setUseProvidedExiftool($useProvidedExiftool)
  {
    $this->useProvidedExiftool = $useProvidedExiftool;
    $this->set_Exiftool_Path();
  }

  /**
   * @param mixed $schemataFolderPath
   */
  public function updateSchemataFolderPath($schemataFolderPath)
  {
    $oldSchemataFolderPath = $this->schemataManager->getSchemataFolderPath();
    $this->schemataManager->setSchemataFolderPath($schemataFolderPath, $oldSchemataFolderPath);
  }

  /**
   * @return mixed
   */
  public function getDisplayErrors()
  {
    return $this->displayErrors;
  }

  /**
   * @param mixed $displayErrors
   */
  public function setDisplayErrors($displayErrors)
  {
    $this->displayErrors = $displayErrors;
  }


}