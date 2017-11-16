<?php

/* ### LOADER */

use MagicMonkey\PHPMetadataManager\MetadataManager;

require_once 'Autoloader.php';
Autoloader::register();

/* ### MAIN */

$metadataManager = new MetadataManager("data/images/photo1.jpg");
var_dump($metadataManager->read("XMP-dc:all"));