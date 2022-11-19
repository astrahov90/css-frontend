<?php

namespace core\interfaces;


interface IModelDB
{
    public function getAll($queryString, $params);

    public function getOne($queryString, $params);

    public function getValue($queryString, $params);

    public function createOne($queryString, $params);

    public function updateOne($queryString, $params);

}