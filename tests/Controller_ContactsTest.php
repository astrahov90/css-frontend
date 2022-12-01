<?php

namespace tests;

/**
 * @testdox Testing of contacts controller
 */
class Controller_ContactsTest extends ControllerFixtures
{
    /**
     * @testdox Setup method
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::setController('Contacts');
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
        $this->assertMatchesRegularExpression('/Пикомемсы - контакты/i', $indexOutput);
        ob_clean();
    }

}