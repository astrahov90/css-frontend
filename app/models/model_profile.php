<?php

class Model_Profile extends \core\Model {

    const QUERY_BASE = "SELECT
        * from users WHERE id=:id";

    public function getUserInfo($userId){

        $queryString = self::QUERY_BASE;
        $query = $this->pdo->prepare($queryString);

        $query->bindParam("id", $userId);

        $query->execute();

        $info = $query->fetch(\PDO::FETCH_ASSOC);
        $info["signDate"] = date("j.m.Y", $info["signDate"]/1000);

        return $info;
    }

    public function updateUserAvatar($userId, $uploadedFile){

        $queryString = "UPDATE users SET iconPath=:filePath WHERE id=:id";

        $query = $this->pdo->prepare($queryString);

        $query->bindParam("id", $userId);
        $query->bindParam("filePath", $uploadedFile);

        $query->execute();

        return true;
    }

}