<?php

namespace core;

use core\interfaces\IControllerFactory;

class ControllerFactory implements IControllerFactory
{
    const CONTROLLER_PREFIX = 'controllers\Controller_';

    public static function build($className) : ?Controller
    {
        if (class_exists(self::CONTROLLER_PREFIX.$className))
        {
            $loader = new \Twig\Loader\FilesystemLoader(str_replace('\\', DIRECTORY_SEPARATOR,__DIR__.'/../views/'));
            $twig = new \Twig\Environment($loader, [
                'cache' => str_replace('\\', DIRECTORY_SEPARATOR,__DIR__.'/../views/cache'),
                'debug' => true,
            ]);

            $controller = new (self::CONTROLLER_PREFIX.$className)($twig);
            $controller->setModel($className);

            return $controller;
        }

        return null;
    }
}