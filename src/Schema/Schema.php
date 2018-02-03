<?php

namespace MagicMonkey\Metasya\Schema;


/**
 * Class Schema
 * @package MagicMonkey\Metasya\Schema
 */
class Schema
{

  /**
   * @var String $fileName
   */
  private $fileName;

  /**
   * @var String $shortcut
   */
  private $shortcut;

  /**
   * @var boolean $isValid
   */
  private $isValid;

  /**
   * @var String[] $errors
   */
  private $errors;

  /**
   * @var String $description
   */
  private $description;

  /**
   * @var Metadata[] $metadata
   */
  private $metadata;

  /**
   * @var array $schemaAsArray
   */
  private $schemaAsArray;

  /**
   * Schema constructor.
   * @param $shortcut
   * @param String $description
   */
  public function __construct($shortcut = "", $description = "")
  {
    $this->errors = array();
    $this->metadata = array();
    $this->shortcut = trim($shortcut);
    $this->description = $description;
    $this->isValid = $this->checkValidation();
  }

  /**
   * Allow to check the validation of the schema
   *
   * @return bool
   */
  private function checkValidation()
  {
    if (!empty($this->shortcut) && count($this->errors) == 0) {
      return true;
    }
    return false;
  }

  /**
   * Build an array of targeted metadata according to the metadata list
   * example : ["XMP-dc:Title", "System:FileSize"] ...
   *
   * @return array
   */
  public function buildTargetedMetadata()
  {
    $targetedMetadata = array();
    foreach ($this->metadata as $metadata) {
      array_push($targetedMetadata, $metadata->__toString());
    }
    return $targetedMetadata;
  }

  /**
   * Test if a string corresponds to a shortcut of a metadata of this schema
   *
   * @param $string
   * @param $returnMetadata
   * @return bool|Metadata|mixed
   */
  public function isMetadataFromShortcut($string, $returnMetadata)
  {
    if ($this->isValid) {
      foreach ($this->metadata as $metadata) {
        if ($metadata->getShortcut() == $string) {
          return $returnMetadata ? $metadata : true;
        }
      }
    }
    return false;
  }

  /**
   * Return a metadata object from its shortcut if it exists for this schema
   *
   * @param $string
   * @return null|Metadata|mixed
   */
  public function getMetadatFromShortcut($string)
  {
    return $this->isMetadataFromShortcut($string, true) ?: null;
  }

  /**
   * @param $metadata
   * @return bool
   */
  public function addMetadata($metadata)
  {
    if ($metadata instanceof Metadata) {
      array_push($this->metadata, $metadata);
      return true;
    }
    return false;
  }

  /**
   * @param $metadata
   * @return bool
   */
  public function removeMetadata($metadata)
  {
    if ($metadata instanceof Metadata) {
      if (($key = array_search($metadata, $this->metadata, true)) !== FALSE) {
        unset($this->metadata[$key]);
        return true;
      }
    } else {
      unset($this->metadata[$metadata]);
    }
    return false;
  }

  /*
    public function deploy()
    {
      return SchemataManager::getInstance()->deploy($this);
    }*/

  /**
   * @return String
   */
  public function getDescription()
  {
    return $this->description;
  }

  /**
   * @return array|Metadata[]
   */
  public function getMetadata()
  {
    return $this->metadata;
  }

  /**
   * @return array
   */
  public function getSchemaAsArray()
  {
    return $this->schemaAsArray;
  }

  /**
   * @param array $schemaAsArray
   */
  public function setSchemaAsArray($schemaAsArray)
  {
    $this->schemaAsArray = $schemaAsArray;
  }

  /**
   * @param String $shortcut
   */
  public function setShortcut($shortcut)
  {
    $this->shortcut = $shortcut;
    $this->setIsValid($this->checkValidation());
  }

  /**
   * @param String $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }

  /**
   * @param Metadata[] $metadata
   */
  public function setMetadata($metadata)
  {
    $this->metadata = $metadata;
  }

  /**
   * @return String
   */
  public function getShortcut()
  {
    return $this->shortcut;
  }

  /**
   * @return bool
   */
  public function isValid()
  {
    return $this->isValid;
  }

  /**
   * @param bool $isValid
   */
  private function setIsValid($isValid)
  {
    $this->isValid = $isValid;
  }

  /**
   * @return String[]
   */
  public function getErrors()
  {
    return $this->errors;
  }

  /**
   * @param String[] $errors
   */
  public function setErrors($errors)
  {
    $this->errors = $errors;
    $this->setIsValid($this->checkValidation());
  }

  /**
   * @param String $error
   */
  public function addError($error)
  {
    array_push($this->errors, $error);
    $this->setIsValid($this->checkValidation());
  }

  /**
   * @return String
   */
  public function getFileName()
  {
    return $this->fileName;
  }

  /**
   * @param String $fileName
   */
  public function setFileName($fileName)
  {
    $this->fileName = $fileName;
  }

}