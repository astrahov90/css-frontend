<?php

namespace core\traits;


trait AuthorizedTrait
{
    protected function checkAuthorization(): void
    {
        if (!isset($_SESSION['isAuthorized']) || !$_SESSION['isAuthorized'])
        {
            header($_SERVER['SERVER_PROTOCOL'] . ' 403 Authorization required');
            die();
        }
    }

    protected function checkAuthorizationRedirectLogin(): void
    {
        if (!isset($_SESSION['isAuthorized']) || !$_SESSION['isAuthorized'])
        {
            $location = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS']==='on') ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . "/login?redirect=" . $_SERVER["REQUEST_URI"] . (!empty($_SERVER['QUERY_STRING']) ? "&" . $_SERVER['QUERY_STRING'] : "");
            header('Location: ' . $location);
            die();
        }
    }

    protected function setAuthorization(iterable $user): void
    {
        $_SESSION["isAuthorized"] = true;
        $_SESSION["userId"] = $user["id"];
        $_SESSION["userName"] = $user["username"];
    }

    protected function clearAuthorization(): void
    {
        unset($_SESSION['isAuthorized']);
        unset($_SESSION['userId']);
        unset($_SESSION['userName']);

        if (session_status()===PHP_SESSION_ACTIVE)
        {
            session_destroy();
        }
    }
}