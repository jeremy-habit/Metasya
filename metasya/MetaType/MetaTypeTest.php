<?php

use MagicMonkey\Metasya\Schema\Metadata\Type\Interfaces\MetaTypeInterface;

/**
 * Class MetaTypeTest
 */
class MetaTypeTest implements MetaTypeInterface
{

  /**
   * Return true of false according if the value is accepted or not.
   *
   * @param $value
   * @return bool
   */
  public function isAccepted($value)
  {
    return true;
  }
}