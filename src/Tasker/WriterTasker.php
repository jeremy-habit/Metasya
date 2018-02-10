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

  /*private function untarget_Existing_Metadata($targetedMetadata)
  {
    $newTargetedMetadata = $targetedMetadata;
    $reader = new ReaderTasker($this->metadataHelper);
    foreach ($targetedMetadata as $metadataTag => $metadaValue) {
      $eventualMetadata = $this->schemataManager->getMetadataFromShortcut($metadataTag);
      if ($eventualMetadata != null) {

        $unprefixedCondition = array_key_exists(strtolower($metadataTag), array_change_key_case($reader->read(), CASE_LOWER));
        $prefixedCondition = array_key_exists(strtolower($metadataTag), array_change_key_case($reader->readWithPrefix(), CASE_LOWER));
        $highPrefixedCondition = array_key_exists(strtolower($metadataTag), array_change_key_case($reader->readWithPrefix([], 1), CASE_LOWER));

        if ($unprefixedCondition || $prefixedCondition || $highPrefixedCondition) {
          unset($newTargetedMetadata[$metadataTag]);
        }
      }
      return $newTargetedMetadata;
    }
  }*/

  /**
   * Return the stringified targeted metadata tag with its associated value.
   *
   * @param $targetedMetadata
   * @param $replace
   * @return string
   */
  private function stringify_Targeted_Metadata($targetedMetadata, $replace)
  {
    $stringifiedTargetedMetadata = "";
    $prefix = "-";
    $targetedMetadataLength = count($targetedMetadata);
    $i = 0;

    /* ##### VERSION 1 ##### */

    /* if (!$replace) {
      $reader = new ReaderTasker($this->metadataHelper);
      $unprefixedRead = array_change_key_case($reader->read(), CASE_LOWER);
      $prefixedRead = array_change_key_case($reader->readWithPrefix(), CASE_LOWER);
      $highPrefixedRead = array_change_key_case($reader->readWithPrefix([], 1), CASE_LOWER);
    }
    foreach ($targetedMetadata as $metadataTag => $metadataValue) {
      $eventualMetadata = $this->schemataManager->getMetadataFromShortcut($metadataTag);
      if ($eventualMetadata != null) {
        $metadataTag = $eventualMetadata->__toString();
      }
      if ($replace || (!array_key_exists(strtolower($metadataTag), $highPrefixedRead) && !array_key_exists(strtolower($metadataTag), $prefixedRead) && !array_key_exists(strtolower($metadataTag), $unprefixedRead))) {
        $stringifiedTargetedMetadata .= $prefix . $metadataTag . "=\"" . $metadataValue . "\"";
      }
      if ($i++ !== $targetedMetadataLength) {
        $stringifiedTargetedMetadata .= " ";
      }
    }*/


    /* ##### VERSION 2 ##### */
    foreach ($targetedMetadata as $metadataTag => $metadataValue) {
      $acceptedValue = true;
      $eventualMetadata = $this->schemataManager->getMetadataFromShortcut($metadataTag);
      if ($eventualMetadata != null) {
        $acceptedValue = $eventualMetadata->getType()->isAccepted($metadataValue);
        $metadataTag = $eventualMetadata->__toString();
      }
      if ($acceptedValue) {
        $stringifiedTargetedMetadata .= $prefix . $metadataTag . "=\"" . $metadataValue . "\"" . (!$replace ? " " . $prefix . "$metadataTag" . $prefix . "=" : null);
        if ($i++ !== $targetedMetadataLength) {
          $stringifiedTargetedMetadata .= " ";
        }
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
    /* if (!$replace) {
       $targetedMetadata = $this->untarget_Existing_Metadata($targetedMetadata);
     }*/
    if (!empty($targetedMetadata)) {
      return $this->stringify_Targeted_Metadata($targetedMetadata, $replace) . " " . $overwrite;
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
  public
  function writeFromJsonFile($jsonFilePath = null, $replace = true, $overwrite = true)
  {
    if (!empty($jsonFilePath) && file_exists($jsonFilePath)) {
      $stringifiedCmd = $this->make_Stringify_Cmd($this->toolBox->getJsonFileAsArray($jsonFilePath), $replace, $overwrite);
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
  public
  function writeFromJson($json, $replace = true, $overwrite = true)
  {
    if ($this->toolBox->isJson($json)) {
      return $this->write($this->toolBox->convertObjectToArray(json_decode($json)[0]), $replace, $overwrite);
    }
    return false;
  }

}