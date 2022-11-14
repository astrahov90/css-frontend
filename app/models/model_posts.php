<?php

class Model_Posts extends \core\Model {

    const QUERY_BASE = "SELECT
        posts.id, posts.title, posts.text, posts.pubDate, users.id as authorId, users.name as authorName, users.iconPath, IFNULL(posts_likes.likes_count,0) as likes_count, IFNULL(comments.comments_count,0) as comments_count
        FROM posts
        LEFT JOIN (SELECT post, SUM(rating) as likes_count FROM posts_likes GROUP BY post) as posts_likes ON posts.id=posts_likes.post
        LEFT JOIN (SELECT post, COUNT(id) as comments_count FROM comments GROUP BY post) as comments ON posts.id=comments.post
        INNER JOIN users ON posts.author=users.id WHERE TRUE
        ORDER BY :order
        LIMIT 5 OFFSET :offset";

    private function getPage($offset=0, $newest=false, $id=null){

        $order = "likes_count DESC, pubDate DESC";

        if ($newest)
        {
            $order = "pubDate DESC";
        }

        $queryString = str_replace(":order",$order,self::QUERY_BASE);
        $query = $this->pdo->prepare($queryString);
        if ($id){
            $queryString = str_replace("WHERE TRUE","WHERE posts.author=:id",$queryString);
            $query = $this->pdo->prepare($queryString);
            $query->bindParam("id", $id);
        }

        $query->bindParam("offset", $offset);

        $query->execute();

        $posts = $query->fetchAll(\PDO::FETCH_ASSOC);

        $posts = array_map(function ($elem){
            $elem["pubDate"] = date("d.m.Y H:i:s", $elem["pubDate"]);
            $elem["comments_count_text"] = $elem["comments_count"]." ".$this->getCommentSuffix($elem["comments_count"]);
            $elem["text"] = $this->bbCodeDecode($elem["text"]);
            return $elem;
        },$posts);

        return $posts;
    }

    private function getCount($id=null) {

        $query = "SELECT COUNT(id) FROM posts";
        $postsCount = $this->pdo->prepare($query);

        if ($id)
        {
            $query = "SELECT COUNT(id) FROM posts WHERE author=:id";
            $postsCount = $this->pdo->prepare($query);
            $postsCount->bindParam("id", $id);
        }
        $postsCount->execute();
        $postsCount = $postsCount->fetchColumn();

        return $postsCount;
    }

    public function get_data($offset=0, $newest=false){

        $result = [];
        $result["posts"] = $this->getPage($offset, $newest);
        $result["totalCount"] = $this->getCount();
        $result["currentCount"] = $offset + count($result["posts"]);

        return $result;
    }

    public function getByAuthor($offset=0, $id){
        $result = [];
        $result["posts"] = $this->getPage($offset, true,$id);
        $result["totalCount"] = $this->getCount($id);
        $result["currentCount"] = $offset + count($result["posts"]);

        return $result;
    }

    public function getPostInfo($postId){

        $queryString = str_replace(":order","TRUE",self::QUERY_BASE);
        $queryString = str_replace("WHERE TRUE","WHERE posts.id=:id",$queryString);
        $query = $this->pdo->prepare($queryString);

        $query->bindParam("id", $postId);
        $query->bindValue("offset", 0);

        $query->execute();

        $info = $query->fetch(\PDO::FETCH_ASSOC);
        $info["pubDate"] = date("d.m.Y H:i:s", $info["pubDate"]);
        $info["comments_count_text"] = $info["comments_count"]." ".$this->getCommentSuffix($info["comments_count"]);
        $info["text"] = $this->bbCodeDecode($info["text"]);

        return $info;
    }

    private function getCommentSuffix($commentNum) {
        switch ($commentNum%10){
            case 1:
                switch ($commentNum){
                    case 11: return "комментариев";
                    default: return "комментарий";
                }
            case 2:
            case 3:
            case 4:
                switch ($commentNum){
                    case 12:
                    case 13:
                    case 14: return "комментариев";
                    default: return "комментария";
                }
            default: return "комментариев";
        }
    }

    public function addPost($authorId, $title, $text){
        $queryString = "INSERT INTO posts (author, title, text, pubDate) VALUES (:authorId, :title, :text, :pubDate);";

        $query = $this->pdo->prepare($queryString);

        $query->bindParam("title", $title);
        $query->bindParam("authorId", $authorId);
        $query->bindParam("text", $text);

        $query->bindValue("pubDate", time());

        $query->execute();

        $postId = $this->pdo->lastInsertId();

        return $postId;
    }

    public function addPostLike($authorId, $postId, $like){
        $queryString = "INSERT INTO posts_likes (author, post, rating) VALUES (:authorId, :postId, :rating);";

        $query = $this->pdo->prepare($queryString);

        $query->bindParam("postId", $postId);
        $query->bindParam("authorId", $authorId);
        $query->bindValue("rating", ($like?1:-1));
        $query->execute();

        $queryString = "SELECT SUM(rating) AS likes_count FROM posts_likes WHERE post=:postId;";

        $query = $this->pdo->prepare($queryString);

        $query->bindParam("postId", $postId);
        $query->execute();

        $row = $query->fetch(\PDO::FETCH_ASSOC);

        return $row['likes_count'];
    }

    public function checkPostRating($authorId, $postId){
        $queryString = "SELECT id FROM posts_likes WHERE author=:authorId AND post=:postId;";

        $query = $this->pdo->prepare($queryString);

        $query->bindParam("postId", $postId);
        $query->bindParam("authorId", $authorId);

        $query->execute();

        $row = $query->fetch(\PDO::FETCH_ASSOC);

        return !!$row;
    }

}