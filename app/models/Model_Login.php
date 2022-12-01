<?php

namespace models;

use core\interfaces\IModelCreate;
use core\interfaces\IModelGet;

class Model_Login extends \core\Model implements IModelCreate, IModelGet
{
    const STATUS_ACTIVE = 10;

    const QUERY_BASE = "SELECT
        users.id, users.username, users.password_hash
        FROM users
        WHERE username=:username";

    public function get($username)
    {
        $params = [];
        $params["username"] = $username;

        $queryString = self::QUERY_BASE;

        $result = $this->getOne($queryString, $params);

        return $result;
    }

    public function create(iterable $args)
    {
        $params = [];
        $params["username"] = $args['username'];
        $params["auth_key"] = $this->getRandomHashKey();
        $params["password_hash"] = password_hash($args['password'], PASSWORD_DEFAULT);
        $params["password_reset_token"] = $this->getRandomHashKey() . '_' . time();
        $params["email"] = $args['email'];
        $params["status"] = self::STATUS_ACTIVE;
        $params["created_at"] = time();
        $params["updated_at"] = time();
        $params["iconPath"] = $args['iconPath'];
        $params["description"] = $args['description'];
        $params["verification_token"] = $this->getRandomHashKey() . '_' . time();

        $queryString = "-- auto-generated definition
                INSERT INTO user 
                (username, auth_key, password_hash, password_reset_token, email,
                 status, created_at, updated_at, verification_token, iconPath, description) 
                VALUES (:username, :auth_key, :password_hash, :password_reset_token, :email,
                 :status, :created_at, :updated_at, :verification_token, :iconPath, :description);";

        $this->createOne($queryString, $params);

        $userData = $this->get($args['username']);

        return $userData;
    }
}