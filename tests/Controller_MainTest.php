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
        self::$controller->runAction('index');
        $indexOutput = $this->getActualOutputForAssertion();
        $this->assertMatchesRegularExpression('/Пикомемсы - лучшее/i', $indexOutput);
        ob_clean();

        $_REQUEST['newest'] = true;
        self::$controller->runAction('index');
        $indexOutput = $this->getActualOutputForAssertion();
        $this->assertMatchesRegularExpression('/Пикомемсы - свежее/i', $indexOutput);
        ob_clean();

        unset($_REQUEST['newest']);
        $_REQUEST['newPost'] = true;
        self::$controller->runAction('index');
        $indexOutput = $this->getActualOutputForAssertion();
        $this->assertMatchesRegularExpression('/Пикомемсы - новый пост/i', $indexOutput);
        ob_clean();
    }

}
