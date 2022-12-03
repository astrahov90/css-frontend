<?php

namespace controllers;

use core\App;

class Controller_Main extends \core\Controller
{
    function action_index()
    {
        $data = [];

        $templateName = 'posts.html';

        if (isset($_REQUEST["newPost"])) {
            $data['newPost'] = true;
            $templateName = 'new-post.html';
        } else {
            if (isset($_REQUEST["newest"]))
                $data['newest'] = true;
            else
                $data['best'] = true;
        }

        $this->setCSRFToken();
        return App::$app->render($templateName, $data);
    }

    function action_error()
    {
        return App::$app->render('error.html');
    }
}