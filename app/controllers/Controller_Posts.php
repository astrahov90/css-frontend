<?php

namespace controllers;

use core\RedisCache;

class Controller_Posts extends \core\Controller
{
    function action_getPosts()
    {
        $this->checkMethodGet();

        $offset = $_REQUEST["offset"]??0;
        $newest = isset($_REQUEST["newest"]);
        $authorId = $_REQUEST["authorId"]??null;

        $redisKey = 'posts-getList-'.$offset.($newest?'newest':'best').($authorId?"-".$authorId:'');
        $callback = function () use ($offset, $newest, $authorId){
            return $this->model->getList(compact(['offset','newest','authorId']));
        };

        $result = RedisCache::getCacheOrDoRequest($redisKey, $callback, 60);

        return json_encode($result);
    }

    function action_getPost(): ?string
    {
        $this->checkMethodGet();

        $postId = $_REQUEST["postId"]??null;

        if ($postId === null)
        {
            header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad request');
            return null;
        }

        $redisKey = 'posts-get-'.$postId;
        $callback = function () use ($postId){
            return $this->model->get($postId);
        };

        $result = RedisCache::getCacheOrDoRequest($redisKey, $callback, 3600);

        return json_encode($result);
    }

    function action_index($model_id = null)
    {

    }

    function action_comments($postId)
    {
        $data = [];
        $data["post"] = $this->model->get($postId);

        $this->setCSRFToken();
        $this->twig->addGlobal('session', $_SESSION);
        echo $this->twig->render(str_replace('\\', DIRECTORY_SEPARATOR,'comments.html'), $data);
    }

    function action_addPost()
    {
        $this->checkMethodPost();
        $this->checkAuthorization();
        $this->checkCSRFToken();

        $title = $_POST["title"];
        $body = $_POST["body"];
        $authorId = $_SESSION["userId"];

        $postId = $this->model->create(compact(['title','body','authorId']));

        $result = $this->model->get($postId);

        RedisCache::clearCache('*posts-getList-*');

        return json_encode($result);
    }

    function action_like($postId): void
    {
        $this->checkMethodPost();
        $this->checkAuthorization();
        $this->checkCSRFToken();

        $author = $_SESSION["userId"];
        $this->postSetRating($author, $postId, true);
    }

    function action_dislike($postId): void
    {
        $this->checkMethodPost();
        $this->checkAuthorization();
        $this->checkCSRFToken();

        $author = $_SESSION["userId"];
        $this->postSetRating($author, $postId, false);
    }

    private function postSetRating($authorId, $postId, $like): void
    {
        $ratingSet = $this->model->checkPostRating($authorId, $postId);

        if ($ratingSet) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad request');
            echo "Оценка уже выставлена";

            return;
        }
        else {
            $this->model->addPostLike($authorId, $postId, $like);
        }

        RedisCache::clearCache('*posts-*');
    }

    function action_rating($postId)
    {
        $this->checkMethodGet();

        $redisKey = 'posts-getRating-'.$postId;
        $callback = function () use ($postId){
            return $this->model->getPostRating($postId);
        };

        $result = ['rating' => RedisCache::getCacheOrDoRequest($redisKey, $callback, 3600)?:0];

        return json_encode($result);
    }
}
