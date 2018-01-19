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
   *
   * @var static $instance
   */
  private static $instance;

  /**
   * @var Schema[] $schemata
   */
  protected $schemata;

  /**
   * @var ToolBox $toolbox
   */
  protected $toolbox;

  /**
   * @var String $userSchemataFolderPath
   */
  protected $userSchemataFolderPath;

  /**
   * SchemataManager constructor.
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

  public function deploy($schema)
  {
    /* TODO : rework ! */
    if ($schema instanceof Schema) {
      if (!$this->isSchemaShortcut($schema->getShortcut())) {
        // creation du nouveau json
        $properties = array();
        foreach ($schema->getProperties() as $property) {
          $properties[$property->getTagName()] = array(
            "value" => $property->getValue(),
            "namespace" => $property->getNamespace()
          );
        }
        $jsonSchema = array(
          "shortcut" => $schema->getShortcut(),
          "description" => $schema->getDescription(),
          "namespace" => $schema->getNamespace(),
          "properties" => $properties
        );
        $fp = fopen($this->userSchemataFolderPath . ToolBox::DS . $schema->getShortcut() . '.json', 'w');
        fwrite($fp, json_encode($jsonSchema));
        fclose($fp);
        array_push($this->schemata, $schema);
      } else {
        return "The shortcut " . $schema->getShortcut() . " is already used by an other one schema !";
      }
    }
    return false;
  }


  /**
   * @param $string
   * @param bool $returnSchema
   * @return bool|Schema
   */
  public function isSchemaShortcut($string, $returnSchema = false)
  {
    foreach ($this->schemata as $schema) {
      if ($schema->getShortcut() == $string) {
        return $returnSchema ? $schema : true;
      }
    }
    return false;
  }


  /**
   * @param $shortcut
   * @return bool|Schema
   */
  public function getSchemaFromShortcut($shortcut)
  {
    return $this->isSchemaShortcut($shortcut, true);
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

        $schemaAsArray = $this->toolbox->getJsonFileAsArray($schemataJsonFile);

        // creation of an empty object Schema
        $schema = $this->createSchemaFromArray($schemaAsArray);

        /*$schema = new Schema();
        $validationResult = $this->isValidSchema($schemaAsArray);
        $schema->setErrors($validationResult['errors']);
        $schema->setIsValid($validationResult['valid']);
        if ($schema->isValid()) {
          // setting description and shortcut

          // adding of metadata
          foreach ($schemaAsArray['metadata'] as $metadataGroup) {
            foreach ($metadataGroup["properties"] as $tagName => $content) {
              $schema->addMetadata(new Metadata($tagName, $metadataGroup['namespace'], $content['shortcut']));
            }
          }
        }*/
        // adding of this objet into the list of Schemata
        array_push($this->schemata, $schema);
      }
    }
  }

  /**
   * Allows to test if a schema as array is valid or not
   *
   * @param $schemaAsArray
   * @return Schema
   */
  private function createSchemaFromArray($schemaAsArray)
  {
    $newSchema = new Schema();
    $newSchema->setSchemaAsArray($schemaAsArray);
    $newSchema->setDescription($schemaAsArray['description']);
    // shortcut required
    if (!isset($schemaAsArray['shortcut'])) {
      $newSchema->setIsValid(false);
      $newSchema->addError("Schema's shortcut is missing.");
    } else {
      $newSchema->setShortcut($schemaAsArray['shortcut']);
    }
    // metadata required
    if (!isset($schemaAsArray['metadata']) || !is_array($schemaAsArray["metadata"])) {
      $newSchema->setIsValid(false);
      $newSchema->addError("Schema's metadata list is missing or is not an array.");
    } else {
      foreach ($schemaAsArray['metadata'] as $index => $metadataGroup) {
        // namespace required
        if (!isset($metadataGroup['namespace'])) {
          $newSchema->setIsValid(false);
          $newSchema->addError("The namespace of the metadata's group at the index " . $index . " is missing.");
        } else {
          // properties required
          if (!isset($metadataGroup['properties']) || !is_array($metadataGroup['properties'])) {
            $newSchema->setIsValid(false);
            $newSchema->addError("The properties list of the metadata's group at the index " . $index . " is missing or is not an array.");
          } else {
            foreach ($metadataGroup['properties'] as $tagName => $content) {
              // shortcut required
              if (!isset($content['shortcut'])) {
                $newSchema->setIsValid(false);
                $newSchema->addError("The shortcut of the property " . $tagName . " of the metadata's group at the index " . $index . " is missing.");
              } else {
                $newSchema->addMetadata(new Metadata($tagName, $metadataGroup['namespace'], $content['shortcut']));
              }
            }
          }
        }
      }
    }
    return $newSchema;


    /* $result = array('valid' => true, 'errors' => array());
     // shortcut required
     if (!isset($schemaAsArray['shortcut'])) {
       $result['valid'] = false;
       array_push($result['message'], "Schema's shortcut is missing.");
     }
     // metadata required
     if (!isset($schemaAsArray['metadata']) || !is_array($schemaAsArray["metadata"])) {
       $result['valid'] = false;
       array_push($result['message'], "Schema's metadata list is missing or is not an array.");
     } else {
       foreach ($schemaAsArray['metadata'] as $index => $metadataGroup) {
         // namespace required
         if (!isset($metadataGroup['namespace'])) {
           $result['valid'] = false;
           array_push($result['message'], "The namespace of the metadata's group at the index " . $index . " is missing.");
         }
         // properties required
         if (!isset($metadataGroup['properties']) || !is_array($metadataGroup['properties'])) {
           $result['valid'] = false;
           array_push($result['message'], "The properties list of the metadata's group at the index " . $index . " is missing or is not an array.");
         } else {
           foreach ($metadataGroup['properties'] as $tagName => $content) {
             // shortcut required
             if (!isset($content['shortcut'])) {
               $result['valid'] = false;
               array_push($result['message'], "The shortcut of the property " . $tagName . " of the metadata's group at the index " . $index . " is missing.");
             }
           }
         }
       }
     }

    return $result;
    */

  }

  private
  function synchronize_Default_Schemata()
  {
    $this->convert_Json_File_To_Schema_Object(self::DEFAULT_SCHEMATA_FOLDER_PATH);
  }


  private
  function synchronize_User_Schemata()
  {
    $this->convert_Json_File_To_Schema_Object(self::USER_SCHEMATA_FOLDER_PATH);
  }


  /**
   * @param string $oldSchemataFolderPath
   * @param bool $removeDefaultFolder
   */
  private
  function change_User_Schemata_Folder($oldSchemataFolderPath, $removeDefaultFolder = false)
  {
    // creation of the new folder(s)
    if (!is_dir($this->userSchemataFolderPath)) {
      mkdir($this->userSchemataFolderPath, 0777, true);
    }
    // if old folders exists
    if (is_dir($oldSchemataFolderPath)) {
      // move schemata in the nex folder(s)
      $schemataJsonFiles = $this->toolbox->lsFiles($oldSchemataFolderPath, array('json'));
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
  public
  function getUserSchemataFolderPath()
  {
    return $this->userSchemataFolderPath;
  }

  /**
   * @param mixed $userSchemataFolderPath
   * @param bool $removeDefaultOlder
   */
  public
  function setUserSchemataFolderPath($userSchemataFolderPath, $removeDefaultOlder = false)
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


}
