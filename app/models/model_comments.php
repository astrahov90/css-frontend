<?php

namespace models;

use core\interfaces\IModelCreate;
use core\interfaces\IModelGetList;
use core\interfaces\IModelPostWork;

class Model_Comments extends \core\Model implements IModelGetList, IModelCreate, IModelPostWork
{

    const QUERY_BASE = "SELECT
        comments.id, comments.post_id, comments.body, comments.created_at, user.id as authorId, user.username as authorName, user.iconPath
        FROM comments
        INNER JOIN user ON comments.author_id=user.id WHERE comments.post_id=:postId
        ORDER BY comments.created_at
        LIMIT 5 OFFSET :offset";

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

    public function getList(iterable $args)
    {
        $result = [];
        $result["comments"] = $this->getPage($args);
        $result["totalCount"] = $this->getCount($args);
        $result["currentCount"] = $args['offset'] + count($result["comments"]);

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
}