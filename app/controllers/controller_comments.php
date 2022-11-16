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

        $offset = 0;
        if (isset($_REQUEST["offset"]))
            $offset = $_REQUEST["offset"];

        $result = $this->model->getCommentsByPost($offset, $_REQUEST["id"]);

        header('Content-Type: application/json; charset=utf-8');
        die(json_encode($result));
    }

    function action_addCommentToPost()
    {
        $postId = $_REQUEST["id"];
        $comment = $_REQUEST["comment"];
        $author = $_SESSION["userId"];

        $this->model->addCommentToPost($postId, $author, $comment);

        $url = ((!empty($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . "/posts/" . $postId . "/comments/";
        header("Location: " . $url);
    }

}