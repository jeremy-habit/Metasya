<?php

require_once "class/MetadataManager.php";

$metaManager = new MetadataManager("data/images/photo1.jpg");

var_dump($metaManager->getMetadata());
/*

$metaManager->add(
  [
    'FileName' => 'coucou.pdf'
  ]
);

var_dump($metaManager->get('FileName'));*/

