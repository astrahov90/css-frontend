<?php

namespace controllers;

class Controller_Posts extends \core\Controller
{
    function action_getPosts()
    {
        $this->checkMethodGet();

        $offset = $_REQUEST["offset"]??0;
        $newest = isset($_REQUEST["newest"]);
        $authorId = $_REQUEST["authorId"]??null;

        $result = $this->model->getList(compact(['offset','newest','authorId']));

        header('Content-Type: application/json; charset=utf-8');
        die(json_encode($result));
    }

    function action_getPost()
    {
        $this->checkMethodGet();

        $postId = $_REQUEST["postId"]??null;

        if ($postId == null)
        {
            http_response_code(400);
            die();
        }

        $result = $this->model->get($postId);
        header('Content-Type: application/json; charset=utf-8');
        die(json_encode($result));
    }


    function action_index($model_id = null)
    {
        $this->view->generate('app/views/post_view.php', "template_view.php");
    }

    function action_comments($postId)
    {
        $data = [];
        $data["post"] = $this->model->get($postId);

        $this->view->generate('app/views/comments_view.php', "template_view.php", $data);
    }

    function action_getComments()
    {
        $this->checkMethodGet();

        $offset = 0;
        if (isset($_REQUEST["offset"]))
            $offset = $_REQUEST["offset"];

        $result = $this->model->getCommentsByPost($offset, $_REQUEST["id"]);

        header('Content-Type: application/json; charset=utf-8');
        die(json_encode($result));
    }

    function action_addPost()
    {
        $this->checkMethodPost();
        $this->checkCSRFToken();
        $this->checkAuthorization();

        $title = $_REQUEST["title"];
        $body = $_REQUEST["body"];
        $authorId = $_SESSION["userId"];

        $postId = $this->model->create(compact(['title','body','authorId']));

        $result = $this->model->get($postId);
        header('Content-Type: application/json; charset=utf-8');
        die(json_encode($result));
    }

    function action_like($postId)
    {
        $this->checkMethodPost();
        $this->checkCSRFToken();
        $this->checkAuthorization();

        $author = $_SESSION["userId"];
        $this->postSetRating($author, $postId, true);
    }

    function action_dislike($postId)
    {
        $this->checkMethodPost();
        $this->checkCSRFToken();
        $this->checkAuthorization();

        $author = $_SESSION["userId"];
        $this->postSetRating($author, $postId, false);

    }

    private function postSetRating($authorId, $postId, $like)
    {
        $ratingSet = $this->model->checkPostRating($authorId, $postId);

        $result = [];
        if ($ratingSet) {
            $result['error'] = "Оценка уже выставлена";
            http_response_code(400);
        }
        else {
            $this->model->addPostLike($authorId, $postId, $like);
        }

        header('Content-Type: application/json; charset=utf-8');
        die(json_encode($result));
    }

    function action_rating($postId)
    {
        $this->checkMethodGet();

        $ratingCount = $this->model->getPostRating($postId);

        header('Content-Type: application/json; charset=utf-8');
        die(json_encode($ratingCount));
    }
}
