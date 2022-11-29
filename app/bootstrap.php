<?php

error_reporting(E_ERROR | E_PARSE);

require str_replace('\\', DIRECTORY_SEPARATOR, __DIR__.'/../vendor/autoload.php');

$dotenv = Dotenv\Dotenv::createImmutable(str_replace('\\', DIRECTORY_SEPARATOR,__DIR__.'/../'));
$dotenv->load();

$loader = new \Twig\Loader\FilesystemLoader(str_replace('\\', DIRECTORY_SEPARATOR,__DIR__.'/views/'));
$twig = new \Twig\Environment($loader, [
    'cache' => str_replace('\\', DIRECTORY_SEPARATOR,__DIR__.'/views/cache'),
]);

session_start();

\core\Router::start();
