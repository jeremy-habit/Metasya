<?php

namespace MagicMonkey\Metasya\Tasker;

use MagicMonkey\Metasya\Inheritance\AbstractTasker;
use MagicMonkey\Metasya\Schema\Metadata\Metadata;
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
    $stringifiedTargetedMetadata = "";
    $prefix = "-";
    if ($exclusion) {
      $prefix = "--";
    }
    if (is_array($targetedMetadata)) {
      $targetedMetadataLength = count($targetedMetadata);
      $i = 0;
      foreach ($targetedMetadata as $metadataTag) {


        switch ($metadataTag) {
          case $metadataTag instanceOf Schema:
            if ($metadataTag->isValid()) {
              $stringifiedTargetedMetadata .= $this->stringify_Targeted_Metadata($metadataTag->buildTargetedMetadata(), $exclusion);
            }
            break;
          case ($schemaFromShortcut = $this->schemataManager->getSchemaFromShortcut($metadataTag)) instanceof Schema:
            if ($schemaFromShortcut->isValid()) {
              $stringifiedTargetedMetadata .= $this->stringify_Targeted_Metadata($schemaFromShortcut->buildTargetedMetadata(), $exclusion);
            }
            break;
          case $metadataTag instanceOf Metadata:
            $stringifiedTargetedMetadata .= $prefix . $metadataTag->__toString();
            break;
          case ($metadataFromShortcut = $this->schemataManager->getMetadataFromShortcut($metadataTag)) instanceOf Metadata:
            $stringifiedTargetedMetadata .= $prefix . $metadataFromShortcut->__toString();
            break;
          default:
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
   * @param array $selectedMetadata
   * @param array $excludedMetadata
   * @return array|null|string
   */
  public function read($selectedMetadata = null, $excludedMetadata = null)
  {
    $stringifiedCmd = $this->make_Stringify_Cmd($selectedMetadata, $excludedMetadata);
    return $this->execute($stringifiedCmd, true);
  }

  /**
   * Return metadata as array with the group option -G[$num...] : Print group name for each tag.
   * @param array $selectedMetadata
   * @param int $num
   * @param array $excludedMetadata
   * @return array|null|string
   */
  public function readWithPrefix($selectedMetadata = null, $num = 0, $excludedMetadata = null)
  {
    $stringifiedCmd = $this->make_Stringify_Cmd($selectedMetadata, $excludedMetadata, "-G" . $num);
    return $this->execute($stringifiedCmd, true);
  }

  /**
   * Return metadata as array with the group option -g[$num...] : Organize output by tag group.
   * @param array $selectedMetadata
   * @param int $num
   * @param array $excludedMetadata
   * @return array|null|string
   */
  public function readByGroup($selectedMetadata = null, $num = 0, $excludedMetadata = null)
  {
    $stringifiedCmd = $this->make_Stringify_Cmd($selectedMetadata, $excludedMetadata, "-g" . $num);
    return $this->execute($stringifiedCmd, true);
  }

}