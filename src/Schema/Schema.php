<?php

namespace MagicMonkey\Metasya\Schema;


/**
 * Class Schema
 * @package MagicMonkey\Metasya\Schema
 */
class Schema
{

  /**
   * @var string $shortcut
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
   * @var string $description
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
   * @param string $description
   * @param bool $isValid
   */
  public function __construct($shortcut = null, $description = "", $isValid = true)
  {
    $this->isValid = $isValid;
    $this->errors = array();
    $this->shortcut = trim($shortcut);
    $this->description = $description;
    $this->metadata = array();
  }

  public function buildTargetedMetadata()
  {
    $targetedMetadata = array();
    foreach ($this->metadata as $metadata) {
      array_push($targetedMetadata, $metadata->__toString());
    }
    return $targetedMetadata;
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

  public function deploy()
  {
    return SchemataManager::getInstance()->deploy($this);
  }

  /**
   * @return null
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
   * @param string $shortcut
   */
  public function setShortcut($shortcut)
  {
    $this->shortcut = $shortcut;
  }

  /**
   * @param string $description
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
   * @return mixed
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
  public function setIsValid($isValid)
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
  }

  /**
   * @param $error
   */
  public function addError($error)
  {
    array_push($this->errors, $error);
  }


}