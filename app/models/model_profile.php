<?php

namespace models;

use core\interfaces\IModelGet;
use core\interfaces\IModelPostWork;

class Model_Profile extends \core\Model implements IModelGet, IModelPostWork
{

    const QUERY_BASE = "SELECT
        * from user WHERE id=:id";

    public function postWork($elem){
        $elem["created_at"] = date("d.m.Y", $elem["created_at"]);
        return $elem;
    }

    public function get($userId)
    {
        $params = [];
        $params["id"] = $userId;

        $queryString = self::QUERY_BASE;

        $result = $this->getOne($queryString, $params);

        $result = $this->postWork($result);

        return $result;
    }

    public function updateUserAvatar($id, $iconPath)
    {
        $params = [];
        $params["id"] = $id;
        $params["iconPath"] = $iconPath;
        $params["updated_at"] = time();

        $queryString = "UPDATE user SET iconPath=:iconPath, updated_at=:updated_at WHERE id=:id";

        $this->updateOne($queryString, $params);
    }

}