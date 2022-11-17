<?php

use core\interfaces\IModelCreate;
use core\interfaces\IModelGetList;

class Model_Comments extends \core\Model implements IModelGetList, IModelCreate
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

        $offset = $args['offset'];
        $postId = $args['postId'];

        $queryString = self::QUERY_BASE;
        $query = $this->pdo->prepare($queryString);
        $query->bindParam("postId", $postId);
        $query->bindParam("offset", $offset);
        $query->execute();

        $comments = $query->fetchAll(\PDO::FETCH_ASSOC);

        $comments = array_map([$this,'postWork'], $comments);

        return $comments;
    }

    public function getCount(iterable $args)
    {
        $postId = $args['postId'];

        $query = "SELECT COUNT(id) FROM comments WHERE post_id=:postId";
        $commentsCount = $this->pdo->prepare($query);
        $commentsCount->bindParam("postId", $postId);
        $commentsCount->execute();
        $commentsCount = $commentsCount->fetchColumn();

        return $commentsCount;
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
        $postId = $args['postId'];
        $body = $args['body'];
        $authorId = $args['authorId'];

        $queryString = "INSERT INTO comments (post_id, author_id, body, created_at) VALUES (:postId, :authorId, :body, :created_at)";

        $query = $this->pdo->prepare($queryString);

        $query->bindParam("postId", $postId);
        $query->bindParam("authorId", $authorId);
        $query->bindParam("body", $body);
        $query->bindValue("created_at", time());

        $query->execute();

        return true;
    }
}