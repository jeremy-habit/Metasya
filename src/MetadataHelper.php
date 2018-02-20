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
  const XMP_SIDECAR_FOLDER_PATH = "metasya" . ToolBox::DS . "Sidecar";

  /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
  /* ### ATTRIBUTES & CONSTRUCTORS ### */
  /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */

  /**
   * @var string $filePath
   */
  private $filePath;

  /**
   * @var boolean $displayErrors
   */
  private $displayErrors;

  /**
   * @var ToolBox $toolBox
   */
  private $toolBox;

  /**
   * @var SchemataManager $schemataManager
   */
  private $schemataManager;

  /**
   * @var boolean $useProvidedExiftool
   */
  private $useProvidedExiftool;

  /**
   * @var string $exiftoolPath
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
    $this->createSidecarFolder();
  }

  /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
  /* ### PRIVATE FUNCTIONS ### */
  /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */

  /**
   * Allows to create the folder for xmp sidecar files.
   */
  private function createSidecarFolder()
  {
    if (!is_dir(self::XMP_SIDECAR_FOLDER_PATH)) {
      mkdir(self::XMP_SIDECAR_FOLDER_PATH, 0777, true);
    }
  }

  /**
   * Concatenate the EXIFTOOL_PATH const with the result of the function determine_OS in order generate the path to exiftool exe
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


  /**
   * Set the exiftool path according the value of $useProvidedExiftool
   */
  public function set_Exiftool_Path()
  {
    $this->exiftoolPath = ($this->useProvidedExiftool) ? $this->generate_Full_Exiftool_Path() : "";
  }

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
   * @param $stringifiedCmd
   * @return string
   */
  public function execute($stringifiedCmd)
  {
    try {
      $cmd = (($this->useProvidedExiftool) ? $this->generate_Full_Exiftool_Path() . "exiftool " . $stringifiedCmd : "exiftool " . $stringifiedCmd) . " 2>&1";
      return shell_exec($cmd);
    } catch (Exception $exception) {
      return $exception->getMessage();
    }
  }


  public function generateXMPSideCar($outFileName = null)
  {
    try {
      if ($outFileName == null) {
        $outFileName = "\\" . $this->toolBox->getFileNameWithoutExtension($this->filePath);
      }
      $stringifiedCmd = "-overwrite_original -tagsfromfile " . $this->filePath . " " . self::XMP_SIDECAR_FOLDER_PATH . $outFileName . ".xmp";
      $cmd = (($this->useProvidedExiftool) ? $this->generate_Full_Exiftool_Path() . "exiftool " . $stringifiedCmd : "exiftool " . $stringifiedCmd) . " 2>&1";
    /*  var_dump($cmd);
      die();*/
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
   * @param array $targetedMetadata
   * @param array $excludedMetadata
   * @param bool $overwrite
   * @return array|null|string
   */
  public function remove($targetedMetadata, $excludedMetadata = null, $overwrite = true)
  {
    $eraser = new EraserTasker($this);
    return $eraser->remove($targetedMetadata, $excludedMetadata, $overwrite);
  }

  /* ReaderTasker */

  /**
   * @param array $selectedMetadata
   * @param array $excludedMetadata
   * @return array|null|string
   */
  public function read($selectedMetadata = null, $excludedMetadata = null)
  {
    $reader = new ReaderTasker($this);
    return $reader->read($selectedMetadata, $excludedMetadata);
  }

  /**
   * @param array $selectedMetadata
   * @param int $num
   * @param array $excludedMetadata
   * @return array|null|string
   */
  public function readWithPrefix($selectedMetadata = null, $num = 0, $excludedMetadata = null)
  {
    $reader = new ReaderTasker($this);
    return $reader->readWithPrefix($selectedMetadata, $num, $excludedMetadata);
  }

  /**
   * @param array $selectedMetadata
   * @param int $num
   * @param array $excludedMetadata
   * @return array|null|string
   */
  public function readByGroup($selectedMetadata = null, $num = 0, $excludedMetadata = null)
  {
    $reader = new ReaderTasker($this);
    return $reader->readByGroup($selectedMetadata, $num, $excludedMetadata);
  }

  /* WriterTasker */

  /**
   * @param array $targetedMetadata
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
   * @param string $jsonFilePath
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
   * @return SchemataManager
   */
  public function getSchemataManager()
  {
    return $this->schemataManager;
  }

  /**
   * @param SchemataManager $schemataManager
   */
  public function setSchemataManager($schemataManager)
  {
    $this->schemataManager = $schemataManager;
  }

  /**
   * @return $this|ToolBox
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