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
   * @var string $namespace
   */
  private $namespace;

  /**
   * @var string $description
   */
  private $description;

  /**
   * @var Property[] $properties
   */
  private $properties;

  /**
   * Schema constructor.
   * @param $shortcut
   * @param $namespace
   * @param $description
   */
  public function __construct($shortcut, $namespace, $description = null)
  {
    $this->shortcut = trim($shortcut);
    $this->namespace = $namespace;
    $this->description = $description;
    $this->properties = array();
  }

  public function buildTargetedMetadata()
  {
    $targetedMetadata = array();
    foreach ($this->properties as $property) {
      if (!empty(trim($property->getNamespace()))) {
        $metadataTag = $property->getNamespace() . ":" . $property->getTagName();
      } else {
        $metadataTag = $this->getNamespace() . ":" . $property->getTagName();
      }
      array_push($targetedMetadata, $metadataTag);
    }
    return $targetedMetadata;
  }

  /**
   * @param $property
   * @return bool
   */
  public function addProperty($property)
  {
    if ($property instanceof Property) {
      array_push($this->properties, $property);
      return true;
    }
    return false;
  }

  /**
   * @param $property
   * @return bool
   */
  public function removeProperty($property)
  {
    if ($property instanceof Property) {
      if (($key = array_search($property, $this->properties, true)) !== FALSE) {
        unset($this->properties[$key]);
        return true;
      }
    } else {
      unset($this->properties[$property]);
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
  public
  function getNamespace()
  {
    return $this->namespace;
  }

  /**
   * @return null
   */
  public
  function getDescription()
  {
    return $this->description;
  }


  /**
   * @return array|Property[]
   */
  public function getProperties()
  {
    return $this->properties;
  }

  /**
   * @return mixed
   */
  public
  function getShortcut()
  {
    return $this->shortcut;
  }


}