<?php

namespace MagicMonkey\Metasya\Schema;

use MagicMonkey\Metasya\ToolBox;

/**
 * Class SchemataManager
 * @package MagicMonkey\Metasya\Schema
 */
class SchemataManager
{

  /* const DEFAULT_SCHEMATA_FOLDER_PATH = "vendor" . ToolBox::DS . "magicmonkey" . ToolBox::DS . "metasya" . ToolBox::DS . "data" . ToolBox::DS . "defaultSchemata";*/
  const DEFAULT_SCHEMATA_FOLDER_PATH = "src" . ToolBox::DS . "Schema" . ToolBox::DS . "defaultSchemata";
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
  protected $userSchemataFolderPath;

  /**
   * private constructor
   */
  private function __construct()
  {
    $this->schemata = array();
    $this->toolbox = ToolBox::getInstance();
    $this->setUserSchemataFolderPath(self::USER_SCHEMATA_FOLDER_PATH);
    $this->synchronize_Default_Schemata();
    $this->synchronize_User_Schemata();
  }

  /**
   * Deactivate the cloning
   */
  private function __clone()
  {

  }

  public function isSchemaShortcut($string, $returnSchema = false)
  {
    foreach ($this->schemata as $schema) {
      if ($schema->getShortcut() == $string) {
        return $returnSchema ? $schema : true;
      }
    }
    return false;
  }

  public function getSchemaFromShortcut($schortcut)
  {
    return $this->isSchemaShortcut($schortcut, true);
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

  private function convert_Json_File_To_Schema_Object($folderPath)
  {
    if (is_dir($folderPath)) {
      $schemataJsonFiles = $this->toolbox->lsFiles($folderPath, array('json'));
      foreach ($schemataJsonFiles as $schemataJsonFile) {
        $json = $this->toolbox->extractJsonFromFile($schemataJsonFile);
        // test shortcut && nameSpace exists
        if (isset($json['shortcut']) && isset($json['namespace']) && isset($json["properties"]) && is_array($json["properties"])) {
          // creation of an object Schema
          $schema = new Schema(trim($json["shortcut"]), trim($json["namespace"]), isset($json["description"]) ? $json['description'] : "");
          // adding of properties
          foreach ($json["properties"] as $tag => $dataTag) {
            $schema->addProperty(new Property(
              $tag,
              isset($dataTag["value"]) ? $dataTag["value"] : "",
              isset($dataTag["namespace"]) ? $dataTag["namespace"] : $json["namespace"]));
          }
          // adding of this objet into the list of Schemata
          array_push($this->schemata, $schema);
        }
      }
    }
  }

  private function synchronize_Default_Schemata()
  {
    $this->convert_Json_File_To_Schema_Object(self::DEFAULT_SCHEMATA_FOLDER_PATH);
  }


  private function synchronize_User_Schemata()
  {
    $this->convert_Json_File_To_Schema_Object(self::USER_SCHEMATA_FOLDER_PATH);
  }


  /**
   * @param string $oldSchemataFolderPath
   * @param bool $removeDefaultFolder
   */
  private function change_User_Schemata_Folder($oldSchemataFolderPath, $removeDefaultFolder = false)
  {
    // creation of the new folder(s)
    if (!is_dir($this->userSchemataFolderPath)) {
      mkdir($this->userSchemataFolderPath, 0777, true);
    }
    // if old folders exists
    if (is_dir($oldSchemataFolderPath)) {
      // move schemata in the nex folder(s)
      $schemataJsonFiles = $this->toolbox->lsFiles(self::USER_SCHEMATA_FOLDER_PATH, array('json'));
      if ($removeDefaultFolder) {
        foreach ($schemataJsonFiles as $schemataJsonFile) {
          rename($schemataJsonFile, $this->userSchemataFolderPath . ToolBox::DS . basename($schemataJsonFile));
        }
        $this->toolbox->recursiveRmdir($oldSchemataFolderPath);
      } else {
        foreach ($schemataJsonFiles as $schemataJsonFile) {
          copy($schemataJsonFile, $this->userSchemataFolderPath . ToolBox::DS . basename($schemataJsonFile));
        }
      }
    }
  }

  /**
   * @return mixed
   */
  public function getUserSchemataFolderPath()
  {
    return $this->userSchemataFolderPath;
  }

  /**
   * @param mixed $userSchemataFolderPath
   * @param bool $removeDefaultOlder
   */
  public function setUserSchemataFolderPath($userSchemataFolderPath, $removeDefaultOlder = false)
  {
    $oldSchemataFolderPath = $this->userSchemataFolderPath;
    $this->userSchemataFolderPath = $userSchemataFolderPath;
    $this->change_User_Schemata_Folder($oldSchemataFolderPath, $removeDefaultOlder);
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
