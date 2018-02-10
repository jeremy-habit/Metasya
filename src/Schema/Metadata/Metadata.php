<?php

namespace MagicMonkey\Metasya\Schema\Metadata;

use MagicMonkey\Metasya\Schema\Metadata\Type\Inheritance\AbstractMetaType;
use MagicMonkey\Metasya\Schema\Metadata\Type\MetaTypeAny;


/**
 * Class Metadata
 * @package MagicMonkey\Metasya\Schema
 */
class Metadata
{

  /**
   * @var string $tagName
   */
  private $tagName;

  /**
   * @var string $shortcut
   */
  private $shortcut;

  /**
   * @var string $namespace
   */
  private $namespace;

  /**
   * @var AbstractMetaType $type
   */
  private $type;

  /**
   * Metadata constructor.
   *
   * @param $tagName
   * @param null $namespace
   * @param $shortcut
   * @param $type
   */
  public function __construct($tagName, $namespace, $shortcut, $type = null)
  {
    $this->tagName = $tagName;
    $this->namespace = $namespace;
    $this->shortcut = $shortcut;
    $this->type = $type;
    if ($this->type == null) {
      $this->type = new MetaTypeAny();
    }
  }

  /**
   * @return string
   */
  public function __toString()
  {
    return $this->namespace . ":" . $this->tagName;
  }

  /**
   * @return mixed
   */
  public function getTagName()
  {
    return $this->tagName;
  }

  /**
   * @return mixed
   */
  public function getNamespace()
  {
    return $this->namespace;
  }

  /**
   * @return mixed
   */
  public function getShortcut()
  {
    return $this->shortcut;
  }

  /**
   * @return AbstractMetaType
   */
  public function getType()
  {
    return $this->type;
  }

}