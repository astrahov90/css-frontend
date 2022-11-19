<?php

namespace core;

class Controller
{

    public $model;
    public $view;


    function __construct($dbh = null)
    {
        $this->view = new View();
    }

    function action_index()
    {
    }
}