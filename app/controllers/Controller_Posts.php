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

        $redisCache = RedisCache::getInstance();
        $redisKey = 'posts-getList-'.$offset.($newest?'newest':'best').($authorId?"-".$authorId:'');

        $cacheItem = $redisCache->getItem($redisKey);
        $result = json_decode($cacheItem->get());
        if (!$result)
        {
            $result = $this->model->getList(compact(['offset','newest','authorId']));

            $cacheItem->set(json_encode($result));
            $cacheItem->expiresAfter(60);
            $redisCache->save($cacheItem);
        }

        /*$result = $this->model->getList(compact(['offset','newest','authorId']));*/

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

        $redisCache = RedisCache::getInstance();
        $redisKey = 'posts-get-'.$postId;

        $cacheItem = $redisCache->getItem($redisKey);
        $result = json_decode($cacheItem->get());
        if (!$result)
        {
            $result = $this->model->get($postId);

            $cacheItem->set(json_encode($result));
            $cacheItem->expiresAfter(3600);
            $redisCache->save($cacheItem);
        }

        /*$result = $this->model->get($postId);*/
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
        $this->checkCSRFToken();
        $this->checkAuthorization();

        $title = $_REQUEST["title"];
        $body = $_REQUEST["body"];
        $authorId = $_SESSION["userId"];

        $postId = $this->model->create(compact(['title','body','authorId']));

        $result = $this->model->get($postId);

        $redisCache = RedisCache::getInstance();
        $keysFounded = $redisCache->scanItems('*posts-getList-*');
        if ($keysFounded)
            $redisCache->deleteItems($keysFounded);

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

        $redisCache = RedisCache::getInstance();
        $keysFounded = $redisCache->scanItems('*posts-getRating-*');
        if ($keysFounded)
            $redisCache->deleteItems($keysFounded);

        header('Content-Type: application/json; charset=utf-8');
        die(json_encode($result));
    }

    function action_rating($postId)
    {
        $this->checkMethodGet();

        $redisCache = RedisCache::getInstance();
        $redisKey = 'posts-getRating-'.$postId;

        $cacheItem = $redisCache->getItem($redisKey);
        $result = json_decode($cacheItem->get());
        if (!$result)
        {
            $result = $this->model->getPostRating($postId);

            $cacheItem->set(json_encode($result));
            $cacheItem->expiresAfter(3600);
            $redisCache->save($cacheItem);
        }

        /*$result = $this->model->getPostRating($postId);*/

        header('Content-Type: application/json; charset=utf-8');
        die(json_encode($result));
    }
}
