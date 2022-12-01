<?php

namespace db;

use core\Config;

class SQLiteConnection implements IDBConnection
{
    private $pdo;

    public function connect(): \PDO
    {
        if ($this->pdo == null) {
            $this->pdo = new \PDO("sqlite:" . Config::getDBHost());
            if (!filesize(Config::getDBHost()))
                throw new \Exception('There are no tables in the database!');
        }
        return $this->pdo;
    }
}