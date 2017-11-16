<?php

use src\MetadataManager;


/* ### CONFIG */

define('DS', DIRECTORY_SEPARATOR); // meilleur portabilité sur les différents systeme.
define('ROOT', dirname(__FILE__) . DS); // pour se simplifier la vie


/* ### LOADER */

require_once 'Autoloader.php';
Autoloader::register();


/* ### MAIN */

$metadataManager = new MetadataManager("data/images/photo1.jpg");


var_dump($metadataManager->read("XMP-dc:all"));