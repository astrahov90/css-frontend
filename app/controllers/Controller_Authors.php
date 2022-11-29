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

        echo $this->twig->render(str_replace('\\', DIRECTORY_SEPARATOR,'authors.html'), $data);
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
        echo $this->twig->render(str_replace('\\', DIRECTORY_SEPARATOR,'authors-posts.html'), compact(['authorId']));
    }
}