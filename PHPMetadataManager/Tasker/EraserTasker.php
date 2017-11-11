<?php

namespace PHPMetadataManager\Tasker;

use PHPMetadataManager\Inheritance\AbstractTasker;

/**
 * Class EraserTasker
 * @package PHPMetadataManager\Tasker
 */
class EraserTasker extends AbstractTasker
{

  /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
  /* ### PRIVATE FUNCTIONS ### */
  /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */

  private function stringify_Targeted_Metadata($targetedMetadata, $exclusion = false)
  {
    /*$stringifiedTargetedMetadata = "";
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
    return $stringifiedTargetedMetadata;*/
  }

  private function make_Stringify_Cmd($selectedMetadata, $excludedMetadata, $optionGroup = null)
  {
    /* return $this->stringify_Targeted_Metadata($selectedMetadata) . " " . $this->stringify_Targeted_Metadata($excludedMetadata, true) . " " . $optionGroup;*/
  }

  /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
  /* ### PUBLIC FUNCTIONS ### */
  /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
}