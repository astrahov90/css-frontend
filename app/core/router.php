<?php

namespace core;

class Router
{
    static function start($pdo)
    {
        $controller_name = 'Main';
        $action_name = 'index';
        $model_id = null;

        $routes = explode('/', parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH));

        if ( !empty($routes[1]) )
        {
            $controller_name = $routes[1];
        }

        if ( !empty($routes[2]) )
        {
            if(is_numeric($routes[2]))
                $model_id = $routes[2];
            else
                $action_name = $routes[2];
        }

        if ( !empty($routes[3])
        && is_numeric($routes[2]))
        {
            $model_id = $routes[2];
            $action_name = $routes[3];
        }

        $model_name = 'Model_'.$controller_name;
        $controller_name = 'Controller_'.$controller_name;
        $action_name = 'action_'.$action_name;

        $model_file = strtolower($model_name).'.php';
        $model_path = "app/models/".$model_file;
        if(file_exists($model_path))
        {
            include $model_path;
        }

        $controller_file = strtolower($controller_name).'.php';
        $controller_path = "app/controllers/".$controller_file;
        if(file_exists($controller_path))
        {
            include $controller_path;
        }
        else
        {
            self::ErrorPage404();
        }

        $controller = new $controller_name($pdo);
        $action = $action_name;

        if(method_exists($controller, $action))
        {
            if (isset($model_id))
                $controller->$action($model_id);
            else
                $controller->$action();
        }
        else
        {
            self::ErrorPage404();
        }

    }

    static function ErrorPage404()
    {
        $host = 'http://'.$_SERVER['HTTP_HOST'].'/';
        header('HTTP/1.1 404 Not Found');
        header("Status: 404 Not Found");
        header('Location:'.$host.'404');
    }
}