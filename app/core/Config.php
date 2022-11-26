<?php

namespace core;

class Config
{
    public static function get_db_host():string
    {
        return $_ENV['DB_HOST']??'app/db/sqlite.db';
    }
}