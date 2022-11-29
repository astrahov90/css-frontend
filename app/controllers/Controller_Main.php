<?php

namespace controllers;

class Controller_Main extends \core\Controller
{
    function action_index()
    {
        $data = [];

        if (isset($_REQUEST["newPost"])) {
            $data['newPost'] = true;
            echo $this->twig->render(str_replace('\\', DIRECTORY_SEPARATOR,'new-post.html'), $data);
        } else {
            if (isset($_REQUEST["newest"]))
                $data['newest'] = true;
            else
                $data['best'] = true;

            echo $this->twig->render(str_replace('\\', DIRECTORY_SEPARATOR,'posts.html'), $data);
        }

    }

    function action_error()
    {
        echo $this->twig->render(str_replace('\\', DIRECTORY_SEPARATOR,'error.html'));
    }
}