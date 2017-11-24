<?php

use MagicMonkey\Metasya\MetadataHelper;

require_once 'Autoloader.php';
Autoloader::register();

/* ### MAIN */

var_dump(PHP_OS);
var_dump($_SERVER['HTTP_USER_AGENT']);

$metadataHelper = new MetadataHelper("data/images/photo1.jpg");

var_dump($metadataHelper->getUsedExiftoolVersion());

var_dump($metadataHelper->getExiftoolVersionsInfo());

var_dump($metadataHelper->read());