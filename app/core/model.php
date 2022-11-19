<?php

namespace core;

use core\interfaces\IModelDB;

class Model implements IModelDB
{
    private \PDO $dbh;

    public function __construct($dbh)
    {
        $this->dbh = $dbh;
    }

    protected function bbCodeDecode($curText)
    {
        $curText = preg_replace("/(\[b\])(.+?)(\[\/b\])/i", "<span style='font-weight: bold;'>$2</span>", $curText);
        $curText = preg_replace("/(\[i\])(.+?)(\[\/i\])/i", "<span style='font-style: italic;'>$2</span>", $curText);
        $curText = preg_replace("/(\[u\])(.+?)(\[\/u\])/i", "<span style='text-decoration: underline;'>$2</span>", $curText);
        $curText = preg_replace("/(\[s\])(.+?)(\[\/s\])/i", "<span style='text-decoration: line-through;'>$2</span>", $curText);
        $curText = preg_replace("/(\[quote\])(.+?)(\[\/quote\])/i", "<blockquote>$2</blockquote>", $curText);
        $curText = preg_replace("/(\[img\])(.+?)(\[\/img\])/i", "<img src='$2'>", $curText);
        $curText = preg_replace("/(\[url\])(.+?)(\[\/url\])/i", "<a href='$2'>$2</a>", $curText);
        $curText = preg_replace("/(\[url=(.+?)\])(.+?)(\[\/url\])/i", "<a href='$2'>$3</a>", $curText);
        $curText = preg_replace("/(\[color='(.+?)'\])(.+?)(\[\/color\])/i", "<span style='color: $2;'>$3</span>", $curText);

        return $curText;
    }

    protected function getRandomHashKey(){
        return substr(strtr(base64_encode(random_bytes(32)), '+/', '-_'), 0, 32);
    }

    public function getValue($queryString, $params)
    {
        $query = $this->dbh->prepare($queryString);
        $query->execute($params);
        $result = $query->fetchColumn();

        return $result;
    }

    public function getAll($queryString, $params=[])
    {
        $query = $this->dbh->prepare($queryString);
        $query->execute($params);
        $result = $query->fetchAll(\PDO::FETCH_ASSOC);

        return $result;
    }

    public function getOne($queryString, $params)
    {
        $query = $this->dbh->prepare($queryString);
        $query->execute($params);
        $result = $query->fetch(\PDO::FETCH_ASSOC);

        return $result;
    }

    public function createOne($queryString, $params)
    {
        $query = $this->dbh->prepare($queryString);
        $query->execute($params);

        $recordId = $this->dbh->lastInsertId();

        return $recordId;
    }

    public function updateOne($queryString, $params)
    {
        $query = $this->dbh->prepare($queryString);
        $query->execute($params);

        $recordId = $this->dbh->lastInsertId();

        return $recordId;
    }

}