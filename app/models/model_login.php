<?php

class Model_Login extends \core\Model {

    const QUERY_BASE = "SELECT
        users.id, users.name, users.login, users.password
        FROM users
        WHERE login=:login";

    public function getUser($login){
        $queryString = self::QUERY_BASE;
        $query = $this->pdo->prepare($queryString);

        $query->bindParam("login", $login);
        $query->execute();

        $info = $query->fetch(\PDO::FETCH_ASSOC);
        return $info;
    }

    public function addUser($login, $password, $name, $description){
        $queryString = "INSERT INTO users (name, description, login, password, signDate) VALUES (:name, :description, :login, :password, :signDate);";
        $query = $this->pdo->prepare($queryString);
        $query->bindParam("name", $name);
        $query->bindParam("description", $description);
        $query->bindParam("login", $login);
        $query->bindParam("password", $password);

        $date = new DateTime(null);
        $query->bindValue("signDate", ($date->getTimestamp())*1000);

        $query->execute();

        $info = $this->getUser($login);
        return $info;
    }
}