<?php

use MagicMonkey\Metasya\MetadataHelper;

require_once 'Autoloader.php';
Autoloader::register();

/* ### MAIN */

$metadataHelper = new MetadataHelper("data/images/photo1.jpg");

var_dump($metadataHelper->getExiftoolVersionsInfo());

var_dump($metadataHelper->read());