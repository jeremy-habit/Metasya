<?php

namespace MagicMonkey\Metasya;

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

  const DS = DIRECTORY_SEPARATOR;
  const EXIFTOOL_PATH = "vendor" . self::DS . "magicmonkey" . self::DS . "metasya" . self::DS . "exiftool" . self::DS;

  /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
  /* ### ATTRIBUTES & CONSTRUCTORS ### */
  /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */

  /**
   * @var string $filePath
   */
  private $filePath;

  /**
   * @var boolean $useProvidedExiftool
   */
  private $useProvidedExiftool;

  /**
   * @var ReaderTasker $reader
   */
  private $reader;

  /**
   * @var WriterTasker $writer
   */
  private $writer;

  /**
   * @var EraserTasker $eraser
   */
  private $eraser;

  /**
   * MetadataHelper constructor.
   * @param string $filePath
   * @param bool $useProvidedExiftool
   */
  public function __construct($filePath, $useProvidedExiftool = true)
  {
    $this->filePath = $filePath;
    $this->useProvidedExiftool = $useProvidedExiftool;
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
    $exiftoolPath = ($this->useProvidedExiftool) ? self::EXIFTOOL_PATH . $this->determine_OS() : "";
    $this->reader = new ReaderTasker($this->filePath, $exiftoolPath);
    $this->writer = new WriterTasker($this->filePath, $exiftoolPath);
    $this->eraser = new EraserTasker($this->filePath, $exiftoolPath);
  }


  private function determine_OS()
  {
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
      return "windows" . self::DS;
    }
    return "linux" . self::DS;

  }

  /**
   * Return the version of exiftool (provided version or installed/local version)
   * @param bool $providedExiftoolVersion
   * @return string
   */
  private function get_Exiftool_Version($providedExiftoolVersion = true)
  {
    $cmd = ($providedExiftoolVersion) ? self::EXIFTOOL_PATH . "exiftool -ver" : "exiftool -ver";
    return shell_exec(escapeshellcmd($cmd));
  }

  /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
  /* ### PUBLIC FUNCTIONS ### */
  /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */

  /* Version */

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


  /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
  /* ### TASKERS FUNCTIONS ### */
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

}