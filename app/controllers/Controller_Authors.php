<?php

namespace controllers;
use core\App;
use core\RedisCache;

class Controller_Authors extends \core\Controller
{
    function action_getAuthor()
    {
        $this->checkMethodGet();

        $authorId = $_REQUEST["authorId"]??null;

        if ($authorId == null)
        {
            header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad request');
            return;
        }

        $redisKey = 'authors-get-'.$authorId;
        $callback = function () use ($authorId){
            return $this->model->get($authorId);
        };

        $result = RedisCache::getCacheOrDoRequest($redisKey, $callback, 3600);

        return json_encode($result);
    }

    function action_index()
    {
        $data = [];
        $data['authors'] = true;

        return App::$app->render('authors.html', $data);
    }

    function action_getAuthors()
    {
        $this->checkMethodGet();

        $offset = $_REQUEST['offset']??0;

        $redisKey = 'authors-getList-'.$offset;
        $callback = function () use ($offset){
            return $this->model->getList(compact(['offset']));
        };

        $result = RedisCache::getCacheOrDoRequest($redisKey, $callback, 60);

        return json_encode($result);
    }

    function action_posts($authorId)
    {
        return App::$app->render('authors-posts.html', compact(['authorId']));
    }
}