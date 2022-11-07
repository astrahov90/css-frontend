<?php

class Model_Comments extends \core\Model {

    const QUERY_BASE = "SELECT
        comments.id, comments.post, comments.text, comments.pubDate, users.id as authorId, users.name as authorName, users.iconPath
        FROM comments
        INNER JOIN users ON comments.author=users.id WHERE comments.post=:id
        ORDER BY pubDate
        LIMIT 5 OFFSET :offset";

    private function getPage($offset=0, $postId=null){

        $queryString = self::QUERY_BASE;
        $queryString = str_replace("WHERE TRUE","WHERE posts.author=:id",$queryString);
        $query = $this->pdo->prepare($queryString);
        $query->bindParam("id", $postId);

        $query->bindParam("offset", $offset);

        $query->execute();

        $posts = $query->fetchAll(\PDO::FETCH_ASSOC);

        $posts = array_map(function ($elem){
            $elem["pubDate"] = date("j.m.Y H:i:s", $elem["pubDate"]/1000);
            $elem["text"] = $this->bbCodeDecode($elem["text"]);
            return $elem;
        },$posts);

        return $posts;
    }

    private function getCount($postId) {

        $query = "SELECT COUNT(id) FROM comments WHERE post=:id";
        $CommentsCount = $this->pdo->prepare($query);
        $CommentsCount->bindParam("id", $postId);
        $CommentsCount->execute();
        $CommentsCount = $CommentsCount->fetchColumn();

        return $CommentsCount;
    }

    public function getCommentsByPost($offset=0, $postId){

        $result = [];
        $result["comments"] = $this->getPage($offset,$postId);
        $result["totalCount"] = $this->getCount($postId);
        $result["currentCount"] = $offset + count($result["comments"]);

        return $result;
    }

    public function addCommentToPost($postId, $authorId, $comment){
        $queryString = "INSERT INTO comments (post, author, text, pubDate) VALUES (:postId, :authorId, :comment, :time)";

        $query = $this->pdo->prepare($queryString);

        $query->bindParam("postId", $postId);
        $query->bindParam("authorId", $authorId);
        $query->bindParam("comment", $comment);

        $date = new DateTime(null);
        $query->bindValue("time", ($date->getTimestamp())*1000);

        $query->execute();

        return true;
    }
}