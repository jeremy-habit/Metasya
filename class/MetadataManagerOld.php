<?php

/**
 * Class MetadataManagerOld
 */
class MetadataManagerOld
{

  /**
   * @var
   */
  private $filePath;
  /**
   * @var
   */
  private $metadata;

  /**
   * MetadataManager constructor.
   * @param $filePath
   */
  public function __construct($filePath)
  {
    $this->setFilePath($filePath);
  }


  /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
  /* PRIVATE FUNCTIONS */
  /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */

  /**
   * Initialize metadata as array from $filePath via exiftool.
   */
  private function read()
  {
    if (file_exists($this->filePath)) {
      $this->metadata = (array)json_decode(shell_exec("exiftool -json -G " . $this->filePath))[0];
    } else {
      $this->metadata = null;
    }
  }

  /**
   * Return the value of a metadata if this last one exists
   * @param $metadataTag
   * @return null
   */
  private function getOne($metadataTag)
  {
    if ($this->exists($metadataTag)) {
      return $this->metadata[$metadataTag];
    }
    return null;
  }


  /**
   * Remove one metadata according to its tag name if this last one exists
   * @param $metadataTag
   * @return bool
   */
  private function removeOne($metadataTag)
  {
    if ($this->exists($metadataTag)) {
      unset($this->metadata[$metadataTag]);
      return true;
    }
    return false;
  }

  /**
   * Edit one metadata if this last one exists
   * @param $metadataTag
   * @param $newMetadataValue
   * @return bool
   */
  private function editOne($metadataTag, $newMetadataValue)
  {
    if ($this->exists($metadataTag)) {
      $this->metadata[$metadataTag] = $newMetadataValue;
      return true;
    }
    return false;
  }


  /**
   * Add one metadata. If this last one exists, its value is replaced by default. $replace = true
   * @param $newMetadataTag
   * @param $newMetadataValue
   * @param bool $replace
   */
  private function addOne($newMetadataTag, $newMetadataValue, $replace = true)
  {
    if ($replace || (!$replace && !$this->exists($newMetadataTag))) {
      $this->metadata[$newMetadataTag] = $newMetadataValue;
    }
  }

  /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
  /* PUBLIC FUNCTIONS */
  /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */

  /**
   * Edit one or several metadata inside the array $metadata
   * @param $targetedMetadata
   */

  public function edit($targetedMetadata)
  {
    foreach ($targetedMetadata as $metadataTag => $newMetadataValue) {
      $this->editOne($metadataTag, $newMetadataValue);
    }
  }

  /**
   * Remove one or several metadata inside the array $metadata
   * @param $targetedMetadata
   */
  public function remove($targetedMetadata)
  {
    if (is_array($targetedMetadata)) {
      foreach ($targetedMetadata as $metadataTag) {
        $this->removeOne($metadataTag);
      }
    } else {
      $this->removeOne($targetedMetadata);
    }
  }

  /**
   * Add one or several metadata inside the array $metadata.
   * @param $newMetadata
   * @param bool $replace
   */
  public function add($newMetadata, $replace = true)
  {
    foreach ($newMetadata as $newMetadataTag => $newMetadataValue) {
      $this->addOne($newMetadataTag, $newMetadataValue, $replace);
    }
  }

  /**
   * @param $targetedMetadata
   * @return array|null
   */
  public function get($targetedMetadata)
  {
    if (is_array($targetedMetadata)) {
      $lstMetadata = [];
      foreach ($targetedMetadata as $metadataTag) {
        $metadataValue = $this->getOne($metadataTag);
        array_push($lstMetadata, $metadataValue);
      }
      return $lstMetadata;
    }
    return $this->getOne($targetedMetadata);
  }

  public function reset()
  {
    if (file_exists($this->filePath)) {
      shell_exec("exiftool -all= " . $this->filePath);
      $this->read();
    }
  }

  /**
   * Test if a matadata exists or not. Return true of false
   * @param $metadataTag
   * @return bool
   */
  public function exists($metadataTag)
  {
    return !empty($metadataTag) && array_key_exists($metadataTag, $this->metadata);
  }

  /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
  /* GETTERS & SETTERS */
  /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */

  /**
   * @return mixed
   */
  public function getMetadata()
  {
    return $this->metadata;
  }

  /**
   * @param mixed $metadata
   */
  public function setMetadata($metadata)
  {
    if (is_array($metadata)) {
      $this->metadata = $metadata;
    }
  }

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
    $this->read();
  }


}