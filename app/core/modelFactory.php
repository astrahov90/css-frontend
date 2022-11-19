<?php

namespace core;

use core\interfaces\IModelFactory;

class ModelFactory implements IModelFactory
{

    public function getModel($model_name, $dbh) : ?Model
    {
        if (class_exists($model_name))
            return new $model_name($dbh);

        return null;
    }
}