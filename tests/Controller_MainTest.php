<?php

namespace tests;

/**
 * @testdox Testing of main controller
 */
class Controller_MainTest extends ControllerFixtures
{
    /**
     * @testdox Setup method
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::setController('Main');
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

        $this->setGuest();
        $this->setGetMethod();
    }

    public function testIndex(): void
    {
        $indexOutput = self::$controller->runAction('index');
        $this->assertMatchesRegularExpression('/Пикомемсы - лучшее/i', $indexOutput);

        $_REQUEST['newest'] = true;
        $indexOutput = self::$controller->runAction('index');
        $this->assertMatchesRegularExpression('/Пикомемсы - свежее/i', $indexOutput);

        unset($_REQUEST['newest']);
        $_REQUEST['newPost'] = true;
        $indexOutput = self::$controller->runAction('index');
        $this->assertMatchesRegularExpression('/Пикомемсы - новый пост/i', $indexOutput);
    }

}
