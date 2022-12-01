<?php

namespace models;

use core\interfaces\IModelCreate;
use core\interfaces\IModelPostWork;
use core\traits\GetListTrait;

class Model_Comments extends \core\Model implements IModelCreate, IModelPostWork
{

    use GetListTrait;

    const QUERY_BASE = 'SELECT
        comments.id, comments.post_id, comments.body, comments.created_at, users.id as "authorId",
        users.username as "authorName", users.iconpath as "iconPath"
        FROM comments
        INNER JOIN users ON comments.author_id=users.id WHERE comments.post_id=:postId
        ORDER BY comments.created_at
        LIMIT 5 OFFSET :offset';

    public function postWork($elem)
    {
        $elem["created_at"] = date("d.m.Y H:i:s", $elem["created_at"]);
        $elem["body"] = $this->bbCodeDecode($elem["body"]);
        return $elem;
    }

    public function getPage(iterable $args)
    {
        $params = [];
        $params["offset"] = $args["offset"];
        $params["postId"] = $args["postId"];

        $queryString = self::QUERY_BASE;

        $result = $this->getAll($queryString, $params);

        $result = array_map(array($this, 'postWork'), $result);

        return $result;
    }

    public function getCount(iterable $args)
    {
        $params = [];
        $params["postId"] = $args["postId"];

        $queryString = "SELECT COUNT(id) FROM comments WHERE post_id=:postId";

        $result = $this->getValue($queryString, $params);

        return $result;
    }

    public function create(iterable $args)
    {
        $params = [];
        $params["post_id"] = $args['postId'];
        $params["body"] = $args['body'];
        $params["author_id"] = $args['authorId'];
        $params["created_at"] = time();

        $queryString = "INSERT INTO comments (post_id, author_id, body, created_at) VALUES (:post_id, :author_id, :body, :created_at)";

        $recordId = $this->createOne($queryString, $params);

        return $recordId;
    }

    public function get($commentId)
    {
        $params = [];
        $params["offset"] = 0;
        $params["commentId"] = $commentId;

        $queryString = str_replace("WHERE comments.post_id=:postId", "WHERE comments.id=:commentId", self::QUERY_BASE);

        $result = $this->getOne($queryString, $params);

        $result = $this->postWork($result);

        return $result;
    }
}