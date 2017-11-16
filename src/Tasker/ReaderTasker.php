<?php

namespace src\Tasker;

use src\Inheritance\AbstractTasker;

/**
 * Class ReaderTasker
 * @package PHPMetadataManager\Tasker
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
        $stringifiedTargetedMetadata .= $prefix . $metadataTag;
        if ($i++ !== $targetedMetadataLength) {
          $stringifiedTargetedMetadata .= " ";
        }
      }
    } else {
      $targetedMetadata = trim($targetedMetadata);
      if (!empty($targetedMetadata)) {
        $stringifiedTargetedMetadata = $prefix . $targetedMetadata;
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