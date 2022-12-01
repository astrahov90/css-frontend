<?php

namespace core\traits;


trait CheckCSRFTrait
{
    protected function checkCSRFToken(): void
    {
        $token = htmlspecialchars($_POST['token']??null);

        if (!$token || $token !== $_SESSION['token']) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 405 Method Not Allowed');
            die();
        }
    }

    protected function setCSRFToken(): void
    {
        $_SESSION['token'] = md5(uniqid(mt_rand(), true));
    }
}