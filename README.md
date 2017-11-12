# PHP-Metadata-Manager
Library allowing the management of embarked metadatas on diverse types of files, to manage the import of metadatas in an information system and the synchronization of the data between the information system and files with exiftool.

## The MetadataManager Object
### Create the object
In order to manage metadata of a file you have to create a new **MetadataManager** object with the path of the file. Note this object use the singleton pattern.

    $metadataManager = MetadataManager::getInstance("data/images/photo1.jpg");

### Change the path of the file

    $metadataManager->setFilePath("data/images/photo2.jpg");

## Notion of Taskers

The **MetadataManager** object have several **Taskers**. Each **Tasker** bring features thanks the use of exiftool, and whitout **Tasker** you can't do anything.

What is **Exiftool** ? Take a look here : https://www.sno.phy.queensu.ca/~phil/exiftool/





### ReaderTasker

The **ReaderTasker** allow to read file's metadata. You can use 3 features which are :

* **read** ($selectedMetadata, $excludedMetadata) :
    * description : Allow to read all or some file's metadata without exiftool group option.
    * params :
        * **$selectedMetadata** ( default : "all" ) : Indicate metadata you want to read. Can be a **String** or an **Array**.
        * **$excludedMetadata** ( default : null ) : Indicate metadata you won't to read. Can be a **String** or an **Array**.
    * return : array | null
    * examples :
        * Read all metadata :
    
                $metadataManager->reader()->read();
                
        * Read all XMP Dublin Core metadata except the XMP Dublin Core subject :
        
                $metadataManager->reader()->read("XMP-dc:all", "XMP-dc:Subject");
                
                /* Result */
                array (size=5)
                  'SourceFile' => string 'data/images/photo1.jpg' (length=22)
                  'Rights' => string 'CC-by-sa' (length=8)
                  'Description' => string 'Western part of the abandoned Packard Automotive Plant in Detroit, Michigan.' (length=76)
                  'Creator' => string 'Albert Duce' (length=11)
                  'Title' => string 'Abandoned Packard Automobile Factory, Detroit' (length=45)
         
         * Read all metadata except XMP Photoshop and XMP Rights metadata :
         
                $metadataManager->reader()->read("all", ["XMP-photoshop:all", "XMP-xmpRights:all"]);
                
                
                
                
                
                
                
                
                
* **readByGroup** ($selectedMetadata, $num, $excludedMetadata) :
    * description : Allow to read all or some file's metadata with the group option -g[$num...] which organize output by tag group.
    * params :
        * **$selectedMetadata** ( default : "all" ) : Indicate metadata you want to read. Can be a **String** or an **Array**.
        * **$num** ( default : 0 ) : Indicate the level of group.
        * **$excludedMetadata** ( default : null ) : Indicate metadata you won't to read. Can be a **String** or an **Array**.
    * return : array | null
    * examples :
        * Read all metadata with the group level 1 :
        
                $metadataManager->reader()->readByGroup("all", 1);
                    
        * Read all XMP Dublin Core metadata except the XMP Dublin Core subject with the group level 1 :
            
                $metadataManager->reader()->readByGroup("XMP-dc:all", 1, "XMP-dc:Subject");
                
                /* Result */
                array (size=2)
                  'SourceFile' => string 'data/images/photo1.jpg' (length=22)
                  'XMP-dc' => 
                    array (size=4)
                      'Rights' => string 'CC-by-sa' (length=8)
                      'Description' => string 'Western part of the abandoned Packard Automotive Plant in Detroit, Michigan.' (length=76)
                      'Creator' => string 'Albert Duce' (length=11)
                      'Title' => string 'Abandoned Packard Automobile Factory, Detroit' (length=45)
             
    
    
    
    
    
    
    
* **readWithPrefix** ($selectedMetadata, $num, $excludedMetadata) :
     * description : Allow to read all or some file's metadata with the group option -G[$num...] which print group name before each tag.
     * params :
        * **$selectedMetadata** ( default : "all" ) : Indicate metadata you want to read. Can be a **String** or an **Array**.
        * **$num** ( default : 0 ) : Indicate the level of group.
        * **$excludedMetadata** ( default : null ) : Indicate metadata you won't to read. Can be a **String** or an **Array**.
     * return : array | null
     * examples :
         * Read all metadata :
         
                 $metadataManager->reader()->readWithPrefix();
                     
         * Read all XMP Dublin Core metadata except the XMP Dublin Core subject with the group level 1:
             
                 $metadataManager->reader()->readWithPrefix("XMP-dc:all", 1, "XMP-dc:Subject");
                 
                 /* Result */
                 array (size=5)
                   'SourceFile' => string 'data/images/photo1.jpg' (length=22)
                   'XMP-dc:Rights' => string 'CC-by-sa' (length=8)
                   'XMP-dc:Description' => string 'Western part of the abandoned Packard Automotive Plant in Detroit, Michigan.' (length=76)
                   'XMP-dc:Creator' => string 'Albert Duce' (length=11)
                   'XMP-dc:Title' => string 'Abandoned Packard Automobile Factory, Detroit' (length=45)
                 
    
### WriterTasker

### EraserTasker

The EraserTasker allow to remove file's metadata. Only one feature is available at this moment :

* **remove** ($targetedMetadata, $excludedMetadata, $overwrite) :
    * description : Allow to remove all or some file's metadata.
    * params :
        * **$targetedMetadata** ( default : "all" ) : Indicate metadata you want to remove. Can be a **String** or an **Array**.
        * **$excludedMetadata** ( default : null ) : Indicate metadata you won't to remove. Can be a **String** or an **Array**.
        * **$overwrite** ( default : true ) : Indicate if the deletion must be applied to the original file or to a copy. It's corresponds to the use of the exiftool option -overwrite_original.
    * return : array with potentially the command return message and the success of the command as boolean : true or false.
    * examples :
        * Remove all metadata :
    
                $metadataManager->eraser()->remove();
                
                /* Result */
                array (size=2)
                  'exiftoolMessage' => string '1 image files updated' (length=21)
                  'success' => boolean true
                
        * Remove all XMP Dublin Core metadata except the XMP Dublin Core title :
        
                $metadataManager->eraser()->remove("XMP-dc:all", "XMP-dc:Title");
                
                /* Result */
                array (size=2)
                  'exiftoolMessage' => string '1 image files updated' (length=21)
                  'success' => boolean true