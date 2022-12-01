<?php

namespace core;

class Config
{
    public static function getDBType():string
    {
        return $_ENV['DB_TYPE']??'sqlite';
    }

    public static function getDBHost():string
    {
        return $_ENV['DB_HOST']??($_ENV['DB_TYPE']==='sqlite'?'app/db/sqlite.db':'localhost');
    }

    public static function getDBName():string
    {
        return $_ENV['DB_NAME']??'postgres';
    }

    public static function getDBPort():string
    {
        return $_ENV['DB_PORT']??'5432';
    }

    public static function getDBUsername():string
    {
        return $_ENV['DB_USER']??'postgres';
    }

    public static function getDBPassword():string
    {
        return $_ENV['DB_PASSWORD']??'12345678';
    }

    public static function getRedisHost():string
    {
        return $_ENV['REDIS_HOST']??'localhost';
    }

    public static function getRedisPort():string
    {
        return $_ENV['REDIS_PORT']??'6379';
    }

    public static function getRedisPassword():string
    {
        return $_ENV['REDIS_PASSWORD']??'';
    }
}