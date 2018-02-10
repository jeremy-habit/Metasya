<?php

namespace MagicMonkey\Metasya\Schema\Metadata\Type\Inheritance;

use MagicMonkey\Metasya\Schema\Metadata\Type\Interfaces\MetaTypeInterface;

abstract class AbstractMetaType implements MetaTypeInterface
{

  /*
  public function getValue()
  {
    return $this->value;
  }

  public function setValue($value)
  {
    if (!$this->acceptValue($value)) {
      return false;
    }
    $this->value = $value;
    return true;
  }*/

}