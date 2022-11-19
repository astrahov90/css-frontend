<?php

namespace models;

use core\interfaces\IModelGet;
use core\interfaces\IModelGetList;
use core\interfaces\IModelPostWork;

class Model_Authors extends \core\Model implements IModelGet, IModelGetList, IModelPostWork
{

    const QUERY_BASE = "SELECT
        user.id as authorId, user.username as authorName, user.iconPath, user.created_at, user.description, COUNT(posts.id) as posts_count
        FROM user
        LEFT JOIN posts ON user.id=posts.author_id WHERE TRUE
        GROUP BY user.id, user.username, user.iconPath, user.created_at, user.description
        ORDER BY user.username
        LIMIT 5 OFFSET :offset";

    public function postWork($elem)
    {
        $elem["created_at"] = date("d.m.Y", $elem["created_at"]);

        return $elem;
    }

    public function getPage(iterable $args)
    {
        $params = [];
        $params["offset"] = $args['offset'];

        $queryString = str_replace("WHERE TRUE", "", self::QUERY_BASE);

        $result = $this->getAll($queryString, $params);

        $result = array_map(array($this, 'postWork'), $result);

        return $result;
    }

    public function getCount(iterable $args)
    {
        $queryString = "SELECT 
        COUNT(DISTINCT posts.author_id) as authors_count
        FROM posts";
        $result = $this->getValue($queryString);

        return $result;
    }

    public function getList(iterable $args)
    {
        return parent::getList($args);
    }

    public function get($authorId)
    {
        $params = [];
        $params["id"] = $authorId;
        $params["offset"] = 0;

        $queryString = str_replace("WHERE TRUE", "WHERE user.id=:id", self::QUERY_BASE);

        $result = $this->getOne($queryString, $params);

        $result = $this->postWork($result);

        return $result;
    }

}