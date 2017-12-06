<?php

namespace MagicMonkey\Metasya\Schema;

use MagicMonkey\Metasya\ToolBox;

/**
 * Class SchemataManager
 * @package MagicMonkey\Metasya\Schema
 */
class SchemataManager
{

  /*const DEFAULT_SCHEMATA_FOLDER_PATH = "vendor" . ToolBox::DS . "magicmonkey" . ToolBox::DS . "metasya" . ToolBox::DS . "data" . ToolBox::DS . "defaultSchemata";*/
  const DEFAULT_SCHEMATA_FOLDER_PATH = "data" . ToolBox::DS . "defaultSchemata";
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
    $this->schemata = array();
    $this->toolbox = ToolBox::getInstance();
    $this->setSchemataFolderPath(self::USER_SCHEMATA_FOLDER_PATH);
    $this->synchronize_Default_Schemata();
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

  private function synchronize_Default_Schemata()
  {
    if (is_dir(self::DEFAULT_SCHEMATA_FOLDER_PATH)) {
      $schemataJsonFiles = $this->toolbox->lsFiles(self::DEFAULT_SCHEMATA_FOLDER_PATH, array('json'));
      foreach ($schemataJsonFiles as $schemataJsonFile) {
        $json = $this->toolbox->extractJsonFromFile($schemataJsonFile);
        // test shortcut && nameSpace exists
        if (isset($json['shortcut']) && isset($json['namespace']) && isset($json["properties"]) && is_array($json["properties"])) {
          // creation of an object Schema
          $schema = new Schema($json["shortcut"], $json["namespace"], isset($json["description"]) ? $json['description'] : "");
          // adding of properties
          foreach ($json["properties"] as $tag => $value) {
            $schema->addProperty(new Property($tag, $value, $json["namespace"]));
          }
          // adding of this objet into the list of Schemata
          array_push($this->schemata, $schema);
        }
      }
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
