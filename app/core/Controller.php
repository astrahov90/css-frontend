<?php

namespace core;

abstract class Controller
{
    protected $model;
    protected $view;
    protected $twig;

    const ACTION_PREFIX = 'action_';

    function __construct($twig)
    {
        $this->twig = $twig;
    }

    function action_index()
    {
    }

    function setModel($class_name)
    {
        $this->model = ModelFactory::build($class_name);
    }

    function runAction($action_name, $object_id)
    {
        $fullActionName = self::ACTION_PREFIX.$action_name;
        if (method_exists($this, $fullActionName)) {
            if (isset($object_id))
                $this->$fullActionName($object_id);
            else
                $this->$fullActionName();
        } else {
            return false;
        }
    }

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

    protected function checkCSRFToken(): void
    {
        $token = htmlspecialchars($_POST['token']??null);

        if (!$token || $token !== $_SESSION['token']) {
            var_dump($token);
            var_dump($_SESSION['token']);
            header($_SERVER['SERVER_PROTOCOL'] . ' 405 Method Not Allowed');
            die();
        }
    }

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
}