<?php

class Model_Authors extends \core\Model {

    const QUERY_BASE = "SELECT
        users.id as authorId, users.name as authorName, users.iconPath, users.signDate, users.description, COUNT(posts.id) as posts_count
        FROM posts
        INNER JOIN users ON posts.author=users.id :WHERE
        GROUP BY users.id, users.name, users.iconPath, users.signDate, users.description
        ORDER BY users.name
        LIMIT 5 OFFSET :offset";

    private function getPage($offset=0){

        $queryString = str_replace(":WHERE","",self::QUERY_BASE);

        $query = $this->pdo->prepare($queryString);

        $query->bindParam("offset", $offset);

        $query->execute();

        $authors = $query->fetchAll(\PDO::FETCH_ASSOC);

        $authors = array_map(function ($elem){
            $elem["signDate"] = date("j.m.Y", $elem["signDate"]/1000);
            return $elem;
        },$authors);

        return $authors;
    }

    private function getCount($id=null) {

        $query = "SELECT 
        COUNT(DISTINCT posts.author) as authors_count
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

        $queryString = str_replace(":WHERE","WHERE users.id=:id",self::QUERY_BASE);
        $query = $this->pdo->prepare($queryString);

        $query->bindParam("id", $authorId);
        $query->bindValue("offset", 0);

        $query->execute();

        $info = $query->fetch(\PDO::FETCH_ASSOC);
        $info["signDate"] = date("j.m.Y", $info["signDate"]/1000);

        return $info;
    }

}