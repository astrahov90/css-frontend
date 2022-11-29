<?php

namespace controllers;

class Controller_Contacts extends \core\Controller
{
    function action_index()
    {
        echo $this->twig->render(str_replace('\\', DIRECTORY_SEPARATOR,'contacts.html'));
    }
}