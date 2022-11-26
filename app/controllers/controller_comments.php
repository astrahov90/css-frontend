<?php

namespace  controllers;

class Controller_Comments extends \core\Controller
{
    function action_getCommentsByPost()
    {

        $offset = $_REQUEST['offset']??0;
        $postId = $_REQUEST['postId']??null;

        if ($postId == null)
        {
            http_response_code(400);
            die();
        }

        $result = $this->model->getList(compact(['offset','postId']));

        header('Content-Type: application/json; charset=utf-8');
        die(json_encode($result));
    }

    function action_addCommentToPost()
    {
        if (!isset($_SESSION['isAuthorized']) || !$_SESSION['isAuthorized'])
        {
            http_response_code(403);
            die();
        }

        $postId = $_REQUEST["postId"];
        $body = $_REQUEST["body"];
        $authorId = $_SESSION["userId"];

        $this->model->create(compact(['postId','body','authorId']));

        die();
    }

}