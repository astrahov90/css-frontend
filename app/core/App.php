<?php

namespace core;

use core\interfaces\IAppFacade;
use core\interfaces\ITemplateEngine;
use core\traits\TwigTemplateEngineTrait;

class App implements IAppFacade, ITemplateEngine
{
    public static App $app;
    private string $baseDir;

    use TwigTemplateEngineTrait;

    public function __construct()
    {
        $this->setBaseDir();

        $this->setTemplateEngine();

        static::$app = $this;
    }

    public function start(): void
    {
        Router::start();
    }

    private function setBaseDir()
    {
        $this->baseDir = __DIR__.'/../';
    }

    private function getBaseDir():string
    {
        return str_replace('\\', DIRECTORY_SEPARATOR, $this->baseDir);
    }
}