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

        header('Content-Type: application/json; charset=utf-8');
        die(json_encode($result));
    }

    function action_getPost()
    {
        $this->checkMethodGet();

        $postId = $_REQUEST["postId"]??null;

        if ($postId == null)
        {
            header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad request');
            die();
        }

        $redisKey = 'posts-get-'.$postId;
        $callback = function () use ($postId){
            return $this->model->get($postId);
        };

        $result = RedisCache::getCacheOrDoRequest($redisKey, $callback, 3600);

        header('Content-Type: application/json; charset=utf-8');
        die(json_encode($result));
    }

    function action_index($model_id = null)
    {
        echo $this->twig->render(str_replace('\\', DIRECTORY_SEPARATOR,'posts.html'));
    }

    function action_comments($postId)
    {
        $data = [];
        $data["post"] = $this->model->get($postId);

        $_SESSION['token'] = md5(uniqid(mt_rand(), true));
        $this->twig->addGlobal('session', $_SESSION);
        echo $this->twig->render(str_replace('\\', DIRECTORY_SEPARATOR,'comments.html'), $data);
    }

    function action_addPost()
    {
        $this->checkMethodPost();
        $this->checkAuthorization();
        $this->checkCSRFToken();

        $title = $_REQUEST["title"];
        $body = $_REQUEST["body"];
        $authorId = $_SESSION["userId"];

        $postId = $this->model->create(compact(['title','body','authorId']));

        $result = $this->model->get($postId);

        RedisCache::clearCache('*posts-getList-*');

        header('Content-Type: application/json; charset=utf-8');
        die(json_encode($result));
    }

    function action_like($postId)
    {
        $this->checkMethodPost();
        $this->checkAuthorization();
        $this->checkCSRFToken();

        $author = $_SESSION["userId"];
        $this->postSetRating($author, $postId, true);
    }

    function action_dislike($postId)
    {
        $this->checkMethodPost();
        $this->checkAuthorization();
        $this->checkCSRFToken();

        $author = $_SESSION["userId"];
        $this->postSetRating($author, $postId, false);

    }

    private function postSetRating($authorId, $postId, $like)
    {
        $ratingSet = $this->model->checkPostRating($authorId, $postId);

        $result = [];
        if ($ratingSet) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad request');
            die("Оценка уже выставлена");
        }
        else {
            $this->model->addPostLike($authorId, $postId, $like);
        }

        RedisCache::clearCache('*posts-*');

        header('Content-Type: application/json; charset=utf-8');
        die(json_encode($result));
    }

    function action_rating($postId)
    {
        $this->checkMethodGet();

        $redisKey = 'posts-getRating-'.$postId;
        $callback = function () use ($postId){
            return $this->model->getPostRating($postId);
        };

        $result = RedisCache::getCacheOrDoRequest($redisKey, $callback, 3600);

        header('Content-Type: application/json; charset=utf-8');
        die(json_encode($result));
    }
}
