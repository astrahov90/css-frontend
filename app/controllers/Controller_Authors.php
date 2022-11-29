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
            http_response_code(400);
            die();
        }

        $redisCache = RedisCache::getInstance();
        $redisKey = 'authors-get-'.$authorId;

        $cacheItem = $redisCache->getItem($redisKey);
        $result = json_decode($cacheItem->get());
        if (!$result)
        {
            $result = $this->model->get($authorId);

            $cacheItem->set(json_encode($result));
            $cacheItem->expiresAfter(60);
            $redisCache->save($cacheItem);
        }

        /*$result = $this->model->get($authorId);*/
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

        $redisCache = RedisCache::getInstance();
        $redisKey = 'authors-getList-'.$offset;

        $cacheItem = $redisCache->getItem($redisKey);
        $result = json_decode($cacheItem->get());
        if (!$result)
        {
            $result = $this->model->getList(compact(['offset']));

            $cacheItem->set(json_encode($result));
            $cacheItem->expiresAfter(60);
            $redisCache->save($cacheItem);
        }

        /*$result = $this->model->getList(compact(['offset']));*/

        header('Content-Type: application/json; charset=utf-8');
        die(json_encode($result));
    }

    function action_posts($authorId)
    {
        echo $this->twig->render(str_replace('\\', DIRECTORY_SEPARATOR,'authors-posts.html'), compact(['authorId']));
    }
}