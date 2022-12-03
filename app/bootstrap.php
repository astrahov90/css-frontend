<?php

use core\RedisCache;
use core\Router;

error_reporting(E_ERROR | E_PARSE);

require str_replace('\\', DIRECTORY_SEPARATOR, __DIR__.'/../vendor/autoload.php');

$dotenv = Dotenv\Dotenv::createImmutable(str_replace('\\', DIRECTORY_SEPARATOR,__DIR__.'/../'));
$dotenv->load();

$redisCache = new RedisCache(new Redis());
$redisCache->connect();

RedisCache::setInstance($redisCache);

session_start();
