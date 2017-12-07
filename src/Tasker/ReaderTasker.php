<?php

namespace MagicMonkey\Metasya\Tasker;

use MagicMonkey\Metasya\Inheritance\AbstractTasker;
use MagicMonkey\Metasya\Schema\Schema;

/**
 * Class ReaderTasker
 * @package MagicMonkey\Metasya\Tasker
 */
class ReaderTasker extends AbstractTasker
{

  /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
  /* ### PRIVATE FUNCTIONS ### */
  /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */

  /**
   * Return the stringified selected or excluded metadata tag.
   * @param $targetedMetadata
   * @param bool $exclusion
   * @return string
   */
  private function stringify_Targeted_Metadata($targetedMetadata, $exclusion = false)
  {
    /* test if it's an instance of Schema */
    /* if ($targetedMetadata instanceof Schema) {
       $targetedMetadata = $targetedMetadata->buildTargetedMetadata();
     }*/

    /* test if it's a Schema's shortcut */
    /*   if ($targetedMetadata) {

       }*/

    $stringifiedTargetedMetadata = "";
    $prefix = "-";
    if ($exclusion) {
      $prefix = "--";
    }
    if (is_array($targetedMetadata)) {
      $targetedMetadataLength = count($targetedMetadata);
      $i = 0;
      foreach ($targetedMetadata as $metadataTag) {
        if ($metadataTag instanceOf Schema) {
          $stringifiedTargetedMetadata .= $this->stringify_Targeted_Metadata($metadataTag->buildTargetedMetadata(), $exclusion);
        } else if (($schema = $this->schemataManager->isSchemaShortcut($metadataTag, true)) instanceof Schema) {
          $stringifiedTargetedMetadata .= $this->stringify_Targeted_Metadata($schema->buildTargetedMetadata(), $exclusion);
        } else {
          $stringifiedTargetedMetadata .= $prefix . $metadataTag;
        }
        if ($i++ !== $targetedMetadataLength) {
          $stringifiedTargetedMetadata .= " ";
        }
      }
    }
    return $stringifiedTargetedMetadata;
  }

  /**
   * Return the stringified command to execute with exiftool.
   * @param $selectedMetadata
   * @param $excludedMetadata
   * @param null $optionGroup
   * @return string
   */
  private function make_Stringify_Cmd($selectedMetadata, $excludedMetadata, $optionGroup = null)
  {
    return $this->stringify_Targeted_Metadata($selectedMetadata) . " " . $this->stringify_Targeted_Metadata($excludedMetadata, true) . " " . $optionGroup;
  }

  /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
  /* ### PUBLIC FUNCTIONS ### */
  /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */

  /**
   * Return metadata as array without group option.
   * @param string $selectedMetadata
   * @param string $excludedMetadata
   * @return array|null|string
   */
  public function read($selectedMetadata = "all", $excludedMetadata = null)
  {
    $stringifiedCmd = $this->make_Stringify_Cmd($selectedMetadata, $excludedMetadata);
    return $this->execute($stringifiedCmd, true);
  }

  /**
   * Return metadata as array with the group option -G[$num...] : Print group name for each tag.
   * @param string $selectedMetadata
   * @param int $num
   * @param string $excludedMetadata
   * @return array|null|string
   */
  public function readWithPrefix($selectedMetadata = "all", $num = 0, $excludedMetadata = null)
  {
    $stringifiedCmd = $this->make_Stringify_Cmd($selectedMetadata, $excludedMetadata, "-G" . $num);
    return $this->execute($stringifiedCmd, true);
  }

  /**
   * Return metadata as array with the group option -g[$num...] : Organize output by tag group.
   * @param string $selectedMetadata
   * @param int $num
   * @param string $excludedMetadata
   * @return array|null|string
   */
  public function readByGroup($selectedMetadata = "all", $num = 0, $excludedMetadata = null)
  {
    $stringifiedCmd = $this->make_Stringify_Cmd($selectedMetadata, $excludedMetadata, "-g" . $num);
    return $this->execute($stringifiedCmd, true);
  }

}