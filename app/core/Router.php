<?php

namespace core;

class Router
{
    static function start()
    {
        $uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

        preg_match('/\/((?P<controller>\w+)\/?)((?P<id>\d*)\/)?((?P<action>\w*)\/?)?/', $uri, $matches);

        $class_name = ucfirst($matches['controller']?:'Main');
        $object_id = $matches['id']?:null;
        $action_name = $matches['action']?:'index';

        $controller = ControllerFactory::build($class_name);
        if ($controller===null)
            self::ErrorPage404();

        $response = $controller->runAction($action_name, $object_id);
        if ($response===false)
            self::ErrorPage404();

        if (json_decode($response)!==null)
            header('Content-Type: application/json; charset=utf-8');

        echo $response;
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