<?php

namespace controllers;

class Controller_About extends \core\Controller
{
    function action_index()
    {
        echo $this->twig->render(str_replace('\\', DIRECTORY_SEPARATOR,'about.html'));
    }
}