<?php

namespace db;

use core\Config;

class PostgresqlConnection implements IDBConnection
{
    private $pdo;

    public function connect(): \PDO
    {
        if ($this->pdo == null) {
            $dsn = 'pgsql:host='.Config::getDBHost().';port='.Config::getDBPort().';dbname='.Config::getDBName().';';
            $this->pdo = new \PDO($dsn,Config::getDBUsername(),Config::getDBPassword());
            $this->pdo->exec("SET search_path TO public;");
        }
        return $this->pdo;
    }
}