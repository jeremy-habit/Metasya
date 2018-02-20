<?php


use MagicMonkey\Metasya\MetadataHelper;

require_once 'Autoloader.php';
Autoloader::register();

/* ### MAIN */


$metadataHelper = new MetadataHelper("data/images/photo1.jpg", false);

var_dump($metadataHelper->getSchemataManager()->getSchemaFromShortcut("XMP-DC"));
/*var_dump($metadataHelper->getSchemataManager()->get)*/

var_dump($metadataHelper->read(['XMP-DC']));
var_dump($metadataHelper->read());