<?php

namespace core;

class Router
{
    static function start($dbh)
    {
        $uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

        preg_match('/\/((?P<controller>\w+)\/?)((?P<id>\d*)\/)?((?P<action>\w*)\/?)?/', $uri, $matches);

        $controller_name = $matches['controller']?:'Main';
        $object_id = $matches['id']?:null;
        $action_name = $matches['action']?:'index';

        $controller_name = 'controllers\Controller_' . $controller_name;
        $action_name = 'action_' . $action_name;

        if (!class_exists($controller_name))
            self::ErrorPage404();

        $controller = new $controller_name($dbh);

        if (method_exists($controller, $action_name)) {
            if (isset($object_id))
                $controller->$action_name($object_id);
            else
                $controller->$action_name();
        } else {
            self::ErrorPage404();
        }

    }

    static function ErrorPage404()
    {
        $host = 'http://' . $_SERVER['HTTP_HOST'] . '/';
        header('HTTP/1.1 404 Not Found');
        header("Status: 404 Not Found");
        header('Location:' . $host . '404');
    }
}