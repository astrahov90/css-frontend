<?php

namespace core;

use core\interfaces\IViewFactory;

class ViewFactory implements IViewFactory
{
    public static function build($viewName) : ?View
    {
        return new $viewName;
    }
}