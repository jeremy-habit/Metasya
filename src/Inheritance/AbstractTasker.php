<?php

namespace MagicMonkey\Metasya\Inheritance;

use MagicMonkey\Metasya\MetadataHelper;
use MagicMonkey\Metasya\Schema\SchemataManager;
use MagicMonkey\Metasya\ToolBox;

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
   * @var $this
   */
  protected $schemataManager;


  /**
   * AbstractTasker constructor.
   * @param $metadataHelper
   */
  public function __construct($metadataHelper)
  {
    $this->metadataHelper = $metadataHelper;
    $this->toolBox = ToolBox::getInstance();
    $this->schemataManager = SchemataManager::getInstance();
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
    $jsonOption = $jsonOutput ? "-json " : null;
    $errorsOption = $this->metadataHelper->getDisplayErrors() ? " 2>&1" : null;
    $cmd = $this->toolBox->trimMultipleWhitespaces($this->metadataHelper->getExiftoolPath() . "exiftool " . $jsonOption . $stringifiedCmd . " " . $this->metadataHelper->getFilePath() . $errorsOption);
    $cmdResult = shell_exec($cmd);
    if ($this->toolBox->isJson($cmdResult)) {
      return $this->toolBox->convertObjectToArray(json_decode($cmdResult)[0]);
    }
    return $cmdResult;
  }


}
