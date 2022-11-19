<?php

namespace core;

abstract class Controller
{

    public $model;
    public $view;

    const ACTION_PREFIX = 'action_';

    function __construct($view)
    {
        $this->view = $view;
    }

    function action_index()
    {
    }

    function setModel($class_name)
    {
        $this->model = ModelFactory::build($class_name);
    }

    function runAction($action_name, $object_id)
    {
        $fullActionName = self::ACTION_PREFIX.$action_name;
        if (method_exists($this, $fullActionName)) {
            if (isset($object_id))
                $this->$fullActionName($object_id);
            else
                $this->$fullActionName();
        } else {
            return false;
        }
    }
}