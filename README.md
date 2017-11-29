# Metasya ( PHP-Metadata-Manager )
Metasya is a library allowing the management of embarked metadatas on diverse types of files, to manage the import of metadatas in an information system and the synchronization of the data between the information system and files with exiftool.

What is **Exiftool** ? Take a look here : [https://www.sno.phy.queensu.ca/~phil/exiftool/](https://www.sno.phy.queensu.ca/~phil/exiftool/)

## Install

1. You have to use [Composer](https://getcomposer.org/), a tool for dependency management in PHP :

    ````bash
    composer require magicmonkey/metasya
    ````
    
    Metasya is enregistred as package on Packagist : [https://packagist.org/packages/magicmonkey/metasya](https://packagist.org/packages/magicmonkey/metasya)

2. To activate the autoloader, you may need to type the following command into the command line :

    ````bash
    composer dumpautoload -o
    ````
    
3. With operating system based on UNIX, the provided version of Exiftool by Metasya at least must have the execute permission for the owner.

    ````bash
    chmod 500 vendor/magicmonkey/metasya/exiftool/unix/exiftool
    ````

4. You can write in a file like index.php a code that tests if Metasya that you have just downloaded really works :

    ````php
    <?php
     /* index.php */
     
    /* import the needed class */
    use MagicMonkey\Metasya\MetadataHelper; 
    
    /* include the composer autoloader */
    include __DIR__ . "/vendor/autoload.php";
    
    /* Create a MetadataHelper Object with a file path as parameter */
    $metadataHelper = new MetadataHelper("photo1.jpg");  
    
    /* Look all medatadata of photo1.jpg */
    var_dump($metadataHelper->read());
    ````

## Usage : Here we go !
### The MetadataHelper Object
#### Create the object
In order to manage metadata of a file you have to create a new **MetadataHelper** object with the path of the file.

````php
<?php

use MagicMonkey\Metasya\MetadataHelper;

$metadataHelper = new MetadataHelper("data/images/photo1.jpg");
````

#### Use the exiftool version installed on your computer
By default, Metasya uses the provided exiftool. However it is possible to use the one installed on your computer in two different ways :

````php
<?php

use MagicMonkey\Metasya\MetadataHelper;

/* First way via the constructor : passing as second parameter the boolean false which indicates to not use the provided exiftool */

$metadataHelper = new MetadataHelper("data/images/photo1.jpg", false);

/* Second way via the setter : set the attribute named "useProvidedExiftool" to false */

$metadataHelper = new MetadataHelper("data/images/photo1.jpg");
$metadataHelper->setUseProvidedExiftool(false);


````

#### Version information

Different functions are available in order to get information about the version of exiftool.

````php
<?php

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
       'Provided' => string '10.67' (length=6)*/

````

#### Change the path of the file
If you have to change the path of the file, you can proceed as described bellow :

````php
<?php

$metadataHelper->setFilePath("data/images/photo2.jpg");
````

#### Execute his own exiftool command line

The next part is about taskers which allow you to manage files metadata thanks predefined commands. However, you can use
the function "execute" in order to do specifically what you really want.

````php
<?php

/* Print all meta information in an image, including duplicate and unknown tags, sorted by group (for family 1). */
$metadataHelper->execute("-a -u -g1 image.jpg");
```` 

### Notion of Taskers

The **MetadataHelper** object has several **Taskers**. Each **Tasker** bring features thanks the use of exiftool.


### ReaderTasker

The **ReaderTasker** allow to read file's metadata. You can use 3 features which are :

* **read** ($selectedMetadata, $excludedMetadata) :
    * description : Allow to read all or some file's metadata without exiftool group option.
    * params :
        * **$selectedMetadata** ( default : "all" ) : Indicates metadata you want to read. Can be a **String** or an **Array**.
        * **$excludedMetadata** ( default : null ) : Indicates metadata you won't to read. Can be a **String** or an **Array**.
    * return : array | null | string
    * examples :
        * Read all metadata 
        
            ````php
            <?php
            
                $metadataHelper->reader()->read();
                
                /* or the short way */
                
                $metadataHelper->read();
            ````
      
        * Read all XMP Dublin Core metadata except the XMP Dublin Core subject :
        
             ````php
            <?php
                        
            $metadataHelper->reader()->read("XMP-dc:all", "XMP-dc:Subject");
                
            /* or the short way */
            
            $metadataHelper->read("XMP-dc:all", "XMP-dc:Subject");
            
            /* Result :
            
            array (size=5)
              'SourceFile' => string 'data/images/photo1.jpg' (length=22)
              'Rights' => string 'CC-by-sa' (length=8)
              'Description' => string 'Western part of the abandoned Packard Automotive Plant in Detroit, Michigan.' (length=76)
              'Creator' => string 'Albert Duce' (length=11)
              'Title' => string 'Abandoned Packard Automobile Factory, Detroit' (length=45)*/
         
         * Read all metadata except XMP Photoshop and XMP Rights metadata :
         
            ````php
            <?php
                                   
            $metadataHelper->reader()->read("all", ["XMP-photoshop:all", "XMP-xmpRights:all"]);
                
            /* or the short way */
                
            $metadataHelper->read("all", ["XMP-photoshop:all", "XMP-xmpRights:all"]);
            ````
 
* **readByGroup** ($selectedMetadata, $num, $excludedMetadata) :
    * description : Allow to read all or some file's metadata with the group option -g[$num...] which organize output by tag group.
    * params :
        * **$selectedMetadata** ( default : "all" ) : Indicates metadata you want to read. Can be a **String** or an **Array**.
        * **$num** ( default : 0 ) : Indicates the level of group.
        * **$excludedMetadata** ( default : null ) : Indicates metadata you won't to read. Can be a **String** or an **Array**.
    * return : array | null | string
    * examples :
        * Read all metadata with the group level 1 :
        
            ````php
            <?php
            
            $metadataHelper->reader()->readByGroup("all", 1);
                
            /* or the short way */
                
            $metadataHelper->readByGroup("all", 1);
            ````
  
        * Read all XMP Dublin Core metadata except the XMP Dublin Core subject with the group level 1 :
            
            ````php
            <?php
            
            $metadataHelper->reader()->readByGroup("XMP-dc:all", 1, "XMP-dc:Subject");
            
            /* or the short way */
            
            $metadataHelper->readByGroup("XMP-dc:all", 1, "XMP-dc:Subject");
            
            /* Result :
            
            array (size=2)
              'SourceFile' => string 'data/images/photo1.jpg' (length=22)
              'XMP-dc' => 
                array (size=4)
                  'Rights' => string 'CC-by-sa' (length=8)
                  'Description' => string 'Western part of the abandoned Packard Automotive Plant in Detroit, Michigan.' (length=76)
                  'Creator' => string 'Albert Duce' (length=11)
                  'Title' => string 'Abandoned Packard Automobile Factory, Detroit' (length=45) */
            ````
    
    
* **readWithPrefix** ($selectedMetadata, $num, $excludedMetadata) :
     * description : Allow to read all or some file's metadata with the group option -G[$num...] which print group name before each tag.
     * params :
        * **$selectedMetadata** ( default : "all" ) : Indicates metadata you want to read. Can be a **String** or an **Array**.
        * **$num** ( default : 0 ) : Indicates the level of group.
        * **$excludedMetadata** ( default : null ) : Indicates metadata you won't to read. Can be a **String** or an **Array**.
     * return : array | null | string
     * examples :
     
         * Read all metadata :
         
            ````php
            <?php
            
             $metadataHelper->reader()->readWithPrefix();
             
             /* or the short way */
             
             $metadataHelper->readWithPrefix();
            ````
                     
         * Read all XMP Dublin Core metadata except the XMP Dublin Core subject with the group level 1:
             
             ````php
             <?php
            
             $metadataHelper->reader()->readWithPrefix("XMP-dc:all", 1, "XMP-dc:Subject");
             
             /* or the short way */
             
             $metadataHelper->readWithPrefix("XMP-dc:all", 1, "XMP-dc:Subject");
             
             /* Result :
            
             array (size=5)
               'SourceFile' => string 'data/images/photo1.jpg' (length=22)
               'XMP-dc:Rights' => string 'CC-by-sa' (length=8)
               'XMP-dc:Description' => string 'Western part of the abandoned Packard Automotive Plant in Detroit, Michigan.' (length=76)
               'XMP-dc:Creator' => string 'Albert Duce' (length=11)
               'XMP-dc:Title' => string 'Abandoned Packard Automobile Factory, Detroit' (length=45)*/
             ````
    
### WriterTasker

The WriterTasker allow to add metadata to a file or to edit file's metadata. You can use 3 features which are :

* **write** ($targetedMetadata, $replace, $overwrite) :
    * description : Allow to add or edit some metadata of a file.
    * params :
        * **$targetedMetadata** ( default : null ) : Indicates metadata you want to add or edit.
        * **$replace** ( default : true ) : Indicates if the metadata value must be replaced if the metadata already exists.
        * **$overwrite** ( default : true ) : Indicates if the addition or the modification must be applied to the original file or to a copy. It's corresponds to the use of the exiftool option -overwrite_original.
    * return : string | array with potentially the command return message and the success of the command as boolean : true or false.
    * examples :
        * Write some XMP Dublin Core metadata :
    
            ````php
            <?php
            
            $metadataHelper->writer()->write(["XMP-dc:Ttile" => "Blue Bird", "XMP-dc:Description" => "My song of the year"]);
            
            /* or the short way */
            
            $metadataHelper->write(["XMP-dc:Ttile" => "Blue Bird", "XMP-dc:Description" => "My song of the year"]);
            
            /* Result :
            
            array (size=2)
              'exiftoolMessage' => string '1 image files updated' (length=21)
              'success' => boolean true*/
            ````
                
        * Write XMP Dublin Core title only if it does not already exists :
        
            ````php
            <?php
            
            $metadataHelper->writer()->write(["XMP-dc:Title" => "First Title"], false);
            
            /* or the short way */
            
            $metadataHelper->write(["XMP-dc:Title" => "First Title"], false);
            
            /* Result :
            
            array (size=2)
              'exiftoolMessage' => string '1 image files updated' (length=21)
              'success' => boolean true */
            ````
                  
                  
* **writeFromJsonFile** ($jsonFilePath, $replace, $overwrite) :
    * description : Same as write feature but from a json file.
    * **WARNING** : Note that the json inside the json file must contains the metadata tag "SourceFile" with the path of the file used by the MetadataHelper Object as value.
    * params :
        * **$jsonFilePath** ( default : null ) : Indicates the path of the json file which contains metadata tags to use.
        * **$replace** ( default : true ) : Indicates if the metadata value must be replaced if the metadata already exists.
        * **$overwrite** ( default : true ) : Indicates if the addition or the modification must be applied to the original file or to a copy. It's corresponds to the use of the exiftool option -overwrite_original.
    * return : string | array with potentially the command return message and the success of the command as boolean : true or false.
    * examples :
        * Write metadata from json file :
    
            ````php
            <?php
            
            $metadataHelper->writer()->writeFromJsonFile("../path/to/data.json");
            
            /* or the short way */
            
            $metadataHelper->writeFromJsonFile("../path/to/data.json");
            
            /* data.json :
            
            [{"SourceFile": "data/images/photo1.jpg",    <-- same value as $filePath
              "XMP-dc:Title": "Le titre de mon image",
              "XMP-dc:Rights": "CC-by-nc-sa",
              "XMP-dc:Description": "This is a test",
              "XMP-dc:Description-en-EN": "This is a test"
            }] */
            
            /* Result :
            
            array (size=2)
              'exiftoolMessage' => string '1 image files updated' (length=21)
              'success' => boolean true */
            ````
           
* **writeFromJson** ($json, $replace, $overwrite) :
    * description : Same as write feature but from a json string.
    * **WARNING** : Note that the json string must contains the metadata tag "SourceFile" with the path of the file used by the MetadataHelper Object as value.
    * params :
        * **$json** : Indicates the json string which contains metadata tags to use.
        * **$replace** ( default : true ) : Indicates if the metadata value must be replaced if the metadata already exists.
        * **$overwrite** ( default : true ) : Indicates if the addition or the modification must be applied to the original file or to a copy. It's corresponds to the use of the exiftool option -overwrite_original.
    * return : string | array with potentially the command return message and the success of the command as boolean : true or false.
    * examples :
        * Write metadata from json file :
    
            ````php
            <?php
            
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
            
            array (size=2)
              'exiftoolMessage' => string '1 image files updated' (length=21)
              'success' => boolean true     */
            ````

### EraserTasker

The EraserTasker allow to remove file's metadata. Only one feature is available at this moment :

* **remove** ($targetedMetadata, $excludedMetadata, $overwrite) :
    * description : Allow to remove all or some file's metadata.
    * params :
        * **$targetedMetadata** ( default : "all" ) : Indicates metadata you want to remove. Can be a **String** or an **Array**.
        * **$excludedMetadata** ( default : null ) : Indicates metadata you won't to remove. Can be a **String** or an **Array**.
        * **$overwrite** ( default : true ) : Indicates if the deletion must be applied to the original file or to a copy. It's corresponds to the use of the exiftool option -overwrite_original.
    * return : string | array with potentially the command return message and the success of the command as boolean : true or false.
    * examples :
        * Remove all metadata :
    
            ````php
            <?php
            
            $metadataHelper->eraser()->remove();
            
            /* or the short way */
            
            $metadataHelper->remove();
            
            /* Result :
            
            array (size=2)
              'exiftoolMessage' => string '1 image files updated' (length=21)
              'success' => boolean true */
            ````
                
        * Remove all XMP Dublin Core metadata except the XMP Dublin Core title :
        
            ````php
            <?php
            
            $metadataHelper->eraser()->remove("XMP-dc:all", "XMP-dc:Title");
            
            /* or the short way */
            
            $metadataHelper->remove("XMP-dc:all", "XMP-dc:Title");
            
            /* Result :
            
            array (size=2)
              'exiftoolMessage' => string '1 image files updated' (length=21)
              'success' => boolean true */
            ````

## UML

![UML of PHP-Metadata-Manager Project](https://raw.githubusercontent.com/jeremy-habit/PHP-Metadata-Manager/master/documentation/PHP_METADATA_MANAGER.jpg)