<?php

namespace PHPMetadataManager;

use PHPMetadataManager\Tasker\ReaderTasker;
use PHPMetadataManager\Tasker\WriterTasker;
use PHPMetadataManager\Tasker\EraserTasker;

/**
 * Class MetadataManager
 */
class MetadataManager
{

  /**
   * @var $instance
   */
  private static $instance;

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
  private function __construct($filePath)
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
  /* ### GETTERS & SETTERS ### */
  /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */

  /**
   * Method to reach the UNIQUE instance of the class.
   * @return MetadataManager
   */
  public static function getInstance($filePath)
  {
    if (!(self::$instance instanceof self)) {
      self::$instance = new self($filePath);
    }
    return self::$instance;
  }

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