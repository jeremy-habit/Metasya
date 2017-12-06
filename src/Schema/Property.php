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
  private $nameSpace;

  /**
   * @var
   */
  private $value;

  /**
   * Property constructor.
   * @param $tagName
   * @param null $value
   * @param null $nameSpace
   */
  public function __construct($tagName, $value = null, $nameSpace = null)
  {
    $this->tagName = $tagName;
    $this->value = $value;
    $this->nameSpace = $nameSpace;
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
  public function getNameSpace()
  {
    return $this->nameSpace;
  }

  /**
   * @param mixed $nameSpace
   */
  public function setNameSpace($nameSpace)
  {
    $this->nameSpace = $nameSpace;
  }

}