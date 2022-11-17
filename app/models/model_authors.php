<?php

use core\interfaces\IModelGet;
use core\interfaces\IModelGetList;

class Model_Authors extends \core\Model implements IModelGet, IModelGetList
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
        $offset = $args['offset'];

        $queryString = str_replace("WHERE TRUE", "", self::QUERY_BASE);

        $query = $this->pdo->prepare($queryString);

        $query->bindParam("offset", $offset);

        $query->execute();

        $authors = $query->fetchAll(\PDO::FETCH_ASSOC);

        $authors = array_map([$this,'postWork'], $authors);

        return $authors;
    }

    public function getCount(iterable $args)
    {

        $query = "SELECT 
        COUNT(DISTINCT posts.author_id) as authors_count
        FROM posts";
        $authorCount = $this->pdo->prepare($query);

        $authorCount->execute();
        $authorCount = $authorCount->fetchColumn();

        return $authorCount;
    }

    public function getList(iterable $args)
    {

        $result = [];
        $result["authors"] = $this->getPage($args);
        $result["totalCount"] = $this->getCount($args);
        $result["currentCount"] = $args['offset'] + count($result["authors"]);

        return $result;
    }

    public function get($authorId)
    {

        $queryString = str_replace("WHERE TRUE", "WHERE user.id=:id", self::QUERY_BASE);
        $query = $this->pdo->prepare($queryString);

        $query->bindParam("id", $authorId);
        $query->bindValue("offset", 0);

        $query->execute();

        $info = $query->fetch(\PDO::FETCH_ASSOC);
        $info = $this->postWork($info);

        return $info;
    }

}