<?php


class Controller_Comments extends \core\Controller
{
    function __construct($pdo)
    {
        parent::__construct();
        $this->model = new Model_Comments($pdo);
        $this->view = new \core\View();
    }

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
        $postId = $_REQUEST["postId"];
        $body = $_REQUEST["body"];
        $authorId = $_SESSION["userId"];

        $this->model->create(compact(['postId','body','authorId']));

        $url = ((!empty($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . "/posts/" . $postId . "/comments/";
        header("Location: " . $url);
    }

}