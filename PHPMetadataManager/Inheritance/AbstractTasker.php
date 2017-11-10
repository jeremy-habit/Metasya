<?php

namespace PHPMetadataManager\Inheritance;

use PHPMetadataManager\InterfaceRepository\TaskerInterface;

/**
 * Class AbstractTasker
 * @package PHPMetadataManager\Inheritance
 */
abstract class AbstractTasker implements TaskerInterface
{
  /**
   * @var
   */
  protected $filePath;

  /**
   * AbstractTasker constructor.
   * @param $filePath
   */
  public function __construct($filePath)
  {
    $this->filePath = $filePath;
  }

  /**
   * Convert any object to array recursively.
   * @param $obj
   * @return array
   */
  protected function convert_Object_To_Array($obj)
  {
    if (is_object($obj)) $obj = (array)$obj;
    if (is_array($obj)) {
      $newArray = [];
      foreach ($obj as $key => $value) {
        $newArray[$key] = $this->convert_Object_To_Array($value);
      }
    } else $newArray = $obj;
    return $newArray;
  }

  /**
   * Prepare and return a string (targeting metadata) in order to add it in a exiftool command.
   * @param string $targetedMetadata
   * @param bool $exclusion
   * @return string
   */
  protected function stringify_Targeted_Metadata($targetedMetadata = "all", $exclusion = false)
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


  /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
  /* ### GETTERS & SETTERS ### */
  /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */


  /**
   * @return mixed
   */
  public function getFilePath()
  {
    return $this->filePath;
  }

  /**
   * @param mixed $filePath
   */
  public function setFilePath($filePath)
  {
    $this->filePath = $filePath;
  }


}
