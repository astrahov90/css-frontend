<?php

namespace core;

use core\traits\AuthorizedTrait;
use core\traits\CheckCSRFTrait;
use core\traits\MethodsCheckTrait;

abstract class Controller
{
    protected ?Model $model;

    use AuthorizedTrait, CheckCSRFTrait, MethodsCheckTrait;

    const ACTION_PREFIX = 'action_';

    function action_index()
    {
    }

    function setModel($class_name, $dbh)
    {
        $this->model = ModelFactory::build($class_name, $dbh);
    }

    function runAction(string $action_name, ?int $object_id = null)
    {
        $fullActionName = self::ACTION_PREFIX.$action_name;
        if (method_exists($this, $fullActionName)) {
            if (isset($object_id))
                return $this->$fullActionName($object_id);
            else
                return $this->$fullActionName();
        }

        return false;
    }
}