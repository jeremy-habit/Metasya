<?php

namespace MagicMonkey\PHPMetadataManager;

use MagicMonkey\PHPMetadataManager\Tasker\ReaderTasker;
use MagicMonkey\PHPMetadataManager\Tasker\WriterTasker;
use MagicMonkey\PHPMetadataManager\Tasker\EraserTasker;

/**
 * Class MetadataManager
 */
class MetadataManager
{

  /**
   * @var string $filePath
   */
  private $filePath;

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
   * MetadataManager constructor.
   * @param $filePath
   */
  public function __construct($filePath)
  {
    $this->filePath = $filePath;
    $this->initialize_Taskers();
  }

  /**
   * Initialize taskers with the $filePath.
   * Useful when a new MetadataManger is created or when the $filePath is changed.
   */
  private function initialize_Taskers()
  {
    unset($this->reader);
    unset($this->writer);
    unset($this->eraser);
    $this->reader = new ReaderTasker($this->filePath);
    $this->writer = new WriterTasker($this->filePath, $this->reader);
    $this->eraser = new EraserTasker($this->filePath);
  }


  /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
  /* ### PUBLIC FUNCTIONS ### */
  /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */

  /* EraserTasker */

  public function remove($targetedMetadata = "all", $excludedMetadata = null, $overwrite = true)
  {
    return $this->eraser->remove($targetedMetadata, $excludedMetadata, $overwrite);
  }

  /* ReaderTasker */

  public function read($selectedMetadata = "all", $excludedMetadata = null)
  {
    return $this->reader->read($selectedMetadata, $excludedMetadata);
  }

  public function readWithPrefix($selectedMetadata = "all", $num = 0, $excludedMetadata = null)
  {
    return $this->reader->readWithPrefix($selectedMetadata, $num, $excludedMetadata);
  }

  public function readByGroup($selectedMetadata = "all", $num = 0, $excludedMetadata = null)
  {
    return $this->reader()->readByGroup($selectedMetadata, $num, $excludedMetadata);
  }

  /* WriterTasker */

  public function write($targetedMetadata = null, $replace = true, $overwrite = true)
  {
    return $this->writer->write($targetedMetadata, $replace, $overwrite);
  }

  public function writeFromJsonFile($jsonFilePath = null, $replace = true, $overwrite = true)
  {
    return $this->writer->writeFromJsonFile($jsonFilePath, $replace, $overwrite);
  }

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


}