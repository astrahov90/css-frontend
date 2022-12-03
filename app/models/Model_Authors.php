<?php

namespace models;

use core\interfaces\IModelGet;
use core\interfaces\IModelPostWork;
use core\traits\GetListTrait;

class Model_Authors extends \core\Model implements IModelGet, IModelPostWork
{
    use GetListTrait;

    const QUERY_BASE = 'SELECT
        users.id as authorId, users.username as "authorName", users.iconpath as "iconPath", users.created_at, users.description, COUNT(posts.id) as posts_count
        FROM users
        LEFT JOIN posts ON users.id=posts.author_id WHERE TRUE
        GROUP BY users.id, users.username, users.iconpath, users.created_at, users.description
        ORDER BY users.username
        LIMIT 5 OFFSET :offset';

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
        COUNT(DISTINCT users.id) as authors_count
        FROM users";
        $result = $this->getValue($queryString);

        return $result;
    }

    public function get($authorId)
    {
        $params = [];
        $params["id"] = $authorId;
        $params["offset"] = 0;

        $queryString = str_replace("WHERE TRUE", "WHERE users.id=:id", self::QUERY_BASE);

        $result = $this->getOne($queryString, $params);

        $result = $this->postWork($result);

        return $result;
    }

}