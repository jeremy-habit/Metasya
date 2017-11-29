<?php

use MagicMonkey\Metasya\MetadataHelper;
use MagicMonkey\Metasya\Schema\Schema;

require_once 'Autoloader.php';
Autoloader::register();

/* ### MAIN */


$metadataHelper = new MetadataHelper("data/images/photo1.jpg", false);

/* création d'un schéma */
$xmpDc = $metadataHelper->createSchema("myXmp", "XMP-dc");

/* ajout de properties */
$xmpDc->addProperty(new \MagicMonkey\Metasya\Schema\Property("Title", "text"));
$xmpDc->addProperty(new \MagicMonkey\Metasya\Schema\Property("Description", "text"));
$xmpDc->addProperty(new \MagicMonkey\Metasya\Schema\Property("Creator", "text", "Xmp-marvel"));

/*var_dump($xmpDc->buildTargetedMetadata());*/

/* Lecture à partir d'un schéma */
/*var_dump($metadataHelper->read($xmpDc));*/


/*var_dump($metadataHelper->getExiftoolVersionsInfo());*/


/* Lecture à partir d'un shortcut */
/* step 1)  Ajout d'un shéma aux metadataHelper*//*
var_dump($metadataHelper->addSchema($xmpDc));*/



