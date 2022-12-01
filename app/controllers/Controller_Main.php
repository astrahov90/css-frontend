<?php

namespace controllers;

class Controller_Main extends \core\Controller
{
    function action_index()
    {
        $data = [];

        $templateName = 'posts.html';
        $this->setCSRFToken();
        $this->twig->addGlobal('session', $_SESSION);

        if (isset($_REQUEST["newPost"])) {
            $data['newPost'] = true;
            $templateName = 'new-post.html';
        } else {
            if (isset($_REQUEST["newest"]))
                $data['newest'] = true;
            else
                $data['best'] = true;
        }

        echo $this->twig->render(str_replace('\\', DIRECTORY_SEPARATOR,$templateName), $data);
    }

    function action_error()
    {
        echo $this->twig->render(str_replace('\\', DIRECTORY_SEPARATOR,'error.html'));
    }
}