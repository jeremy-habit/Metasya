<?php

/**
 * Class MetadataManager
 */
class MetadataManager
{

  /**
   * @var
   */
  private $filePath;

  /**
   * MetadataManager constructor.
   * @param $filePath
   */
  public function __construct($filePath)
  {
    $this->filePath = $filePath;
  }


  /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
  /* ### PRIVATE FUNCTIONS ### */
  /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */


  /**
   * Return the result of a exiftool read command with some options.
   * @param string $stringifiedTargetedMetadata
   * @param string $stringifiedExcludedMetadata
   * @param null $groupOption
   * @return array|null|string
   */
  private function execute_Read($stringifiedTargetedMetadata = "-all", $stringifiedExcludedMetadata = "", $groupOption = null)
  {
    try {
      if (file_exists($this->filePath)) {
        $cmdResult = json_decode(shell_exec("exiftool -j " . $stringifiedTargetedMetadata . " " . $stringifiedExcludedMetadata . " " . $groupOption . " " . $this->filePath))[0];
        return ($cmdResult == null) ? null : $this->convert_Object_To_Array($cmdResult);
      }
      return "Error : the file \" " . $this->filePath . " \" not found !";
    } catch (Exception $exception) {
      return $exception->getMessage();
    }
  }

  /**
   * Convert any object to array recursively.
   * @param $obj
   * @return array
   */
  private function convert_Object_To_Array($obj)
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
  private function stringify_Targeted_Metadata($targetedMetadata = "all", $exclusion = false)
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
  /* ### PUBLIC FUNCTIONS ### */
  /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */

  /**
   * Return metadata as array without group option.
   * @param string $selectedMetadata
   * @param string $excludedMetadata
   * @return array|null|string
   */
  public function read($selectedMetadata = "all", $excludedMetadata = "")
  {
    return $this->execute_Read($this->stringify_Targeted_Metadata($selectedMetadata), $this->stringify_Targeted_Metadata($excludedMetadata, true));
  }

  /**
   * Return metadata as array with the group option "-G[$num...] : Print group name for each tag.
   * @param int $num
   * @param string $selectedMetadata
   * @param string $excludedMetadata
   * @return array|null|string
   */
  public function readWithPrefix($num = 0, $selectedMetadata = "all", $excludedMetadata = "")
  {
    return $this->execute_Read($this->stringify_Targeted_Metadata($selectedMetadata), $this->stringify_Targeted_Metadata($excludedMetadata, true), "-G" . $num);
  }


  /**
   * Return metadata as array with the group option "-g[$num...] : Organize output by tag group.
   * @param int $num
   * @param string $selectedMetadata
   * @param string $excludedMetadata
   * @return array|null|string
   */
  public function readByGroup($num = 0, $selectedMetadata = "all", $excludedMetadata = "")
  {
    return $this->execute_Read($this->stringify_Targeted_Metadata($selectedMetadata), $this->stringify_Targeted_Metadata($excludedMetadata, true), "-g" . $num);
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