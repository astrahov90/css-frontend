<?php

namespace db;

use core\Config;

class MockConnection implements IDBConnection
{
    private $pdo;

    public function connect(): \PDO
    {
        if ($this->pdo == null) {
            $this->pdo = new \PDO("sqlite::memory:");
        }
        return $this->pdo;
    }
}