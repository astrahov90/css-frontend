<?php

namespace controllers;
class Controller_Authors extends \core\Controller
{
    function action_getAuthor()
    {
        $this->checkMethodGet();

        $authorId = $_REQUEST["authorId"]??null;

        if ($authorId == null)
        {
            http_response_code(400);
            die();
        }

        $result = $this->model->get($authorId);
        header('Content-Type: application/json; charset=utf-8');
        die(json_encode($result));
    }

    function action_index()
    {
        $data = [];
        $data['authors'] = true;

        $this->view->generate('app/views/authors_view.php', "template_view.php", $data);
    }

    function action_getUsers()
    {
        $this->checkMethodGet();

        $offset = $_REQUEST['offset']??0;

        $result = $this->model->getList(compact(['offset']));

        header('Content-Type: application/json; charset=utf-8');
        die(json_encode($result));
    }

    function action_posts($authorId)
    {
        $this->view->generate('app/views/authors_posts_view.php', "template_view.php", compact(['authorId']));
    }
}