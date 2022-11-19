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
            $controller = new (self::CONTROLLER_PREFIX.$className)(ViewFactory::build('core\view'));
            $controller->setModel($className);

            return $controller;
        }

        return null;
    }
}