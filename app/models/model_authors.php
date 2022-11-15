<?php

class Model_Authors extends \core\Model {

    const QUERY_BASE = "SELECT
        user.id as authorId, user.username as authorName, user.iconPath, user.created_at, user.description, COUNT(posts.id) as posts_count
        FROM user
        LEFT JOIN posts ON user.id=posts.author_id :WHERE
        GROUP BY user.id, user.username, user.iconPath, user.created_at, user.description
        ORDER BY user.username
        LIMIT 5 OFFSET :offset";

    private function getPage($offset=0){

        $queryString = str_replace(":WHERE","",self::QUERY_BASE);

        $query = $this->pdo->prepare($queryString);

        $query->bindParam("offset", $offset);

        $query->execute();

        $authors = $query->fetchAll(\PDO::FETCH_ASSOC);

        $authors = array_map(function ($elem){
            $elem["created_at"] = date("d.m.Y", $elem["created_at"]);
            return $elem;
        },$authors);

        return $authors;
    }

    private function getCount($id=null) {

        $query = "SELECT 
        COUNT(DISTINCT posts.author_id) as authors_count
        FROM posts";
        $authorCount = $this->pdo->prepare($query);

        $authorCount->execute();
        $authorCount = $authorCount->fetchColumn();

        return $authorCount;
    }

    public function get_data($offset=0){

        $result = [];
        $result["authors"] = $this->getPage($offset);
        $result["totalCount"] = $this->getCount();
        $result["currentCount"] = $offset + count($result["authors"]);

        return $result;
    }

    public function getAuthorInfo($authorId){

        $queryString = str_replace(":WHERE","WHERE user.id=:id",self::QUERY_BASE);
        $query = $this->pdo->prepare($queryString);

        $query->bindParam("id", $authorId);
        $query->bindValue("offset", 0);

        $query->execute();

        $info = $query->fetch(\PDO::FETCH_ASSOC);
        $info["created_at"] = date("d.m.Y", $info["signDate"]);

        return $info;
    }

}