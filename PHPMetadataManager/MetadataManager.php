<?php

namespace PHPMetadataManager;

use PHPMetadataManager\Tasker\ReaderTasker;
use PHPMetadataManager\Tasker\WriterTasker;

/**
 * Class MetadataManager
 */
class MetadataManager
{

  /**
   * @var
   */
  private static $instance;

  /**
   * @var
   */
  private $filePath;

  /**
   * @var ReaderTasker
   */
  private $reader;

  /**
   * @var WriterTasker
   */
  private $writer;


  /**
   * MetadataManager constructor.
   * @param $filePath
   */
  private function __construct($filePath)
  {
    $this->filePath = $filePath;
    $this->reader = new ReaderTasker($this->filePath);
    $this->writer = new WriterTasker($this->filePath);
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
    $this->reader->setFilePath($filePath);
    $this->writer->setFilePath($filePath);
  }


}