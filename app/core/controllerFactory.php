<?php

namespace core;

use core\interfaces\IControllerFactory;

class ControllerFactory implements IControllerFactory
{

    public function getController($className) : ?Controller
    {
        if (class_exists($className))
            return new $className(new View());

        return null;
    }
}