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
   * @var SchemataManager $instance
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

  /*  public function deploy($schema)
    {
      if ($schema instanceof Schema) {
        if (!$this->isSchemaShortcut($schema->getShortcut())) {
          // creation du nouveau json
          $properties = array();
          foreach ($schema->getMetadata() as $metadata) {
            $properties[$property->getTagName()] = array(
              "value" => $property->getValue(),
              "namespace" => $property->getNamespace()
            );
          }
          $jsonSchema = array(
            "shortcut" => $schema->getShortcut(),
            "description" => $schema->getDescription(),
            "metadata" => $properties
          );
          $fp = fopen($this->userSchemataFolderPath . ToolBox::DS . $schema->getShortcut() . '-schema.json', 'w');
          fwrite($fp, json_encode($jsonSchema));
          fclose($fp);
          array_push($this->schemata, $schema);
        } else {
          return "The shortcut " . $schema->getShortcut() . " is already used by an other one schema !";
        }
      }
      return false;
    }*/

  /**
   * Test if a string corresponds to a shortcut of a schema
   *
   * @param $string
   * @param bool $returnSchema
   * @return bool|Schema|mixed
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
   * Return a schema object from its shortcut if it exists
   *
   * @param $shortcut
   * @return null|Schema|mixed
   */
  public function getSchemaFromShortcut($shortcut)
  {
    return $this->isSchemaShortcut($shortcut, true) ?: null;
  }

  /**
   * Test if a string corresponds to a shortcut of a metadata for every shchema
   *
   * @param $string
   * @param bool $returnMetadata
   * @return bool|Metadata|mixed
   */
  public function isMetadataShortcut($string, $returnMetadata = false)
  {
    foreach ($this->schemata as $schema) {
      $response = $schema->isMetadataFromShortcut($string, $returnMetadata);
      if ($response) {
        return $response;
      }
    }
    return $returnMetadata ? null : false;
  }

  /**
   * Return a metadata object from its shortcut if it exists in one schema
   *
   * @param $shortcut
   * @return null|Metadata|mixed
   */
  public function getMetadataFromShortcut($shortcut)
  {
    return $this->isMetadataShortcut($shortcut, true) ?: null;
  }


  /**
   * Method to reach the UNIQUE instance of the class.
   *
   * @return SchemataManager
   */
  public static function getInstance()
  {
    if (!(self::$instance instanceof self)) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  /**
   * Allow to create schema object from json file
   *
   * @param $folderPath
   */
  private function convert_Json_File_To_Schema_Object($folderPath)
  {
    if (is_dir($folderPath)) {
      $schemataJsonFiles = $this->toolbox->lsFiles($folderPath, array('json'));
      foreach ($schemataJsonFiles as $schemataJsonFile) {
        if ($this->toolbox->endsWith(basename($schemataJsonFile), "-schema.json")) {
          $schemaAsArray = $this->toolbox->getJsonFileAsArray($schemataJsonFile);

          // creation of an empty object Schema
          $schema = $this->createSchemaFromArray($schemaAsArray, basename($schemataJsonFile));

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
  }

  /**
   * Used to create a schema object from json file
   *
   * @param $schemaAsArray
   * @param $jsonFileBasename
   * @return Schema
   */
  private function createSchemaFromArray($schemaAsArray, $jsonFileBasename)
  {
    $newSchema = new Schema();
    $newSchema->setSchemaAsArray($schemaAsArray);
    $newSchema->setDescription($schemaAsArray['description']);
    $newSchema->setFileName($jsonFileBasename);
    // shortcut required
    if (!isset($schemaAsArray['shortcut'])) {
      $newSchema->addError("Schema's shortcut is missing.");
    } else {
      $newSchema->setShortcut($schemaAsArray['shortcut']);
    }
    // metadata required
    if (!isset($schemaAsArray['metadata']) || !is_array($schemaAsArray["metadata"])) {
      $newSchema->addError("Schema's metadata list is missing or is not an array.");
    } else {
      foreach ($schemaAsArray['metadata'] as $index => $metadataGroup) {
        // namespace required
        if (!isset($metadataGroup['namespace'])) {
          $newSchema->addError("The namespace of the metadata's group at the index " . $index . " is missing.");
        } else {
          // list required
          if (!isset($metadataGroup['list']) || !is_array($metadataGroup['list'])) {
            $newSchema->addError("The list of the metadata's group at the index " . $index . " is missing or is not an array.");
          } else {
            foreach ($metadataGroup['list'] as $tagName => $content) {
              // shortcut required
              if (!isset($content['shortcut'])) {
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

  /**
   * Create the default schemata object
   */
  private function synchronize_Default_Schemata()
  {
    $this->convert_Json_File_To_Schema_Object(self::DEFAULT_SCHEMATA_FOLDER_PATH);
  }

  /**
   * Create the custom schemata object
   */
  private function synchronize_User_Schemata()
  {
    $this->convert_Json_File_To_Schema_Object(self::USER_SCHEMATA_FOLDER_PATH);
  }


  /**
   * Allow to change the user schemata folder
   *
   * @param $oldSchemataFolderPath
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
   * @return String
   */
  public function getUserSchemataFolderPath()
  {
    return $this->userSchemataFolderPath;
  }

  /**
   * @param $userSchemataFolderPath
   * @param bool $removeDefaultOlder
   */
  public function setUserSchemataFolderPath($userSchemataFolderPath, $removeDefaultOlder = false)
  {
    $oldSchemataFolderPath = $this->userSchemataFolderPath;
    $this->userSchemataFolderPath = $userSchemataFolderPath;
    $this->change_User_Schemata_Folder($oldSchemataFolderPath, $removeDefaultOlder);
  }

  /**
   * @return array|Schema[]
   */
  public function getSchemata()
  {
    return $this->schemata;
  }

  /**
   * Return the list of valid schemata
   *
   * @return array
   */
  public function getValidSchemata()
  {
    $validSchemata = array();
    foreach ($this->schemata as $schema) {
      if ($schema->isValid()) {
        array_push($validSchemata, $schema);
      }
    }
    return $validSchemata;
  }

  /**
   * Return the state of each schema in the list schemata
   *
   * @return array
   */
  public function checkSchemataState()
  {
    $schemataState = array();
    foreach ($this->schemata as $schema) {
      if ($schema->isValid()) {
        $schemataState[$schema->getShortcut()] = "valid";
      } else {
        $schemataState[$schema->getShortcut()] = $schema->getErrors();
      }
    }
    return $schemataState;
  }


}
