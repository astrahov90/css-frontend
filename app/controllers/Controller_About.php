<?php

namespace controllers;

use core\App;

class Controller_About extends \core\Controller
{
    function action_index()
    {
        return App::$app->render('about.html');
    }
}