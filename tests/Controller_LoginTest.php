<?php

namespace tests;

use core\ControllerFactory;
use stdClass;

/**
 * @testdox Testing of posts controller
 */
class Controller_LoginTest extends ModelFixtures
{
    /**
     * @testdox Setup method
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::setController('Login');
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
    }

    public function setUp(): void
    {
        parent::setUp();

        $_SESSION = [];
        $_REQUEST = [];
        $_SERVER = [];
        $_POST = [];

        $_SERVER['HTTP_HOST'] = 'localhost';
        $_SERVER['QUERY_STRING'] = '';

        $this->setGuest();
        $this->setGetMethod();
    }

    /**
     * @runInSeparateProcess
     */
    public function testLogin(): void
    {
        $this->setPostMethod();
        $this->setToken();

        $username = 'admin';
        $_POST['username'] = $username;
        $_POST['password'] = '12345678';

        $this->assertArrayNotHasKey('isAuthorized', $_SESSION);
        $this->assertArrayNotHasKey('userName', $_SESSION);

        self::$controller->runAction('index');

        $this->assertArrayHasKey('isAuthorized', $_SESSION);
        $this->assertEquals(true, $_SESSION['isAuthorized']);
        $this->assertArrayHasKey('userName', $_SESSION);
        $this->assertEquals($username, $_SESSION['userName']);
    }

    /**
     * @runInSeparateProcess
     */
    public function testLogout(): void
    {
        $this->setPostMethod();
        $this->setToken();
        $this->setAuthorized();

        $this->assertArrayHasKey('isAuthorized', $_SESSION);
        $this->assertEquals(true, $_SESSION['isAuthorized']);
        $this->assertArrayHasKey('userName', $_SESSION);
        $this->assertEquals('admin', $_SESSION['userName']);

        self::$controller->runAction('logout');

        $this->assertArrayNotHasKey('isAuthorized', $_SESSION);
    }

}