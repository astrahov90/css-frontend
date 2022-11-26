<?php

use core\Router;

error_reporting(E_ERROR | E_PARSE);

require str_replace("\\", DIRECTORY_SEPARATOR, __DIR__.'/../vendor/autoload.php');

$dotenv = Dotenv\Dotenv::createImmutable(str_replace("\\", DIRECTORY_SEPARATOR,__DIR__.'/../'));
$dotenv->load();

session_start();

Router::start();
