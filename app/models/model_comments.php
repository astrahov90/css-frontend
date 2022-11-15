<?php

class Model_Comments extends \core\Model {

    const QUERY_BASE = "SELECT
        comments.id, comments.post_id, comments.body, comments.created_at, user.id as authorId, user.username as authorName, user.iconPath
        FROM comments
        INNER JOIN user ON comments.author_id=user.id WHERE comments.post_id=:id
        ORDER BY comments.created_at
        LIMIT 5 OFFSET :offset";

    private function getPage($offset=0, $postId=null){

        $queryString = self::QUERY_BASE;
        $queryString = str_replace("WHERE TRUE","WHERE posts.author_id=:id",$queryString);
        $query = $this->pdo->prepare($queryString);
        $query->bindParam("id", $postId);

        $query->bindParam("offset", $offset);

        $query->execute();

        $posts = $query->fetchAll(\PDO::FETCH_ASSOC);

        $posts = array_map(function ($elem){
            $elem["created_at"] = date("d.m.Y H:i:s", $elem["created_at"]);
            $elem["body"] = $this->bbCodeDecode($elem["body"]);
            return $elem;
        },$posts);

        return $posts;
    }

    private function getCount($post_id) {

        $query = "SELECT COUNT(id) FROM comments WHERE post_id=:id";
        $CommentsCount = $this->pdo->prepare($query);
        $CommentsCount->bindParam("id", $post_id);
        $CommentsCount->execute();
        $CommentsCount = $CommentsCount->fetchColumn();

        return $CommentsCount;
    }

    public function getCommentsByPost($offset=0, $post_id){

        $result = [];
        $result["comments"] = $this->getPage($offset,$post_id);
        $result["totalCount"] = $this->getCount($post_id);
        $result["currentCount"] = $offset + count($result["comments"]);

        return $result;
    }

    public function addCommentToPost($post_id, $author_id, $body){
        $queryString = "INSERT INTO comments (post_id, author_id, body, created_at) VALUES (:post_id, :author_id, :body, :created_at)";

        $query = $this->pdo->prepare($queryString);

        $query->bindParam("post_id", $post_id);
        $query->bindParam("author_id", $author_id);
        $query->bindParam("body", $body);
        $query->bindValue("created_at", time());

        $query->execute();

        return true;
    }
}