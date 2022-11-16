<?php

class Model_Posts extends \core\Model
{

    const QUERY_BASE = "SELECT
        posts.id, posts.title, posts.body, posts.created_at, user.id as authorId, user.username as authorName, user.iconPath, IFNULL(posts_likes.likes_count,0) as likes_count, IFNULL(comments.comments_count,0) as comments_count
        FROM posts
        LEFT JOIN (SELECT post_id, SUM(rating) as likes_count FROM posts_likes GROUP BY post_id) as posts_likes ON posts.id=posts_likes.post_id
        LEFT JOIN (SELECT post_id, COUNT(id) as comments_count FROM comments GROUP BY post_id) as comments ON posts.id=comments.post_id
        INNER JOIN user ON posts.author_id=user.id WHERE TRUE
        ORDER BY :order
        LIMIT 5 OFFSET :offset";

    private function getPage($offset = 0, $newest = false, $id = null)
    {

        $order = "likes_count DESC, posts.created_at DESC";

        if ($newest) {
            $order = "posts.created_at DESC";
        }

        $queryString = str_replace(":order", $order, self::QUERY_BASE);
        $query = $this->pdo->prepare($queryString);
        if ($id) {
            $queryString = str_replace("WHERE TRUE", "WHERE posts.author_id=:id", $queryString);
            $query = $this->pdo->prepare($queryString);
            $query->bindParam("id", $id);
        }

        $query->bindParam("offset", $offset);

        $query->execute();

        $posts = $query->fetchAll(\PDO::FETCH_ASSOC);

        $posts = array_map(function ($elem) {
            $elem["created_at"] = date("d.m.Y H:i:s", $elem["created_at"]);
            $elem["comments_count_text"] = $elem["comments_count"] . " " . $this->getCommentSuffix($elem["comments_count"]);
            $elem["body"] = $this->bbCodeDecode($elem["body"]);
            return $elem;
        }, $posts);

        return $posts;
    }

    private function getCount($id = null)
    {

        $query = "SELECT COUNT(id) FROM posts";
        $postsCount = $this->pdo->prepare($query);

        if ($id) {
            $query = "SELECT COUNT(id) FROM posts WHERE author_id=:id";
            $postsCount = $this->pdo->prepare($query);
            $postsCount->bindParam("id", $id);
        }
        $postsCount->execute();
        $postsCount = $postsCount->fetchColumn();

        return $postsCount;
    }

    public function get_data($offset = 0, $newest = false)
    {

        $result = [];
        $result["posts"] = $this->getPage($offset, $newest);
        $result["totalCount"] = $this->getCount();
        $result["currentCount"] = $offset + count($result["posts"]);

        return $result;
    }

    public function getByAuthor($offset = 0, $id)
    {
        $result = [];
        $result["posts"] = $this->getPage($offset, true, $id);
        $result["totalCount"] = $this->getCount($id);
        $result["currentCount"] = $offset + count($result["posts"]);

        return $result;
    }

    public function getPostInfo($postId)
    {

        $queryString = str_replace(":order", "TRUE", self::QUERY_BASE);
        $queryString = str_replace("WHERE TRUE", "WHERE posts.id=:id", $queryString);
        $query = $this->pdo->prepare($queryString);

        $query->bindParam("id", $postId);
        $query->bindValue("offset", 0);

        $query->execute();

        $info = $query->fetch(\PDO::FETCH_ASSOC);
        $info["created_at"] = date("d.m.Y H:i:s", $info["created_at"]);
        $info["comments_count_text"] = $info["comments_count"] . " " . $this->getCommentSuffix($info["comments_count"]);
        $info["body"] = $this->bbCodeDecode($info["body"]);

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

    public function addPost($author_id, $title, $body)
    {
        $queryString = "INSERT INTO posts (author_id, title, body, created_at) VALUES (:author_id, :title, :body, :created_at);";

        $query = $this->pdo->prepare($queryString);

        $query->bindParam("title", $title);
        $query->bindParam("author_id", $author_id);
        $query->bindParam("body", $body);
        $query->bindValue("created_at", time());

        $query->execute();

        $postId = $this->pdo->lastInsertId();

        return $postId;
    }

    public function addPostLike($author_id, $post_id, $like)
    {
        $queryString = "INSERT INTO posts_likes (author_id, post_id, rating) VALUES (:author_id, :post_id, :rating);";

        $query = $this->pdo->prepare($queryString);

        $query->bindParam("post_id", $post_id);
        $query->bindParam("author_id", $author_id);
        $query->bindValue("rating", ($like ? 1 : -1));
        $query->execute();

        $queryString = "SELECT SUM(rating) AS likes_count FROM posts_likes WHERE post_id=:post_id;";

        $query = $this->pdo->prepare($queryString);

        $query->bindParam("post_id", $post_id);
        $query->execute();

        $row = $query->fetch(\PDO::FETCH_ASSOC);

        return $row['likes_count'];
    }

    public function checkPostRating($author_id, $post_id)
    {
        $queryString = "SELECT id FROM posts_likes WHERE author_id=:author_id AND post_id=:post_id;";

        $query = $this->pdo->prepare($queryString);

        $query->bindParam("post_id", $post_id);
        $query->bindParam("author_id", $author_id);

        $query->execute();

        $row = $query->fetch(\PDO::FETCH_ASSOC);

        return !!$row;
    }

}