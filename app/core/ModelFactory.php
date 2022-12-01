<?php

namespace core;

use core\interfaces\IModelFactory;
use db\DBHandler;
use db\MockConnection;
use db\PostgresqlConnection;
use db\SQLiteConnection;

class ModelFactory implements IModelFactory
{
    const MODEL_PREFIX = 'models\Model_';

    public static function build(string $model_name, \PDO $dbh) : ?Model
    {
        if (class_exists(self::MODEL_PREFIX.$model_name))
        {
            return new (self::MODEL_PREFIX.$model_name)($dbh);
        }

        return null;
    }

    public static function getDBH($dbType): \PDO
    {
        switch ($dbType){
            case 'sqlite':
                $connectionClass = new SQLiteConnection();
                break;
            case 'mock':
                $connectionClass = new MockConnection();
                break;
            case 'postgres':
                $connectionClass = new PostgresqlConnection();
                break;
            default:
                throw new \Exception('DB wrong type');
        }

        return (new DBHandler($connectionClass))->connect();
    }
}