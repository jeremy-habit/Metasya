<?php

namespace MagicMonkey\Metasya\Schema;

use MagicMonkey\Metasya\ToolBox;

/**
 * Class SchemataManager
 * @package MagicMonkey\Metasya\Schema
 */
class SchemataManager
{

  const DEFAULT_SCHEMATA_FOLDER_PATH = "vendor" . ToolBox::DS . "magicmonkey" . ToolBox::DS . "metasya" . ToolBox::DS . "data" . ToolBox::DS . "defaultSchemata";
  const USER_SCHEMATA_FOLDER_PATH = "metasyaSchemata";

  /**
   * $instance is private in order to implement the singleton pattern
   */
  private static $instance;

  /**
   * @var
   */
  protected $schemata;

  /**
   * @var
   */
  protected $toolbox;

  /**
   * @var
   */
  protected $schemataFolderPath;

  /**
   * private constructor
   */
  private function __construct()
  {
    $this->setSchemataFolderPath(self::USER_SCHEMATA_FOLDER_PATH);
    $this->synchronyze_Default_Schemata();
    $this->toolbox = ToolBox::getInstance();
  }

  /**
   * Deactivate the cloning
   */
  private function __clone()
  {
  }

  /**
   * Method to reach the UNIQUE instance of the class.
   *
   * @return $this
   */
  public static function getInstance()
  {
    if (!(self::$instance instanceof self)) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  private function synchronyze_Default_Schemata()
  {
    // pour chaque schemata.json
    if (is_dir(self::DEFAULT_SCHEMATA_FOLDER_PATH)) {
      $schemataJsonFiles = $this->toolbox->lsFiles(self::DEFAULT_SCHEMATA_FOLDER_PATH, array('json'));
      /* foreach ($schemataJsonFiles as $schemataJsonFile) {
         // creation of an object Schema

         // adding of this objet into the list of Schemata
       }*/
    }
  }


  /**
   * @param string $oldSchemataFolderPath
   */
  private function synchronize_Schemata_Folder($oldSchemataFolderPath = "")
  {
    // creation of the new folder(s)
    if (!is_dir($this->schemataFolderPath)) {
      mkdir($this->schemataFolderPath, 0777, true);
    }
    // if old folders exists
    if (is_dir($oldSchemataFolderPath)) {
      // move schemata in the nex folder(s)
      $schemataJsonFiles = $this->toolbox->lsFiles(self::DEFAULT_SCHEMATA_FOLDER_PATH, array('json'));
      foreach ($schemataJsonFiles as $schemataJsonFile) {
        rename($schemataJsonFile, $this->schemataFolderPath . DS . basename($schemataJsonFile));
      }
      $this->toolbox->recursiveRmdir(dirname($oldSchemataFolderPath));
    }
  }

  /**
   * @return mixed
   */
  public function getSchemataFolderPath()
  {
    return $this->schemataFolderPath;
  }

  /**
   * @param mixed $schemataFolderPath
   * @param string $oldSchemataFolderPath
   */
  public function setSchemataFolderPath($schemataFolderPath, $oldSchemataFolderPath = "")
  {
    $this->schemataFolderPath = $schemataFolderPath;
    $this->synchronize_Schemata_Folder($oldSchemataFolderPath);
  }

  /**
   * @return mixed
   */
  public function getSchemata()
  {
    return $this->schemata;
  }

  /**
   * @param mixed $schemata
   */
  public function setSchemata($schemata)
  {
    $this->schemata = $schemata;
  }


}
