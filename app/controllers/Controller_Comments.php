<?php

namespace  controllers;

use core\RedisCache;

class Controller_Comments extends \core\Controller
{
    function action_getCommentsByPost()
    {
        $this->checkMethodGet();

        $offset = $_REQUEST['offset']??0;
        $postId = $_REQUEST['postId']??null;

        if ($postId == null)
        {
            http_response_code(400);
            die();
        }

        $redisCache = RedisCache::getInstance();
        $redisKey = 'comments-getList-'.$postId.'-'.$offset;

        $cacheItem = $redisCache->getItem($redisKey);
        $result = json_decode($cacheItem->get());
        if (!$result)
        {
            $result = $this->model->getList(compact(['offset','postId']));

            $cacheItem->set(json_encode($result));
            $cacheItem->expiresAfter(60);
            $redisCache->save($cacheItem);
        }

        /*$result = $this->model->getList(compact(['offset','postId']));*/

        header('Content-Type: application/json; charset=utf-8');
        die(json_encode($result));
    }

    function action_addCommentToPost()
    {
        $this->checkAuthorization();
        $this->checkCSRFToken();
        $this->checkMethodPost();

        $postId = $_REQUEST["postId"];
        $body = $_REQUEST["body"];
        $authorId = $_SESSION["userId"];

        $this->model->create(compact(['postId','body','authorId']));

        $redisCache = RedisCache::getInstance();
        $keysFounded = $redisCache->scanItems('*comments-getList-'.$postId.'*');
        if ($keysFounded)
            $redisCache->deleteItems($keysFounded);

        die();
    }

}