<?php

use core\interfaces\IModelCreate;
use core\interfaces\IModelGet;
use core\interfaces\IModelGetList;

class Model_Posts extends \core\Model implements IModelGet, IModelCreate, IModelGetList
{

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

    public function getList(iterable $args)
    {
        $result = [];
        $result["posts"] = $this->getPage($args);
        $result["totalCount"] = $this->getCount($args);
        $result["currentCount"] = $args['offset'] + count($result["posts"]);

        return $result;
    }

    public function getPage(iterable $args)
    {

        $offset = $args['offset'];
        $newest = $args['newest'];
        $authorId = $args['authorId'];

        $order = "likes_count DESC, posts.created_at DESC";

        if ($newest) {
            $order = "posts.created_at DESC";
        }

        $queryString = str_replace(":order", $order, self::QUERY_BASE);
        $query = $this->pdo->prepare($queryString);
        if ($authorId) {
            $queryString = str_replace("WHERE TRUE", "WHERE posts.author_id=:authorId", $queryString);
            $query = $this->pdo->prepare($queryString);
            $query->bindParam("authorId", $authorId);
        }

        $query->bindParam("offset", $offset);

        $query->execute();

        $posts = $query->fetchAll(\PDO::FETCH_ASSOC);

        $posts = array_map(array($this, 'postWork'), $posts);

        return $posts;
    }

    public function getCount(iterable $args)
    {

        $queryString = "SELECT COUNT(id) FROM posts WHERE TRUE";
        $postsCount = $this->pdo->prepare($queryString);

        if ($args['authorId']) {
            $queryString = str_replace("WHERE TRUE", "WHERE author_id=:authorId", $queryString);
            $postsCount = $this->pdo->prepare($queryString);
            $postsCount->bindParam("authorId", $args['authorId']);
        }
        $postsCount->execute();
        $postsCount = $postsCount->fetchColumn();

        return $postsCount;
    }

    public function get($postId)
    {

        $queryString = str_replace(":order", "TRUE", self::QUERY_BASE);
        $queryString = str_replace("WHERE TRUE", "WHERE posts.id=:postId", $queryString);
        $query = $this->pdo->prepare($queryString);

        $query->bindParam("postId", $postId);
        $query->bindValue("offset", 0);

        $query->execute();

        $info = $query->fetch(\PDO::FETCH_ASSOC);
        $info = $this->postWork($info);

        return $info;
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

    public function create(iterable $args)
    {
        $title = $args['title'];
        $body = $args['body'];
        $authorId = $args['authorId'];

        $queryString = "INSERT INTO posts (author_id, title, body, created_at) VALUES (:author_id, :title, :body, :created_at);";

        $query = $this->pdo->prepare($queryString);

        $query->bindParam("title", $title);
        $query->bindParam("author_id", $authorId);
        $query->bindParam("body", $body);
        $query->bindValue("created_at", time());

        $query->execute();

        $postId = $this->pdo->lastInsertId();

        return $postId;
    }

    public function addPostLike($authorId, $postId, $likeSign)
    {
        $queryString = "INSERT INTO posts_likes (author_id, post_id, rating) VALUES (:author_id, :post_id, :rating);";

        $query = $this->pdo->prepare($queryString);

        $query->bindParam("post_id", $postId);
        $query->bindParam("author_id", $authorId);
        $query->bindValue("rating", ($likeSign ? 1 : -1));
        $query->execute();

        $queryString = "SELECT SUM(rating) AS likes_count FROM posts_likes WHERE post_id=:post_id;";

        $query = $this->pdo->prepare($queryString);

        $query->bindParam("post_id", $postId);
        $query->execute();

        $row = $query->fetch(\PDO::FETCH_ASSOC);

        return $row['likes_count'];
    }

    public function checkPostRating($authorId, $postId)
    {
        $queryString = "SELECT id FROM posts_likes WHERE author_id=:author_id AND post_id=:post_id;";

        $query = $this->pdo->prepare($queryString);

        $query->bindParam("post_id", $postId);
        $query->bindParam("author_id", $authorId);

        $query->execute();

        $row = $query->fetch(\PDO::FETCH_ASSOC);

        return !!$row;
    }

}