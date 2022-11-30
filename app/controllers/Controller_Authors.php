<?php

namespace controllers;
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
            die();
        }

        $redisKey = 'authors-get-'.$authorId;
        $callback = function () use ($authorId){
            return $this->model->get($authorId);
        };

        $result = RedisCache::getCacheOrDoRequest($redisKey, $callback, 3600);

        header('Content-Type: application/json; charset=utf-8');
        die(json_encode($result));
    }

    function action_index()
    {
        $data = [];
        $data['authors'] = true;

        echo $this->twig->render(str_replace('\\', DIRECTORY_SEPARATOR,'authors.html'), $data);
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

        header('Content-Type: application/json; charset=utf-8');
        die(json_encode($result));
    }

    function action_posts($authorId)
    {
        echo $this->twig->render(str_replace('\\', DIRECTORY_SEPARATOR,'authors-posts.html'), compact(['authorId']));
    }
}