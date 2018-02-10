<?php


use MagicMonkey\Metasya\MetadataHelper;

require_once 'Autoloader.php';
Autoloader::register();

/* ### MAIN */


$metadataHelper = new MetadataHelper("data/images/photo1.jpg", false);
$metadataHelper->setFilePath("data/images/paysage.jpg");

var_dump($metadataHelper->getSchemataManager()->getSchemaFromShortcut("cosmos")->getMetadata());

$meta = new MagicMonkey\Metasya\Schema\Metadata\Metadata("", "", "");
var_dump($metadataHelper->write([$meta => "dd"]));

/*var_dump($metadataHelper->readByGroup(["cosmos", "bonjour"],2));*/

/*
$metadataHelper->getSchemataManager()->setUserSchemataFolderPath("coucou/test", true);


$titleProperty = new Property("Title");
$creatorProperty = new Property("Creator", null, "Mr nobody");
$descriptionProperty = new Property("Description");
$sizeProperty = new Property("FileSize", "System");

$mySchemaObject = new Schema("shortcut", "XMP-dc", "Schema to get some metadata");
$mySchemaObject->addProperty($titleProperty);
$mySchemaObject->addProperty($creatorProperty);
$mySchemaObject->addProperty($descriptionProperty);
$mySchemaObject->addProperty($sizeProperty);


$userxmp = $metadataHelper->getSchemataManager()->getSchemaFromShortcut("USER-xmp");
var_dump($userxmp);
var_dump($userxmp->getProperties());
$userxmp->removeProperty(0);
var_dump($metadataHelper->getSchemataManager()->getSchemaFromShortcut("USER-xmp"));*/
