<?php
/**
 * Created by PhpStorm.
 * User: astra
 * Date: 19.11.2022
 * Time: 17:33
 */

namespace db;


interface IDBConnection
{
    public function connect();
}