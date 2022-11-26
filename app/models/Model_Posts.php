<?php

namespace models;

use core\interfaces\IModelCreate;
use core\interfaces\IModelGet;
use core\interfaces\IModelPostWork;
use core\traits\GetListTrait;

class Model_Posts extends \core\Model implements IModelGet, IModelCreate, IModelPostWork
{
    use GetListTrait;

    const QUERY_BASE = "SELECT
        posts.id, posts.title, posts.body, posts.created_at, user.id as authorId, user.username as authorName, user.iconPath, IFNULL(posts_likes.likes_count,0) as likes_count, IFNULL(comments.comments_count,0) as comments_count
        FROM posts
        LEFT JOIN (SELECT post_id, SUM(rating) as likes_count FROM posts_likes GROUP BY post_id) as posts_likes ON posts.id=posts_likes.post_id
        LEFT JOIN (SELECT post_id, COUNT(id) as comments_count FROM comments GROUP BY post_id) as comments ON posts.id=comments.post_id
        INNER JOIN user ON posts.author_id=user.id WHERE TRUE
        ORDER BY :order
        LIMIT 5 OFFSET :offset";

    public function postWork($elem)
    {
        $elem["created_at"] = date("d.m.Y H:i:s", $elem["created_at"]);
        $elem["comments_count_text"] = $elem["comments_count"] . " " . $this->getCommentSuffix($elem["comments_count"]);
        $elem["body"] = $this->bbCodeDecode($elem["body"]);
        return $elem;
    }

    public function getPage(iterable $args)
    {

        $params = [];

        $offset = $args['offset'];
        $newest = $args['newest'];
        $authorId = $args['authorId'];

        $order = "likes_count DESC, posts.created_at DESC";

        if ($newest) {
            $order = "posts.created_at DESC";
        }

        $queryString = str_replace(":order", $order, self::QUERY_BASE);
        if ($authorId) {
            $queryString = str_replace("WHERE TRUE", "WHERE posts.author_id=:authorId", $queryString);
            $params["authorId"] = $authorId;
        }

        $params["offset"] = $offset;

        $result = $this->getAll($queryString, $params);

        $result = array_map(array($this, 'postWork'), $result);

        return $result;
    }

    public function getCount(iterable $args)
    {
        $params = [];

        $queryString = "SELECT COUNT(id) FROM posts WHERE TRUE";
        if ($args['authorId']) {
            $queryString = str_replace("WHERE TRUE", "WHERE author_id=:authorId", $queryString);
            $params["authorId"] = $args["authorId"];
        }

        $result = $this->getValue($queryString, $params);

        return $result;
    }

    public function get($postId)
    {
        $params = [];
        $params["offset"] = 0;
        $params["postId"] = $postId;

        $queryString = str_replace(":order", "TRUE", self::QUERY_BASE);
        $queryString = str_replace("WHERE TRUE", "WHERE posts.id=:postId", $queryString);

        $result = $this->getOne($queryString, $params);

        $result = $this->postWork($result);

        return $result;
    }

    public function create(iterable $args)
    {
        $params = [];
        $params["title"] = $args['title'];
        $params["body"] = $args['title'];
        $params["author_id"] = $args['authorId'];
        $params["created_at"] = time();

        $queryString = "INSERT INTO posts (author_id, title, body, created_at) VALUES (:author_id, :title, :body, :created_at);";

        $recordId = $this->createOne($queryString, $params);

        return $recordId;
    }

    public function addPostLike($authorId, $postId, $likeSign)
    {
        $queryString = "INSERT INTO posts_likes (author_id, post_id, rating) VALUES (:author_id, :post_id, :rating);";

        $params = [];
        $params["post_id"] = $postId;
        $params["author_id"] = $authorId;
        $params["rating"] = ($likeSign ? 1 : -1);

        $this->createOne($queryString, $params);
    }

    public function getPostRating($postId)
    {
        $queryString = "SELECT SUM(rating) AS likes_count FROM posts_likes WHERE post_id=:post_id;";

        $params = [];
        $params["post_id"] = $postId;

        $result = $this->getValue($queryString, $params);

        return $result;
    }

    public function checkPostRating($authorId, $postId)
    {
        $queryString = "SELECT id FROM posts_likes WHERE author_id=:author_id AND post_id=:post_id;";

        $params = [];
        $params["post_id"] = $postId;
        $params["author_id"] = $authorId;

        $result = $this->getOne($queryString, $params);

        return !!$result;
    }

    private function getCommentSuffix($commentNum)
    {
        switch ($commentNum % 10) {
            case 1:
                switch ($commentNum) {
                    case 11:
                        return "комментариев";
                    default:
                        return "комментарий";
                }
            case 2:
            case 3:
            case 4:
                switch ($commentNum) {
                    case 12:
                    case 13:
                    case 14:
                        return "комментариев";
                    default:
                        return "комментария";
                }
            default:
                return "комментариев";
        }
    }

}