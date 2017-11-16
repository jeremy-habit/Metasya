<?php

define('DS', DIRECTORY_SEPARATOR); // meilleur portabilité sur les différents systeme.
define('ROOT', dirname(__FILE__) . DS); // pour se simplifier la vie
define('APP_BASEFILE', "src" . DS);

class Autoloader
{

  public static function register()
  {
    spl_autoload_register(array(__CLASS__, 'autoload'));
  }

  public static function autoload($class)
  {

    // on explose notre variable $class par \
    $parts = preg_split('#\\\#', $class);

    // on extrait le dernier element
    $className = array_pop($parts);

    // on créé le chemin vers la classe
    // on utilise DS car plus propre et meilleure portabilité entre les différents systèmes (windows/linux)

    $path = implode(DS, $parts);
    $file = $className . '.php';

    $filepath = ROOT . APP_BASEFILE . strtolower($path) . DS . $file;

    // var_dump($filepath); => C:\xampp\htdocs\Labs\Eloyas\app\tester\Test.php
    //
    require $filepath;
  }
}