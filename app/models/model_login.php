<?php

use core\interfaces\IModelCreate;
use core\interfaces\IModelGet;

class Model_Login extends \core\Model implements IModelCreate, IModelGet
{
    const QUERY_BASE = "SELECT
        user.id, user.username, user.password_hash
        FROM user
        WHERE username=:username";

    public function get($username)
    {
        $queryString = self::QUERY_BASE;
        $query = $this->pdo->prepare($queryString);

        $query->bindParam("username", $username);
        $query->execute();

        $info = $query->fetch(\PDO::FETCH_ASSOC);
        $info = $this->postWork($info);
        return $info;
    }

    public function create(iterable $args)
    {
        $username = $args['username'];
        $password = $args['password'];
        $email = $args['email'];
        $description = $args['description'];
        $iconPath = $args['iconPath'];

        $queryString = "-- auto-generated definition
                INSERT INTO user 
                (username, auth_key, password_hash, password_reset_token, email,
                 status, created_at, updated_at, verification_token, iconPath, description) 
                VALUES (:username, :auth_key, :password_hash, :password_reset_token, :email,
                 :status, :created_at, :updated_at, :verification_token, :iconPath, :description);";

        $query = $this->pdo->prepare($queryString);
        $query->bindParam('username', $username);
        $query->bindValue('auth_key', $this->getRandomHashKey());
        $query->bindParam('password_hash', password_hash($password, PASSWORD_DEFAULT));
        $query->bindValue('password_reset_token', $this->getRandomHashKey() . '_' . time());
        $query->bindParam('email', $email);
        $query->bindValue('status', 10);
        $query->bindValue('created_at', time());
        $query->bindValue('updated_at', time());
        $query->bindParam('iconPath', $iconPath);
        $query->bindParam('description', $description);
        $query->bindValue('verification_token', $this->getRandomHashKey() . '_' . time());

        $query->execute();

        $info = $this->get($username);
        return $info;
    }
}