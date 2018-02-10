<?php

namespace MagicMonkey\Metasya\Schema\Metadata;

use MagicMonkey\Metasya\Schema\Metadata\Type\Interfaces\MetaTypeInterface;
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
   * @var string $description
   */
  private $description;

  /**
   * @var MetaTypeInterface $type
   */
  private $type;

  /**
   * Metadata constructor.
   *
   * @param $tagName
   * @param null $namespace
   * @param $shortcut
   * @param null $description
   * @param $type
   */
  public function __construct($tagName, $namespace, $shortcut, $description = null, $type = null)
  {
    $this->tagName = $tagName;
    $this->namespace = $namespace;
    $this->shortcut = $shortcut;
    $this->description = $description;
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
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }

  /**
   * @return MetaTypeInterface|MetaTypeAny
   */
  public function getType()
  {
    return $this->type;
  }

}