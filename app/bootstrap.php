<?php

use \core\Router;

error_reporting(E_ERROR | E_PARSE);

session_start();

spl_autoload_register(function ($className) {
    $path = __DIR__ . '/' . $className . '.php';
    $path = str_replace("\\", DIRECTORY_SEPARATOR, $path);
    if (is_file($path)) {
        require_once $path;
    }
});

Router::start();
