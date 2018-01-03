<?php


use MagicMonkey\Metasya\MetadataHelper;
use MagicMonkey\Metasya\Schema\Schema;
use MagicMonkey\Metasya\Schema\Property;

require_once 'Autoloader.php';
Autoloader::register();

/* ### MAIN */


$metadataHelper = new MetadataHelper("data/images/photo1.jpg", false);
$metadataHelper->setFilePath("data/images/paysage.jpg");


$titleProperty = new Property("Title");
$creatorProperty = new Property("Creator", "", "Mr nobody");
$descriptionProperty = new Property("Description");
$sizeProperty = new Property("FileSize", "System");

$mySchemaObject = new Schema("shortcut", "XMP-dc", "Schema to get some metadata");
$mySchemaObject->addProperty($titleProperty);
$mySchemaObject->addProperty($creatorProperty);
$mySchemaObject->addProperty($descriptionProperty);
$mySchemaObject->addProperty($sizeProperty);

var_dump($mySchemaObject->buildTargetedMetadata());
$mySchemaObject->removeProperty($creatorProperty);
var_dump($mySchemaObject->getProperties());