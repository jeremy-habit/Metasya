<?php


namespace MagicMonkey\Metasya\Schema\Metadata\Type;

use MagicMonkey\Metasya\Schema\Metadata\Type\Interfaces\MetaTypeInterface;

/**
 * Class MetaTypeAny
 * @package MagicMonkey\Metasya\Schema\Metadata\Type
 */
class MetaTypeAny implements MetaTypeInterface
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