<?php

namespace tests;

use core\App;
use core\Controller;
use core\ControllerFactory;
use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;

/**
 * @testdox Testing of posts controller
 */
class ControllerFixtures extends TestCase
{
    protected static ?\PDO $pdo;
    protected static Controller $controller;

    /**
     * @testdox Setup method
     */
    public static function setUpBeforeClass(): void
    {
        require str_replace("\\", DIRECTORY_SEPARATOR, __DIR__.'/../vendor/autoload.php');

        $dotenv = Dotenv::createImmutable(str_replace("\\", DIRECTORY_SEPARATOR,__DIR__.'/../'));
        $dotenv->load();

        $app = new App;

        global $_SESSION;
        global $_REQUEST;
        global $_SERVER;
        global $_POST;

        self::$pdo = new \PDO("sqlite::memory:");
    }

    public static function tearDownAfterClass(): void
    {
        self::$pdo = null;
    }

    protected function setGetMethod():void
    {
        $_SERVER['REQUEST_METHOD'] = "GET";
    }

    protected function setPostMethod():void
    {
        $_SERVER['REQUEST_METHOD'] = "POST";
    }

    protected function setAuthorized(): void
    {
        $_SESSION['isAuthorized'] = true;
        $_SESSION['userId'] = 1;
        $_SESSION['userName'] = 'admin';
    }

    protected function setGuest(): void
    {
        unset($_SESSION['isAuthorized']);
    }

    protected function setToken(): void
    {
        $token = 'newtoken';

        $_SESSION['token'] = $token;
        $_POST['token'] = $token;
    }

    protected static function setController(string $className): void
    {
        $controller = ControllerFactory::build($className);
        $controller->setModel($className, self::$pdo);

        self::$controller = $controller;
    }
}