#Metasya

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



### Notion of Taskers

The **MetadataHelper** object has several **Taskers**. Each **Tasker** brings features thanks the use of exiftool.



#### ReaderTasker

The **ReaderTasker** allows to read file's metadata. You can use 3 features which are :

##### **read** (\$selectedMetadata, \$excludedMetadata) :
* description : Allow to read all or some file's metadata without exiftool group option.
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

  * description : Allow to read all or some file's metadata with the group option -g[$num...] which organize output by tag group.
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

  * description : Allow to read all or some file's metadata with the group option -G[$num...] which print group name before each tag.
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

The WriterTasker allow to add metadata to a file or to edit file's metadata. You can use 3 features which are :

##### **write** (\$targetedMetadata, \$replace, \$overwrite) :
* description : Allow to add or edit some metadata of a file.
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

The EraserTasker allow to remove file's metadata. Only one feature is available at this moment :

##### **remove** ($targetedMetadata, \$excludedMetadata, \$overwrite) :
* description : Allow to remove all or some file's metadata.
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



* **What is a schema ?** A schema can be a JSON file and/or an object which contains information like the sortcut of the schema, metadata properties, namespace, description ... There are two kinds of schema : the default schemata which are nominated by Metasya, and the user's schemata which are created by the user.



* **What is the utility of this system ?** With this system, you can use several defaults schemata in order to read a lot of metadata for example. This sytem it's a saving of time : you can just write one word (the shema's shortcut) instead of the list of all metadata you want to read.




#### How to add a custom schema as JSON file

Note that you have the possibility to create your own schemata and to stock them in the desired folder. Thus, schemeta created in a project can be used in an other one !



*Example of a valid schema as JSON file :*

```json
{
  "shortcut": "cosmos",
  "description": "Schema to get some metadata",
  "namespace": "XMP-dc",
  "properties": {
    "Title": {
      "value": null,
      "namespace": null
    },
    "Creator": {
      "value": "Mr nobody",
      "namespace": null
    },
    "Description": {
      "value": null,
      "namespace": null
    },
    "FileSize": {
      "value": null,
      "namespace": "System"
    }
  }
}
```



#### The SchemataManager

First you need to know that the SchemataManager is a singleton : it means that only one instance of this class can be created. **How it works ?** The SchemataManager will automotically convert all the JSON file inside the both directory user and default shemata to schema objects. Next these schema objects will be added to the list of schemata of the SchemataManager.



You can get it like follow :

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

  array (size=1)
    0 => 
      object(MagicMonkey\Metasya\Schema\Schema)[4]
        private 'shortcut' => string 'xmp-test' (length=8)
        private 'namespace' => string 'XMP-dc' (length=6)
        private 'description' => string '' (length=0)
        private 'properties' => 
          array (size=2)
            0 => 
              object(MagicMonkey\Metasya\Schema\Property)[5]
                ...
            1 => 
              object(MagicMonkey\Metasya\Schema\Property)[6]
                ...

*/
```



##### The path of the user's schemata's folder

By default, the user's schemata's folder is called "**metasyaSchemata**" and it's created at the root of your project. However, you can change it like following :

```php
$metadataHelper->getSchemataManager()->setUserSchemataFolderPath("my/new/path");
```



If the old folder "**metasyaSchemata**" contains json files as schemata, all these files will be copied inside the new folder. Next, if you want to delete the folder "**metasyaSchemata**" and it's content, you can do it manually (safe and secure) or you can ask to Metasya to do it automatically. Indeed, you can inform a boolean with the value "true" as parameter which indicates to remove the older folder and it's content after the copy :

```php
$metadataHelper->getSchemataManager()->setUserSchemataFolderPath("my/new/path", true);
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




#### The class Property

A Property object corresponds to a metadata tag. You can create a Proprety according to its tag name, its namespace and its value. Note that only the tag name is required. Let's see an example :

```php
$titleProperty = new Property("Title");
$creatorProperty = new Property("Creator", "", "Mr nobody");
$descriptionProperty = new Property("Description");
$sizeProperty = new Property("FileSize", "System");
```



#### The class Schema

A Schema object is constituted by a shortcut, a global namespace and a list of properties. The shortcut allows to make a reference to the schema. If the Schema is used to read metadata, the global namespace is used before every property  which has not its own namespace. It's practical when a lot of properties have the same namespace because it's not necessary to inform it during the creation of the property.

```php
/* For this example, we will use the properties created in the last one */
$mySchemaObject = new Schema("shortcut", "XMP-dc", "Schema to get some metadata");
$mySchemaObject->addProperty($titleProperty);
$mySchemaObject->addProperty($creatorProperty);
$mySchemaObject->addProperty($descriptionProperty);
$mySchemaObject->addProperty($sizeProperty);

$metadataHelper->read([$mySchemaObject]);
/*
thus Exiftool will search for the following properties : 
	=> XMP-dc:Title, XMP-dc:Creator, XMP-dc:Description, System:FileSize 
*/
```



##### Get the list of properties

```php
var_dump($mySchemaObject->getProperties());

/* result :
array (size=4)
  0 => 
    object(MagicMonkey\Metasya\Schema\Property)[7]
      private 'tagName' => string 'Title' (length=5)
      private 'nameSpace' => null
      private 'value' => null
  1 => 
    object(MagicMonkey\Metasya\Schema\Property)[8]
      private 'tagName' => string 'Creator' (length=7)
      private 'nameSpace' => string '' (length=0)
      private 'value' => string 'Mr nobody' (length=9)
  2 => 
    object(MagicMonkey\Metasya\Schema\Property)[9]
      private 'tagName' => string 'Description' (length=11)
      private 'nameSpace' => null
      private 'value' => null
  3 => 
    object(MagicMonkey\Metasya\Schema\Property)[10]
      private 'tagName' => string 'FileSize' (length=8)
      private 'nameSpace' => string 'System' (length=6)
      private 'value' => null
*/
```



##### Get the list of properties as targeted metadata

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



##### Add and remove property to a schema

Obviously you can add and remove a property to a schema like following :

```php
$mySchemaObject->addProperty(new Property("Title"));
$mySchemaObject->removeProperty($creatorProperty);
/* or with the index */
$mySchemaObject->removeProperty(0);
```



##### Deploy/add a schema object as json

You can deploy a schema object as a json file like following :

```php
$mySchemaObject->deploy();
```

The execution of this function will create the json of the schema and will add it inside the user's schemata's folder. Its execution will also add the schema object to schemata list of the the SchemataManger. Note that this funcion only works if the shortcut of the schema is not already used. It means that you can update a schema via this function. Modify json files instead.




#### The list of defaults schemata




## UML

![UML of PHP-Metadata-Manager Project](https://raw.githubusercontent.com/jeremy-habit/PHP-Metadata-Manager/master/documentation/PHP_METADATA_MANAGER.jpg)