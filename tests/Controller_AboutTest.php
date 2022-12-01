<?php

namespace tests;

/**
 * @testdox Testing of posts controller
 */
class Controller_AboutTest extends ControllerFixtures
{
    /**
     * @testdox Setup method
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::setController('About');
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
        self::$controller->runAction('index');
        $indexOutput = $this->getActualOutputForAssertion();
        $this->assertMatchesRegularExpression('/Пикомемсы - о проекте/i', $indexOutput);
        ob_clean();
    }

}