<?php

namespace controllers;

class Controller_Contacts extends \core\Controller
{
    function action_index()
    {
        $this->view->generate('app/views/contacts_view.php', "template_view.php");
    }
}