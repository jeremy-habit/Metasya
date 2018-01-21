<?php

namespace MagicMonkey\Metasya\Tasker;

use MagicMonkey\Metasya\Inheritance\AbstractTasker;

/**
 * Class EraserTasker
 * @package MagicMonkey\Metasya\Tasker
 */
class EraserTasker extends AbstractTasker
{

  /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
  /* ### PRIVATE FUNCTIONS ### */
  /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */

  /**
   * Return the stringified targeted or excluded metadata tag.
   *
   * @param $targetedMetadata
   * @param bool $exclusion
   * @return string
   */
  private function stringify_Targeted_Metadata($targetedMetadata, $exclusion = false)
  {
    $stringifiedTargetedMetadata = "";
    $prefix = "-";
    $suffix = "=";
    if ($exclusion) {
      $suffix = null;
      $stringifiedTargetedMetadata = "-tagsFromFile " . $this->metadataHelper->getFilePath() . " ";
    }
    if (is_array($targetedMetadata)) {
      $targetedMetadataLength = count($targetedMetadata);
      $i = 0;
      foreach ($targetedMetadata as $metadataTag) {
        $stringifiedTargetedMetadata .= $prefix . $metadataTag . $suffix;
        if ($i++ !== $targetedMetadataLength) {
          $stringifiedTargetedMetadata .= " ";
        }
      }
    }
    return $stringifiedTargetedMetadata;
  }

  /**
   * Return the stringified command to execute with exiftool.
   *
   * @param $targetedMetadata
   * @param $excludedMetadata
   * @param $overwrite
   * @return string
   */
  private function make_Stringify_Cmd($targetedMetadata, $excludedMetadata, $overwrite)
  {
    $overwrite = ($overwrite) ? "-overwrite_original" : null;
    return $this->stringify_Targeted_Metadata($targetedMetadata) . " " . ((!empty($excludedMetadata)) ? $this->stringify_Targeted_Metadata($excludedMetadata, true) : null) . " " . $overwrite;
  }

  /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
  /* ### PUBLIC FUNCTIONS ### */
  /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */

  /**
   * Remove metadata tag.
   *
   * @param array $targetedMetadata
   * @param array $excludedMetadata
   * @param bool $overwrite
   * @return array|null|string
   */
  public function remove($targetedMetadata, $excludedMetadata = null, $overwrite = true)
  {
    $stringifiedCmd = $this->make_Stringify_Cmd($targetedMetadata, $excludedMetadata, $overwrite);
    return $this->execute($stringifiedCmd);
  }
}