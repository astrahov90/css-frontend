<?php
namespace db;

use \core\Config;

class SQLiteConnection {
    private $pdo;

    public function connect() {
        if ($this->pdo == null) {
            $this->pdo = new \PDO("sqlite:" . Config::PATH_TO_SQLITE_FILE);
            if (!filesize(Config::PATH_TO_SQLITE_FILE))
                throw new \Exception('There are no tables in the database!');
        }
        return $this->pdo;
    }
}