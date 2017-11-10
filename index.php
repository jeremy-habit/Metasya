<?php

use PHPMetadataManager\MetadataManager;


/* ### CONFIG */

define('DS', DIRECTORY_SEPARATOR); // meilleur portabilité sur les différents systeme.
define('ROOT', dirname(__FILE__) . DS); // pour se simplifier la vie


/* ### LOADER */

require_once 'Autoloader.php';
Autoloader::register();


/* ### MAIN */

$metaManager = MetadataManager::getInstance("data/images/photo1.jpg");

var_dump($metaManager->reader()->readByGroup(0, "Creator"));

var_dump($metaManager->writer());