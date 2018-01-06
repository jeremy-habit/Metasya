<?php

namespace MagicMonkey\Metasya\Schema;


/**
 * Class Property
 * @package MagicMonkey\Metasya\Schema
 */
class Property
{

  /**
   * @var
   */
  private $tagName;

  /**
   * @var
   */
  private $namespace;

  /**
   * @var
   */
  private $value;

  /**
   * Property constructor.
   * @param $tagName
   * @param null $value
   * @param null $namespace
   */
  public function __construct($tagName, $namespace = null, $value = null)
  {
    $this->tagName = $tagName;
    $this->value = $value;
    $this->namespace = $namespace;
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
  public function getValue()
  {
    return $this->value;
  }

}