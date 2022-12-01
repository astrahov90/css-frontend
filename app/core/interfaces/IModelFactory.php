<?php

namespace core\interfaces;

interface IModelFactory
{
    public static function build(string $model_name, \PDO $dbh);
}