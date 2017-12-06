<?php

use MagicMonkey\Metasya\MetadataHelper;

require_once 'Autoloader.php';
Autoloader::register();

/* ### MAIN */

var_dump(\MagicMonkey\Metasya\Schema\SchemataManager::getInstance()->getSchemata());

/*$metadataHelper = new MetadataHelper("data/images/photo1.jpg", false);*/

/* READER TEST */

/*var_dump($metadataHelper->read());
var_dump($metadataHelper->readByGroup());
var_dump($metadataHelper->readWithPrefix());  */