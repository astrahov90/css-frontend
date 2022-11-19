<?php
/**
 * Created by PhpStorm.
 * User: astra
 * Date: 19.11.2022
 * Time: 18:30
 */

namespace core\interfaces;


interface IViewFactory
{
    public static function build($viewName);
}