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
            return;
        }

        $redisKey = 'comments-getList-'.$postId.'-'.$offset;
        $callback = function () use ($postId, $offset){
            return $this->model->getList(compact(['offset','postId']));
        };

        $result = RedisCache::getCacheOrDoRequest($redisKey, $callback, 60);

        return json_encode($result);
    }

    function action_addCommentToPost()
    {
        $this->checkMethodPost();
        $this->checkAuthorization();
        $this->checkCSRFToken();

        $postId = $_POST["postId"];
        $body = $_POST["body"];
        $authorId = $_SESSION["userId"];

        $commentId = $this->model->create(compact(['postId','body','authorId']));

        $result = $this->model->get($commentId);

        RedisCache::clearCache('*comments-getList-'.$postId.'*');

        return json_encode($result);
    }

}