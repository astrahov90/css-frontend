<?php

use core\interfaces\IModelGet;

class Model_Profile extends \core\Model implements IModelGet
{

    const QUERY_BASE = "SELECT
        * from user WHERE id=:id";

    protected function postWork($elem){
        $elem["created_at"] = date("d.m.Y", $elem["created_at"]);
        return $elem;
    }

    public function get($userId)
    {

        $queryString = self::QUERY_BASE;
        $query = $this->pdo->prepare($queryString);

        $query->bindParam("id", $userId);

        $query->execute();

        $info = $query->fetch(\PDO::FETCH_ASSOC);
        $info = $this->postWork($info);

        return $info;
    }

    public function updateUserAvatar($id, $uploadedFile)
    {

        $queryString = "UPDATE user SET iconPath=:iconPath, updated_at=:updated_at WHERE id=:id";

        $query = $this->pdo->prepare($queryString);

        $query->bindParam("id", $id);
        $query->bindParam("iconPath", $uploadedFile);
        $query->bindValue("updated_at", time());

        $query->execute();

        return true;
    }

}