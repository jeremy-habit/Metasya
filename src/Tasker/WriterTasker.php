<?php

namespace MagicMonkey\Metasya\Tasker;

use MagicMonkey\Metasya\Inheritance\AbstractTasker;

/**
 * Class WriterTasker
 * @package MagicMonkey\Metasya\Tasker
 */
class WriterTasker extends AbstractTasker
{

  /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
  /* ### PRIVATE FUNCTIONS ### */
  /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */

  private function untarget_Existing_Metadata($targetedMetadata)
  {
    $newTargetedMetadata = $targetedMetadata;
    $reader = new ReaderTasker($this->metadataHelper);
    foreach ($targetedMetadata as $metadataTag => $metadaValue) {
      if (array_key_exists(strtolower($metadataTag), array_change_key_case($reader->read(), CASE_LOWER))) {
        unset($newTargetedMetadata[$metadataTag]);
      }
    }
    return $newTargetedMetadata;
  }

  /**
   * Return the stringified targeted metadata tag with its associated value.
   * @param $targetedMetadata
   * @return string
   */
  private function stringify_Targeted_Metadata($targetedMetadata)
  {
    $stringifiedTargetedMetadata = "";
    $prefix = "-";
    $targetedMetadataLength = count($targetedMetadata);
    $i = 0;
    foreach ($targetedMetadata as $metadataTag => $metadataValue) {
      $stringifiedTargetedMetadata .= $prefix . $metadataTag . "=\"" . $metadataValue . "\"";
      if ($i++ !== $targetedMetadataLength) {
        $stringifiedTargetedMetadata .= " ";
      }
    }
    return $stringifiedTargetedMetadata;
  }


  /**
   * Return the stringified command to execute with exiftool.
   * @param $targetedMetadata
   * @param $replace
   * @param $overwrite
   * @return null|string
   */
  private function make_Stringify_Cmd($targetedMetadata, $replace, $overwrite)
  {
    $overwrite = ($overwrite) ? "-overwrite_original" : null;
    if (!$replace) {
      $targetedMetadata = $this->untarget_Existing_Metadata($targetedMetadata);
    }
    if (!empty($targetedMetadata)) {
      return $this->stringify_Targeted_Metadata($targetedMetadata) . " " . $overwrite;
    }
    return null;
  }


  /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
  /* ### PUBLIC FUNCTIONS ### */
  /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */

  /**
   * Add or edits metadata tag value.
   * @param null $targetedMetadata
   * @param bool $replace
   * @param bool $overwrite
   * @return array|bool|null|string
   */
  public function write($targetedMetadata = null, $replace = true, $overwrite = true)
  {
    if (!empty($targetedMetadata) && is_array($targetedMetadata)) {
      $stringifiedCmd = $this->make_Stringify_Cmd($targetedMetadata, $replace, $overwrite);
      return ($stringifiedCmd != null) ? $this->execute($stringifiedCmd) : "Nothing to write ...";
    }
    return false;
  }

  /**
   * Add or edits metadata tag value from a json file.
   * @param null $jsonFilePath
   * @param bool $replace
   * @param bool $overwrite
   * @return array|bool|null|string
   */
  public function writeFromJsonFile($jsonFilePath = null, $replace = true, $overwrite = true)
  {
    if (!empty($jsonFilePath) && file_exists($jsonFilePath)) {
      $stringifiedCmd = $this->make_Stringify_Cmd($this->toolBox->extractJsonFromFile($jsonFilePath), $replace, $overwrite);
      return ($stringifiedCmd != null) ? $this->execute($stringifiedCmd) : "Nothing to write ...";
    }
    return false;
  }

  /**
   * Add or edits metadata tag value from json.
   * @param $json
   * @param bool $replace
   * @param bool $overwrite
   * @return array|bool|null|string
   */
  public function writeFromJson($json, $replace = true, $overwrite = true)
  {
    if ($this->toolBox->isJson($json)) {
      return $this->write($this->toolBox->convertObjectToArray(json_decode($json)[0]), $replace, $overwrite);
    }
    return false;
  }

}