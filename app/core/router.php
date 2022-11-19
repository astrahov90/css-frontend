<?php

namespace core;

class Router
{
    static function start($dbh)
    {
        $uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

        preg_match('/\/((?P<controller>\w+)\/?)((?P<id>\d*)\/)?((?P<action>\w*)\/?)?/', $uri, $matches);

        $class_name = $matches['controller']?:'Main';
        $object_id = $matches['id']?:null;
        $action_name = $matches['action']?:'index';

        $controller_name = 'controllers\Controller_' . $class_name;
        $model_name = 'models\Model_' . $class_name;
        $action_name = 'action_' . $action_name;

        $controller = (new ControllerFactory())->getController($controller_name);
        if ($controller===null)
            self::ErrorPage404();

        $controller->setModel($model_name, $dbh);
        if ($controller->runAction($action_name, $object_id)===false)
            self::ErrorPage404();

    }

    static function ErrorPage404()
    {
        $host = 'http://' . $_SERVER['HTTP_HOST'] . '/';
        header('HTTP/1.1 404 Not Found');
        header("Status: 404 Not Found");
        header('Location:' . $host . '404');
        die();
    }
}