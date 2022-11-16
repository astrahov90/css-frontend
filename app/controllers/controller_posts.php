<?php


class Controller_Posts extends \core\Controller
{
    function __construct($pdo)
    {
        parent::__construct();
        $this->model = new Model_Posts($pdo);
        $this->view = new \core\View();
    }

    function action_getPosts()
    {

        $offset = 0;
        if (isset($_REQUEST["offset"]))
            $offset = $_REQUEST["offset"];

        $newest = false;
        if (isset($_REQUEST["newest"]))
            $newest = true;

        if (isset($_REQUEST["authorId"])) {
            $result = $this->model->getByAuthor($offset, $_REQUEST["authorId"]);
        } else {
            $result = $this->model->get_data($offset, $newest);
        }

        header('Content-Type: application/json; charset=utf-8');
        die(json_encode($result));
    }


    function action_index($model_id = null)
    {
        if (isset($model_id)) {
            $result = $this->model->getPostInfo($model_id);
            header('Content-Type: application/json; charset=utf-8');
            die(json_encode($result));
        }

        $this->view->generate('app/views/post_view.php', "template_view.php");
    }

    function action_comments($postId)
    {
        $data = [];
        $data["post"] = $this->model->getPostInfo($postId);

        $this->view->generate('app/views/comments_view.php', "template_view.php", $data);
    }

    function action_getComments()
    {

        $offset = 0;
        if (isset($_REQUEST["offset"]))
            $offset = $_REQUEST["offset"];

        $result = $this->model->getCommentsByPost($offset, $_REQUEST["id"]);

        header('Content-Type: application/json; charset=utf-8');
        die(json_encode($result));
    }

    function action_addPost()
    {
        $title = $_REQUEST["title"];
        $text = $_REQUEST["text"];
        $author = $_SESSION["userId"];

        $postId = $this->model->addPost($author, $title, $text);

        $url = ((!empty($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . "/posts/" . $postId . "/comments/";
        header("Location: " . $url);
    }

    function action_postLike($postId)
    {
        $author = $_SESSION["userId"];
        $this->postSetRating($author, $postId, true);
    }

    function action_postDisLike($postId)
    {
        $author = $_SESSION["userId"];
        $this->postSetRating($author, $postId, false);

    }

    private function postSetRating($authorId, $postId, $like)
    {
        $ratingSet = $this->model->checkPostRating($authorId, $postId);

        $result = [];
        if ($ratingSet) {
            $result['error'] = "Оценка уже выставлена";
        } else {
            $ratingCount = $this->model->addPostLike($authorId, $postId, $like);
            $result['ratingCount'] = $ratingCount;
        }

        header('Content-Type: application/json; charset=utf-8');
        die(json_encode($result));
    }
}
