<?php

class Controller_Authors extends \core\Controller
{
    function __construct($pdo)
    {
        parent::__construct();
        $this->model = new Model_Authors($pdo);
        $this->view = new \core\View();
    }

    function action_index($model_id=null)
    {
        if (isset($model_id))
        {
            $result = $this->model->getAuthorInfo($model_id);
            header('Content-Type: application/json; charset=utf-8');
            die(json_encode($result));
        }

        $data = [];
        $data['authors'] = true;

        $this->view->generate('app/views/authors_view.php', "template_view.php", $data);
    }

    function action_getUsers(){

        $offset = 0;
        if (isset($_REQUEST["offset"]))
            $offset = $_REQUEST["offset"];

        $result = $this->model->get_data($offset);

        header('Content-Type: application/json; charset=utf-8');
        die(json_encode($result));
    }

    function action_posts($authorId)
    {
        $data = [];
        $data["author"] = $this->model->getAuthorInfo($authorId);

        $this->view->generate('app/views/authors_posts_view.php', "template_view.php", $data);
    }
}