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
            header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad request');
            die();
        }

        $redisKey = 'comments-getList-'.$postId.'-'.$offset;
        $callback = function () use ($postId, $offset){
            return $this->model->getList(compact(['offset','postId']));
        };

        $result = RedisCache::getCacheOrDoRequest($redisKey, $callback, 60);

        header('Content-Type: application/json; charset=utf-8');
        die(json_encode($result));
    }

    function action_addCommentToPost()
    {
        $this->checkMethodPost();
        $this->checkAuthorization();
        $this->checkCSRFToken();

        $postId = $_REQUEST["postId"];
        $body = $_REQUEST["body"];
        $authorId = $_SESSION["userId"];

        $this->model->create(compact(['postId','body','authorId']));

        RedisCache::clearCache('*comments-getList-'.$postId.'*');

        die();
    }

}