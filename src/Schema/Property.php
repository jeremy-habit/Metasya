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
   * @param mixed $tagName
   */
  public function setTagName($tagName)
  {
    $this->tagName = $tagName;
  }

  /**
   * @return mixed
   */
  public function getNamespace()
  {
    return $this->namespace;
  }

  /**
   * @param mixed $nameSpace
   */
  public function setNamespace($namespace)
  {
    $this->namespace = $namespace;
  }

  /**
   * @return mixed
   */
  public function getValue()
  {
    return $this->value;
  }

  /**
   * @param mixed $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }

}