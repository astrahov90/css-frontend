<?php

namespace core;

use core\interfaces\IModelFactory;

class ModelFactory implements IModelFactory
{
    const MODEL_PREFIX = 'models\Model_';

    public static function build($class_name) : ?Model
    {
        if (class_exists(self::MODEL_PREFIX.$class_name))
        {
            $dbh = (new \db\DBHandler(new \db\SQLiteConnection()))->connect();
            return new (self::MODEL_PREFIX.$class_name)($dbh);
        }

        return null;
    }
}