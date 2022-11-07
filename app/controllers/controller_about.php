<?php

class Controller_About extends \core\Controller
{
    function action_index()
    {
        $this->view->generate('app/views/about_view.php', "template_view.php");
    }
}