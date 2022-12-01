<?php

namespace core;

class Config
{
    public static function get_db_host():string
    {
        return $_ENV['DB_HOST']??'app/db/sqlite.db';
    }

    public static function getDBType():string
    {
        return $_ENV['DB_TYPE']??'sqlite';
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