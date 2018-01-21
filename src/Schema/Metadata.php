<?php

namespace MagicMonkey\Metasya\Schema;


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
   * Metadata constructor.
   *
   * @param $tagName
   * @param null $namespace
   * @param $shortcut
   */
  public function __construct($tagName, $namespace, $shortcut)
  {
    $this->tagName = $tagName;
    $this->namespace = $namespace;
    $this->shortcut = $shortcut;
  }

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

}