<?php

class Model_Login extends \core\Model
{

    const QUERY_BASE = "SELECT
        user.id, user.username, user.password_hash
        FROM user
        WHERE username=:username";

    public function getUser($username)
    {
        $queryString = self::QUERY_BASE;
        $query = $this->pdo->prepare($queryString);

        $query->bindParam("username", $username);
        $query->execute();

        $info = $query->fetch(\PDO::FETCH_ASSOC);
        return $info;
    }

    public function addUser($username, $password, $email, $description)
    {
        $queryString = "-- auto-generated definition
                INSERT INTO user 
                (username, auth_key, password_hash, password_reset_token, email,
                 status, created_at, updated_at, verification_token, iconPath, description) 
                VALUES (:username, :auth_key, :password_hash, :password_reset_token, :email,
                 :status, :created_at, :updated_at, :verification_token, :iconPath, :description);";

        $query = $this->pdo->prepare($queryString);
        $query->bindParam('username', $username);
        $query->bindValue('auth_key', substr(strtr(base64_encode(random_bytes(32)), '+/', '-_'), 0, 32));
        $query->bindParam('password_hash', password_hash($password, PASSWORD_DEFAULT));
        $query->bindValue('password_reset_token', substr(strtr(base64_encode(random_bytes(32)), '+/', '-_'), 0, 32) . '_' . time());
        $query->bindParam('email', $email);
        $query->bindValue('status', 10);
        $query->bindValue('created_at', time());
        $query->bindValue('updated_at', time());
        #$query->bindParam('iconPath', $userData['iconPath']);
        $query->bindParam('description', $description);
        $query->bindValue('verification_token', substr(strtr(base64_encode(random_bytes(32)), '+/', '-_'), 0, 32) . '_' . time());

        $query->execute();

        $info = $this->getUser($username);
        return $info;
    }
}