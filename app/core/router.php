<?php

namespace core;

class Router
{
    static function start($pdo)
    {
        $uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

        preg_match('/\/(?P<controller>\w+)\/((?P<id>\d*)\/)?((?P<action>\w*)\/?)?/', $uri, $matches);

        $controller_name = $matches['controller']?:'Main';
        $model_id = $matches['id']?:null;
        $action_name = $matches['action']?:'index';

        $model_name = 'Model_' . $controller_name;
        $controller_name = 'Controller_' . $controller_name;
        $action_name = 'action_' . $action_name;

        $model_file = strtolower($model_name) . '.php';
        $model_path = "app/models/" . $model_file;
        if (file_exists($model_path)) {
            include $model_path;
        }

        $controller_file = strtolower($controller_name) . '.php';
        $controller_path = "app/controllers/" . $controller_file;
        if (file_exists($controller_path)) {
            include $controller_path;
        } else {
            self::ErrorPage404();
        }

        $controller = new $controller_name($pdo);
        $action = $action_name;

        if (method_exists($controller, $action)) {
            if (isset($model_id))
                $controller->$action($model_id);
            else
                $controller->$action();
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