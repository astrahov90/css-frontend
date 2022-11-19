<?php

namespace db;

class DBHandler
{
    private $pdo;

    private $dbConnection;

    public function __construct(IDBConnection $dbConnection)
    {
        $this->dbConnection = $dbConnection;
    }

    public function connect()
    {
        if ($this->pdo == null)
            $this->pdo = $this->dbConnection->connect();
        return $this->pdo;
    }
}