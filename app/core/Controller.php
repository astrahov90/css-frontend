<?php

namespace core;

abstract class Controller
{

    protected $model;
    protected $view;
    protected $twig;

    const ACTION_PREFIX = 'action_';

    function __construct($view, $twig)
    {
        $this->view = $view;
        $this->twig = $twig;
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

    protected function checkMethodGet()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET')
        {
            http_response_code(405);
            die();
        }
    }

    protected function checkMethodPost():void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        {
            http_response_code(405);
            die();
        }
    }
}