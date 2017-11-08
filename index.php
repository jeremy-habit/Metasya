<?php

require_once "class/MetadataManager.php";

$metaManager = new MetadataManager("data/images/photo1.jpg");

var_dump($metaManager->readByGroup());

