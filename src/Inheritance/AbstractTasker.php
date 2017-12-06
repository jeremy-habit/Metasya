<?php

namespace MagicMonkey\Metasya\Inheritance;

use MagicMonkey\Metasya\MetadataHelper;
use MagicMonkey\Metasya\Schema\SchemataManager;

/**
 * Class AbstractTasker
 * @package MagicMonkey\Metasya\Inheritance
 */
abstract class AbstractTasker
{

  /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
  /* ### ATTRIBUTES & CONSTRUCTORS ### */
  /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */

  /**
   * @var MetadataHelper $metadataHelper
   */
  protected $metadataHelper;


  /**
   * @var mixed
   */
  protected $toolBox;


  /**
   * AbstractTasker constructor.
   * @param $metadataHelper
   */
  public function __construct($metadataHelper)
  {
    $this->metadataHelper = $metadataHelper;
    $this->toolBox = SchemataManager::getInstance();
  }

  /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
  /* ### FUNCTIONS ### */
  /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */

  /**
   * Execute a stringified command with exiftool and return its result.
   * @param $stringifiedCmd
   * @param bool $jsonOutput
   * @return array|null|string
   */
  protected function execute($stringifiedCmd, $jsonOutput = false)
  {
    $cmd = $this->toolBox->trimMultipleWhitespaces($this->metadataHelper->getExiftoolPath() . "exiftool " . (($jsonOutput) ? "-json " : null) . $stringifiedCmd . " " . $this->metadataHelper->getFilePath() . " 2>&1");
    $cmdResult = shell_exec($cmd);
    if ($this->toolBox->isJson($cmdResult)) {
      return $this->toolBox->convertObjectToArray(json_decode($cmdResult)[0]);
    }
    return $cmdResult;
  }




}
