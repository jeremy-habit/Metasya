<?php


namespace MagicMonkey\Metasya\Schema\Metadata\Type;

use MagicMonkey\Metasya\Schema\Metadata\Type\Inheritance\AbstractMetaType;

class MetaTypeAny extends AbstractMetaType
{

  public function isAccepted($value)
  {
    return true;
  }

}