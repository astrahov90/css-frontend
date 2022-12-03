<?php

namespace controllers;

use core\App;

class Controller_Contacts extends \core\Controller
{
    function action_index()
    {
        return App::$app->render('contacts.html');
    }
}