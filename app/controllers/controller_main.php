<?php

class Controller_Main extends \core\Controller
{
    function action_index()
    {
        $data = [];

        if (isset($_REQUEST["newPost"])){
            $data['newPost'] = true;
            $this->view->generate('app/views/newpost_view.php', "template_view.php", $data);
            die();
        }
        else {
            if (isset($_REQUEST["newest"]))
            $data['newest'] = true;
        else
            $data['best'] = true;

            $this->view->generate('app/views/posts_view.php', "template_view.php", $data);
        }

    }
}