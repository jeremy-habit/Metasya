<?php

namespace PHPMetadataManager\Tasker;

use Exception;
use PHPMetadataManager\Inheritance\AbstractTasker;

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
   * Return the result of a exiftool read command with some options.
   * @param string $stringifiedTargetedMetadata
   * @param string $stringifiedExcludedMetadata
   * @param null $groupOption
   * @return array|null|string
   */
  private function execute($stringifiedTargetedMetadata = "-all", $stringifiedExcludedMetadata = "", $groupOption = null)
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
    return $this->execute($this->stringify_Targeted_Metadata($selectedMetadata), $this->stringify_Targeted_Metadata($excludedMetadata, true));
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
    return $this->execute($this->stringify_Targeted_Metadata($selectedMetadata), $this->stringify_Targeted_Metadata($excludedMetadata, true), "-G" . $num);
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
    return $this->execute($this->stringify_Targeted_Metadata($selectedMetadata), $this->stringify_Targeted_Metadata($excludedMetadata, true), "-g" . $num);
  }

}