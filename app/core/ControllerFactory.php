<?php

namespace core;

use core\interfaces\IControllerFactory;

class ControllerFactory implements IControllerFactory
{
    const CONTROLLER_PREFIX = 'controllers\Controller_';

    public static function build(string $className) : ?Controller
    {
        if (class_exists(self::CONTROLLER_PREFIX.$className))
        {
            $controller = new (self::CONTROLLER_PREFIX.$className);
            $controller->setModel($className, ModelFactory::getDBH(Config::getDBType()));

            return $controller;
        }

        return null;
    }
}