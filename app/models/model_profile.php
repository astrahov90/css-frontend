<?php

class Model_Profile extends \core\Model {

    const QUERY_BASE = "SELECT
        * from user WHERE id=:id";

    public function getUserInfo($userId){

        $queryString = self::QUERY_BASE;
        $query = $this->pdo->prepare($queryString);

        $query->bindParam("id", $userId);

        $query->execute();

        $info = $query->fetch(\PDO::FETCH_ASSOC);
        $info["created_at"] = date("d.m.Y", $info["created_at"]/1000);

        return $info;
    }

    public function updateUserAvatar($id, $uploadedFile){

        $queryString = "UPDATE user SET iconPath=:iconPath, updated_at=:updated_at WHERE id=:id";

        $query = $this->pdo->prepare($queryString);

        $query->bindParam("id", $id);
        $query->bindParam("iconPath", $uploadedFile);
        $query->bindValue("updated_at", time());

        $query->execute();

        return true;
    }

}