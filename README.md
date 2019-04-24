# Metasya

Metasya is a library allowing the management of embarked metadatas on diverse types of files, to manage the import of metadatas in an information system and the synchronization of the data between the information system and files with exiftool.

What is **Exiftool** ? Take a look here : [https://www.sno.phy.queensu.ca/~phil/exiftool/](https://www.sno.phy.queensu.ca/~phil/exiftool/)

[TOC]

## Install

1. You have to use [Composer](https://getcomposer.org/), a tool for dependency management in PHP :

  ```bash
  composer require magicmonkey/metasya
  ```
  Metasya is enregistred as package on Packagist : [https://packagist.org/packages/magicmonkey/metasya](https://packagist.org/packages/magicmonkey/metasya)

  ​

2. To activate the autoloader, you may need to type the following command into the command line :

    ```bash
    composer dumpautoload -o
    ```
    ​

3. With operating system based on UNIX, the provided version of Exiftool by Metasya at least must have the execute permission for the owner :

    ```bash
    chmod 500 vendor/magicmonkey/metasya/exiftool/unix/exiftool
    ```
    ​

4. You can write in a file like index.php a code that tests if Metasya that you have just downloaded really works :

    ```php
    /* import the needed class */
    use MagicMonkey\Metasya\MetadataHelper; 

    /* include the composer autoloader */
    include __DIR__ . "/vendor/autoload.php";

    /* Create a MetadataHelper Object with a file path as parameter */
    $metadataHelper = new MetadataHelper("photo1.jpg");  

    /* Look all medatadata of photo1.jpg */
    var_dump($metadataHelper->read());
    ```




## Usage : Here we go !



### The MetadataHelper

The MetadataHelper is the main class of Metasya.



#### Create the object

In order to manage metadata of a file you have to create a new **MetadataHelper** object with the path of the file.

```php
$metadataHelper = new MetadataHelper("data/images/photo1.jpg");
```



#### Use the exiftool version installed on your computer

By default, Metasya uses the provided exiftool. However it's possible to use the one installed on your computer in two different ways :

```php
/* First way via the constructor : passing as SECOND parameter the boolean false which indicates to not use the provided exiftool */

$metadataHelper = new MetadataHelper("data/images/photo1.jpg", false);

/* Second way via the setter : set the attribute named "useProvidedExiftool" to false */

$metadataHelper = new MetadataHelper("data/images/photo1.jpg");
$metadataHelper->setUseProvidedExiftool(false);
```



#### Display errors

By default, Metasya displays errors returned by the tool Exiftool. However it's possible to not display it :

```php
/* First way via the constructor : passing as THIRD parameter the boolean false which indicates to not display errors */

$metadataHelper = new MetadataHelper("data/images/photo1.jpg", true, false);

/* Second way via the setter : set the attribute named "displayErros" to false */

$metadataHelper = new MetadataHelper("data/images/photo1.jpg");
$metadataHelper->setDisplayErrors(false);
```



#### Version information

Various functions are available in order to get information about the version of exiftool.

```php
/* Get the version of the exiftool installed on your computer else return null */
echo $metadataHelper->getLocalExiftoolVersion();

/* Get the version of the provided exiftool by Metasya */
echo $metadataHelper->getProvidedExiftoolVersion();

/* Return an array which indicates if Metasya uses the local or the provided exiftool and the version of the used exiftool */
var_dump($metadataHelper->getUsedExiftoolVersion());

/* example : 
array (size=1)
  'Provided' => string '10.67' (length=6)
*/

/* return an array which contains 3 information above */
var_dump($metadataHelper->getExiftoolVersionsInfo());

/* example : 

array (size=3)
  'Local' => null     ----> exiftool not installed ...
  'Provided' => string '10.67' (length=6)
  'Used' => 
  	array (size=1)
  	  'Provided' => string '10.67' (length=6)
  	  
*/
```



#### Change the path of the file

If you have to change the path of the file, you can proceed as described bellow :

```php
$metadataHelper->setFilePath("data/images/photo2.jpg");
```



#### Execute his own exiftool command line

The next part is about taskers which allow you to manage files metadata thanks predefined commands. However, you can use the function "execute" in order to do specifically what you really want regardless of the path of the file given as parameter to the object metadataHelper.

```php
/* Print all meta information in an image, including duplicate and unknown tags, sorted by group (for family 1). */
 
var_dump($metadataHelper->execute("-a -u -g1 image.jpg"));
```



#### Generate Sidecar file

**Sidecar files**, also known as **buddy files** or **connected files**, are computer files that store data (often metadata which is not supported by the format of a source file.

```php
$metadataHelper->generateXMPSideCar();
```



Note that by default the output path of the generated sidecar file is the folder which is called "**metasya/Sidecar**" and it's created at the root of your project. However you can specify an other path like this :

```php
$metadataHelper->generateXMPSideCar("an/other/path");
```



### Notion of Taskers

The **MetadataHelper** object has several **Taskers**. Each **Tasker** brings features thanks the use of exiftool.



#### ReaderTasker

The **ReaderTasker** allows to read file's metadata. You can use 3 features which are :

##### **read** (\$selectedMetadata, \$excludedMetadata) :
* description : Allows to read all or some file's metadata without exiftool group option.
* params :
    * **$selectedMetadata** : array (default : null) : Indicates metadata you want to read.
    * **$excludedMetadata** : array (default : null) : Indicates metadata you won't to read.
* return : array | string
* examples :
    * Read all metadata 

        ```php
        $metadata = $metadataHelper->reader()->read();
            
        /* or the short way */
            
        $metadata = $metadataHelper->read();
        ```

    * Read all XMP Dublin Core metadata except the XMP Dublin Core subject :

         ```php
         $metadata = $metadataHelper->reader()->read(["XMP-dc:all"], ["XMP-dc:Subject"]);

         /* or the short way */

         $metadata = $metadataHelper->read(["XMP-dc:all"], ["XMP-dc:Subject"]);

         /* Result :
             
         array (size=5)
           'SourceFile' => string 'data/images/photo1.jpg' (length=22)
           'Rights' => string 'CC-by-sa' (length=8)
           'Description' => string 'Western part of the abandoned Packard Automotive Plant in 		Detroit, Michigan.' (length=76)
            'Creator' => string 'Albert Duce' (length=11)
            'Title' => string 'Abandoned Packard Automobile Factory, Detroit' (length=45)
             
         */
         ```

    * Read all metadata except XMP Photoshop and XMP Rights metadata :

        ```php
        $metadata = $metadataHelper->reader()->read(["all"], ["XMP-photoshop:all", "XMP-xmpRights:all"]);
            
        /* or the short way */
            
        $metadata = $metadataHelper->read(["all"], ["XMP-photoshop:all", "XMP-xmpRights:all"]);
        ```




##### **readByGroup** (\$selectedMetadata, \$num, \$excludedMetadata) :

  * description : Allows to read all or some file's metadata with the group option -g[$num...] which organize output by tag group.
  * params :
        * **$selectedMetadata** : array (default : null) : Indicates metadata you want to read.
        * **$num** : int (default : 0) : Indicates the level of group.
        * **$excludedMetadata** : array (default : null) : Indicates metadata you won't to read.
    * return : array | string
    * examples :
        * Read all metadata with the group level 1 :

            ```php
            $metadata = $metadataHelper->reader()->readByGroup(["all"], 1);
                
            /* or the short way */
                
            $metadata = $metadataHelper->readByGroup(["all"], 1);
            ```

        * Read all XMP Dublin Core metadata except the XMP Dublin Core subject with the group level 1 :

            ```php
            $metadata = $metadataHelper->reader()->readByGroup(["XMP-dc:all"], 1, ["XMP-dc:Subject"]);

            /* or the short way */

            $metadata = $metadataHelper->readByGroup(["XMP-dc:all"], 1, ["XMP-dc:Subject"]);

            /* Result :

            array (size=2)
              'SourceFile' => string 'data/images/photo1.jpg' (length=22)
              'XMP-dc' => 
                array (size=4)
                  'Rights' => string 'CC-by-sa' (length=8)
                  'Description' => string 'Western part of the abandoned Packard Automotive Plant in 		Detroit, Michigan.' (length=76)
                  'Creator' => string 'Albert Duce' (length=11)
                  'Title' => string 'Abandoned Packard Automobile Factory, Detroit' (length=45)
                  
            */
            ```




##### **readWithPrefix** (\$selectedMetadata, \$num, \$excludedMetadata) :

  * description : Allows to read all or some file's metadata with the group option -G[$num...] which print group name before each tag.
  * params :
        * **$selectedMetadata** : array (default : null) : Indicates metadata you want to read.
        * **$num** : int (default : 0) : Indicates the level of group.
        * **$excludedMetadata** : array (default : null) : Indicates metadata you won't to read.
     * return : array | string
     * examples :

         * Read all metadata :

            ```php
            $metadata = $metadataHelper->reader()->readWithPrefix();
             
             /* or the short way */
             
             $metadata = $metadataHelper->readWithPrefix();
            ```

         * Read all XMP Dublin Core metadata except the XMP Dublin Core subject with the group level 1:

             ```php
             $metadata = $metadataHelper->reader()->readWithPrefix(["XMP-dc:all"], 1, ["XMP-dc:Subject"]);

             /* or the short way */

             $metadata = $metadataHelper->readWithPrefix(["XMP-dc:all"], 1, ["XMP-dc:Subject"]);

             /* Result :
                
             array (size=5)
               'SourceFile' => string 'data/images/photo1.jpg' (length=22)
               'XMP-dc:Rights' => string 'CC-by-sa' (length=8)
               'XMP-dc:Description' => string 'Western part of the abandoned Packard Automotive Plant 	in Detroit, Michigan.' (length=76)
               'XMP-dc:Creator' => string 'Albert Duce' (length=11)
               'XMP-dc:Title' => string 'Abandoned Packard Automobile Factory, Detroit' (length=45)

             */
             ```




#### WriterTasker

The WriterTasker allows to add metadata to a file or to edit file's metadata. You can use 3 features which are :

##### **write** (\$targetedMetadata, \$replace, \$overwrite) :
* description : Allows to add or edit some metadata of a file.
* params :
  * **$targetedMetadata** ( default : null ) : Indicates metadata you want to add or edit.
  * **$replace** ( default : true ) : Indicates if the metadata value must be replaced if the metadata already exists.
  * **$overwrite** ( default : true ) : Indicates if the addition or the modification must be applied to the original file or to a copy. It's corresponds to the use of the exiftool option -overwrite_original.

* return : string | null
* examples :
  * Write some XMP Dublin Core metadata :

    ```php
    $metadataHelper->writer()->write(["XMP-dc:Title" => "Blue Bird", "XMP-dc:Description" => "My song of the year"]);

    /* or the short way */

    $metadataHelper->write(["XMP-dc:Title" => "Blue Bird", "XMP-dc:Description" => "My song of the year"]);

    /* Result :

    	:string '1 image files updated' (length=21)

    */
    ```

   * Write XMP Dublin Core title only if it doesn't already exists :

     ```php
     $metadataHelper->writer()->write(["XMP-dc:Title" => "First Title"], false);

     /* or the short way */

     $metadataHelper->write(["XMP-dc:Title" => "First Title"], false);
     ```

   * Write Keywords as an array appending the values :

     ```php
     $metadataHelper->writer()->write(["Keywords" => ["One", "Two", "Three"]], false);

     /* or the short way */

     $metadataHelper->writer()->write(["Keywords" => ["One", "Two", "Three"]], false);

     /* or when replacing the values */

     $metadataHelper->writer()->write(["Keywords" => ["One", "Two", "Three"]], true);
     ```



##### **writeFromJsonFile** (\$jsonFilePath, \$replace, \$overwrite) :

* description : Same as write feature but from a json file.

* **WARNING** : Note that the json inside the json file must contains the metadata tag "SourceFile" with the path of the file used by the MetadataHelper Object as value.
* params :
    * **$jsonFilePath** ( default : null ) : Indicates the path of the json file which contains metadata tags to use.
    * **$replace** ( default : true ) : Indicates if the metadata value must be replaced if the metadata already exists.
    * **$overwrite** ( default : true ) : Indicates if the addition or the modification must be applied to the original file or to a copy. It's corresponds to the use of the exiftool option -overwrite_original.

* return : string | null

* examples :
    * Write metadata from json file :

        ```php
        $metadataHelper->writer()->writeFromJsonFile("../path/to/data.json");

        /* or the short way */

        $metadataHelper->writeFromJsonFile("../path/to/data.json");

        /* data.json :

          [{"SourceFile": "data/images/photo1.jpg",    <-- same value as $filePath
            "XMP-dc:Title": "Le titre de mon image",
            "XMP-dc:Rights": "CC-by-nc-sa",
            "XMP-dc:Description": "This is a test",
            "XMP-dc:Description-en-EN": "This is a test"
          }]

        */

        /* Result :

        	:string '1 image files updated' (length=21)

        */
        ```




##### **writeFromJson** (\$json, \$replace, \$overwrite) :

* description : Same as **write** feature but from a json string.

* **WARNING** : Note that the json string must contains the metadata tag "SourceFile" with the path of the file used by the MetadataHelper Object as value.
* params :
    * **$json** : Indicates the json string which contains metadata tags to use.
    * **$replace** ( default : true ) : Indicates if the metadata value must be replaced if the metadata already exists.
    * **$overwrite** ( default : true ) : Indicates if the addition or the modification must be applied to the original file or to a copy. It's corresponds to the use of the exiftool option -overwrite_original.

* return : string | null

* examples :
    * Write metadata from json file :

        ```php
        $metadataHelper->writer()->writeFromJson('
              [{"SourceFile": "data/images/photo1.jpg",
              "XMP-dc:Title": "Le titre de mon image",
              "XMP-dc:Rights": "CC-by-nc-sa",
              "XMP-dc:Description": "This is a test",
              "XMP-dc:Description-en-EN": "This is a test"
              }]
        ');

        /* or the short way */

        $metadataHelper->writeFromJson('
                              [{"SourceFile": "data/images/photo1.jpg",
                              "XMP-dc:Title": "Le titre de mon image",
                              "XMP-dc:Rights": "CC-by-nc-sa",
                              "XMP-dc:Description": "This is a test",
                              "XMP-dc:Description-en-EN": "This is a test"
                              }]
                        ');

        /* Result :

        	:string '1 image files updated' (length=21)
            
        */
        ```





#### EraserTasker

The EraserTasker allows to remove file's metadata. Only one feature is available at this moment :

##### **remove** ($targetedMetadata, \$excludedMetadata, \$overwrite) :
* description : Allows to remove all or some file's metadata.
* params :
    * **$targetedMetadata** ( default : "all" ) : Indicates metadata you want to remove. Can be a **String** or an **Array**.
    * **$excludedMetadata** ( default : null ) : Indicates metadata you won't to remove. Can be a **String** or an **Array**.
    * **$overwrite** ( default : true ) : Indicates if the deletion must be applied to the original file or to a copy. It's corresponds to the use of the exiftool option -overwrite_original.
* return : string | null

* examples :
    * Remove all metadata :

        ```php
        $metadataHelper->eraser()->remove(["all"]);

        /* or the short way */

        $metadataHelper->remove(["all"]);

        /* Result :

        	:string '1 image files updated' (length=21)
          
        */
        ```

    * Remove all XMP Dublin Core metadata except the XMP Dublin Core title :

        ```php
        $metadataHelper->eraser()->remove(["XMP-dc:all"], ["XMP-dc:Title"]);

        /* or the short way */

        $metadataHelper->remove(["XMP-dc:all"], ["XMP-dc:Title"]);

        /* Result :

        	:string '1 image files updated' (length=21)

        */
        ```




### The system of schemata

Metasya offers a system of schemata in order to easly manage metadata of files. 



* **What is a schema ?** A schema can be a JSON file and/or an object which contains information like the shortcut of the schema, metadata properties, namespace, description ... There are two kinds of schema : the default schemata which are nominated by Metasya, and the user's schemata which are created by the user.



* **What is the utility of this system ?** With this system, you can use several schemata in order to read, write or delete a lot of metadata. This sytem it's a saving of time : you can just write one word (the shema's shortcut) instead of the list of all metadata you want to read.




#### How to add a custom schema as JSON file

Note that you have the possibility to create your own schemata and to stock them in the desired folder. Thus, schemeta created in a project can be used in an other one !



**! Note** : the name of a personal schema must ends with "*-schema.json*". Example of a valid name : **cosmos-schema.json**.



##### Example of a valid schema as JSON file

```json
{
  "shortcut": "cosmos",
  "description": "Schema to get some metadata",
  "metadata": [
    {
      "namespace": "XMP-dc",
      "list": {
        "Title": {
          "shortcut": "dublinTitle"
        },
        "Creator": {
          "shortcut": "dublinAuthor",
           "description":"",
           "type":"String"   // corresponds to MetaTypeString class
        },
        "Description": {
          "shortcut": "dubinDesc"
        }
      }
    },
    {
      "namespace": "System",
      "list": {
        "FileSize": {
          "shortcut": "fs"
        }
      }
    }
  ]
}
```



##### Description & rules of the structure

Respect the following rules in order to create a valid schema as JSON file. Note that if a JSON file is not valid, the schema object will be created but it will not be usable.

| JSON key            | Description                                                  | Rules                                                        | Required |
| ------------------- | ------------------------------------------------------------ | ------------------------------------------------------------ | -------- |
| shortcut            | The shortcut is a label which refers to a schema or a metadata. | It must be unique.                                           | ✓        |
| description         | Describe the schema or a metadata.                           |                                                              | ✗        |
| metadata            | This JSON array contains several metadata grouped by namespace. | It must be a JSON array.                                     | ✓        |
| metadata[namespace] | Corresponds to the namespace of the group of metadata.       |                                                              | ✓        |
| metadata[list]      | The list of metadata with their shortcut.                    |                                                              | ✓        |
| type                | Corresponds to the type of value that the metadata accepts.  | It must corresponds to a PHP class which implements the interface MetaTypeInterface. If you want to use the MetaTypeString class, just write "String". | ✗        |



#### Types of metadata

The default type of a metadata is an instance of the class MetaTypeAny. This last one accepts any type of value. By specifying the type of metadata, the new value of a metadata will be checked before to be added. If this last one is not accepted, it's not added.



##### The list of types of metadata

| Class          | JSON shortcut | Description                 |
| -------------- | ------------- | --------------------------- |
| MetaTypeAny    | Any           | Accepts any type of values. |
| MetaTypeString | String        | Accepts only string values. |



##### How to create his own type of metadata





#### How to use schemata with taskers

Note that for every following examples, the above schema example called "cosmos" will be used.



##### How to read

You can read all schema's metadata by passing its shortcut or the schema as object. You can also read only some metadata of the schema in same ways.

Let's see an example :

```php
// 1.  all metadata of the schema "cosmos" and the meadata Title with the namespace XMP-dc will be returned (if they exist) :

  // shortcut way
  $metadataHelper->read(["cosmos", "XMP-dc:title"]);

  // schema object way
  $metadataHelper->read([$cosmosSchemaObject, "XMP-dc:title"]);


// 2. Only the description metadata from the cosmos schema and the meadata Title with the namespace XMP-dc will be returned (if they exist) :

  // metadata shortcut way
  $metadataHelper->read(["dublinDesc", "XMP-dc:title"]);

  // metadata object way
  $metadataHelper->read([$descriptionMetadata, "XMP-dc:title"]);
```



##### How to write

You can add or edit metatdata without knowing namespace and metadata tag. Indeed, the system of schemata allows to use the shortcut of schemata's metadata like following : 

```php
// dublinDesc and dublinTitle are shortcut, rights is not a shortcut
$metadataHelper->write(["dublinDesc" => "new description", "dublinTitle" => "new title", "rights" => "new rights"]));
```



##### How to delete

As the method to read, you can remove all schema's metadata by passing its shortcut or the schema as object. You can also remove only some metadata of the schema in same ways.

```php
// 1. remove all metadata except metadata of the schema "cosmos" :
  
$metadataHelper->remove(["all"], ["cosmos"]);

// 2. remove metadata of the schema "cosmos" and the metadata rights except the description metadata targeted via its cosmos shortcut "dublinDesc" :

$metadataHelper->remove(["cosmos", "rights"], ["dublinDesc"]);
```



#### The SchemataManager

First you need to know that the SchemataManager is a singleton : it means that only one instance of this class can be created. **How it works ?** The SchemataManager will automotically convert all the JSON file inside the both directory user and default shemata to schema objects. Next these schema objects will be added to the list of schemata of the SchemataManager.



You can get this manager like following :

```php
$schemataManager = SchemataManager::getInstance();
```

or directly via the MetadataHelper class :

```php
$metadataHelper->getSchemataManager();
```



##### Get all schemata as objects (default and user)

```php
$metadataHelper->getSchemataManager()->getSchemata();

/* Result :

array (size=2)
  0 => 
    object(MagicMonkey\Metasya\Schema\Schema)[4]
      private 'fileName' => string 'xmp-test-schema.json' (length=20)
      private 'shortcut' => string 'USER-xmp' (length=8)
      private 'isValid' => boolean false
      private 'errors' => 
        array (size=1)
          0 => string 'Schema's metadata list is missing or is not an array.' (length=53)
      private 'description' => string '' (length=0)
      private 'metadata' => 
        array (size=0)
          empty
      private 'schemaAsArray' => 
        array (size=4)
          'shortcut' => string 'USER-xmp' (length=8)
          'description' => string '' (length=0)
          'namespace' => string 'XMP-dc' (length=6)
          'properties' => 
            array (size=2)
              ...

*/
```



##### Get all valid schemata as objects (default and user)

```php
$metadataHelper->getSchemataManager()->getValidSchemata();
```



##### The path of the user's schemata's folder

By default, the user's schemata's folder is called "**metasya/Schemata**" and it's created at the root of your project. However, you can change it like following :

```php
$metadataHelper->getSchemataManager()->setUserSchemataFolderPath("my/new/path");
```



If the old folder "**metasyaSchemata**" contains json files as schemata, all these files will be copied inside the new folder. Next, if you want to delete the folder "**metasyaSchemata**" and it's content, you can do it manually (safe and secure) or you can ask to Metasya to do it automatically. Indeed, you can inform a boolean with the value "true" as parameter which indicates to remove the older folder and it's content after the copy :

```php
$metadataHelper->getSchemataManager()->setUserSchemataFolderPath("my/new/path", true);
```



You can get the user's schemata's folder for information like this :
```php
$metadataHelper->getSchemataManager()->getUsersSchemataFolder();
```




##### Test if a string is a shortcut of schema

You can test if a string is associated to a schema with the function *isSchemaShortcut()*. This last one return true of false according the shortcut value given as parameter :

```php
$metadataHelper->getSchemataManager()->isSchemaShortcut("a-shortcut");
```



##### Get a schema as object from its shortcut

You can get a schema as object with the function *getSchemaFromShortcut()* :

```php
$metadataHelper->getSchemataManager()->getSchemaFromShortcut("a-shortcut");
```



##### Test if a string is a shortcut of metadata

You can test if a string is associated to a metadata with the function *isMetadataShortcut()*. This last one return true of false according the shortcut value given as parameter. Note that only metadata from valid schemata are tested.

```php
$metadataHelper->getSchemataManager()->isMetadataShortcut("a-shortcut");
```



##### Get a metadata as object from its shortcut

You can get a metadata as object with the function *getMetadataFromShortcut()*. Note that only metadata from valid schemata are tested.

```php
$metadataHelper->getSchemataManager()->getMetadataFromShortcut("a-shortcut");
```



##### Check the state of schemata

It can be useful to check the state of schemata in order to be aware of possible errors. In the example below, the first one schema identified by "USER-xmp" isn't valid, contrary to to the second one identified by "cosmos".

```php
$metadataHelper->getSchemataManager()->checkSchemataState();

/* Result :

  array (size=2)
    'USER-xmp' => 
      array (size=1)
        0 => string 'Schema's metadata list is missing or is not an array.'
        (length=53)
    'cosmos' => string 'valid' (length=5)
    
*/
```




#### The class Metadata

A Metadata object corresponds to a metadata tag. You can create a Metadata according to its tag name, its namespace, its shortcut, it's description and it's type (in this order). Let's see an example :

```php
$title = new Metadata("Title", "XMP-dc", "ti");
$creator = new Metadata("Creator", "XMP-dc", "crea", "creator description");
$description = new Metadata("Description", "XMP-dc", "desc");
$sizeProperty = new Metadata("FileSize", "System", "fs", null, new MetaTypeString());
```

Note that by default the type of a Metadata will be the type MetaTypeAny and the description of a Metadata is null.



#### The class Schema

A Schema object is mainly constituted by a shortcut, a description and a list of metadata. The shortcut allows to make a reference to the schema.

```php
/* For this example, we will use the metadata created in the last one */
$mySchemaObject = new Schema("super-xmp", "Schema to get some metadata");
$mySchemaObject->addMetadata($title);
$mySchemaObject->addMetadata($creator);
$mySchemaObject->addMetadata($description);
$mySchemaObject->addMetadata($size);

$metadataHelper->read([$mySchemaObject]);
/*
thus Exiftool will search for the following metadata : 
	=> XMP-dc:Title, XMP-dc:Creator, XMP-dc:Description, System:FileSize 
*/
```



##### Get the list of metadata

```php
var_dump($mySchemaObject->getMetadata());

/* result :
array (size=4)
  0 => 
    object(MagicMonkey\Metasya\Schema\Metadata)[7]
      private 'tagName' => string 'Title' (length=5)
      private 'nameSpace' => null
      private 'value' => null
  1 => 
    object(MagicMonkey\Metasya\Schema\Metadata)[8]
      private 'tagName' => string 'Creator' (length=7)
      private 'nameSpace' => string '' (length=0)
      private 'value' => string 'Mr nobody' (length=9)
  2 => 
    object(MagicMonkey\Metasya\Schema\Metadata)[9]
      private 'tagName' => string 'Description' (length=11)
      private 'nameSpace' => null
      private 'value' => null
  3 => 
    object(MagicMonkey\Metasya\Schema\Metadata)[10]
      private 'tagName' => string 'FileSize' (length=8)
      private 'nameSpace' => string 'System' (length=6)
      private 'value' => null
*/
```



##### Get the list of metadata as targeted metadata

```php
var_dump($mySchemaObject->buildTargetedMetadata());

/* result :
array (size=4)
  0 => string 'XMP-dc:Title' (length=12)
  1 => string 'XMP-dc:Creator' (length=17)
  2 => string 'XMP-dc:Description' (length=18)
  3 => string 'System:FileSize' (length=15)
*/
```



##### Add and remove metadata to a schema

Obviously you can add and remove a metadata to a schema like following :

```php
$mySchemaObject->addMetadata(new Metadata("Title", "XMP-dc", "t-shortcut"));
$mySchemaObject->removeMetadata($creator);
/* or with the index */
$mySchemaObject->removeMetadata(0);
```



##### Test if a string is a shortcut of metadata of the schema

You can test if a string is associated to a metadata of the schema with the function *isMetadataShortcut()*. This last one return true of false according the shortcut value given as parameter. Note that the schema must be valid.

```php
$mySchemaObject->isMetadataShortcut("a-shortcut");
```



##### Get a metadata as object from its shortcut

You can get a metadata fo the schema as object with the function *getMetadataFromShortcut()*. Note that the schema must be valid.

```php
$mySchemaObject->getMetadataFromShortcut("a-shortcut");
```



#### The list of defaults schemata

comming soon ...
