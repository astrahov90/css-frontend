<?php

namespace core;

abstract class Controller
{

    public $model;
    public $view;

    function __construct($view)
    {
        $this->view = $view;
    }

    function action_index()
    {
    }

    function setModel($model_name, $dbh)
    {
        $this->model = (new ModelFactory())->getModel($model_name, $dbh);
    }

    function runAction($action_name, $object_id)
    {
        if (method_exists($this, $action_name)) {
            if (isset($object_id))
                $this->$action_name($object_id);
            else
                $this->$action_name();
        } else {
            return false;
        }
    }
}