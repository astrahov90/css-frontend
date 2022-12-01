<?php

namespace core\traits;


trait MethodsCheckTrait
{
    protected function checkMethodGet():void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET')
        {
            header($_SERVER['SERVER_PROTOCOL'] . ' 405 Method Not Allowed');
            die();
        }
    }

    protected function checkMethodPost():void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        {
            header($_SERVER['SERVER_PROTOCOL'] . ' 405 Method Not Allowed');
            die();
        }
    }
}