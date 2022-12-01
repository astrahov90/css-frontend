<?php

namespace tests;

use core\ControllerFactory;
use PHPUnit\Framework\TestCase;

class ControllerFactoryTest extends TestCase
{
    public function setUp(): void
    {
        global $_SESSION;
    }

    public function testPosts(): void
    {
        $Controller = ControllerFactory::build('Posts');
        $this->assertInstanceOf(\controllers\Controller_Posts::class, $Controller);
    }

    public function testComments(): void
    {
        $Controller = ControllerFactory::build('Comments');
        $this->assertInstanceOf(\controllers\Controller_Comments::class, $Controller);
    }

    public function testAbout(): void
    {
        $Controller = ControllerFactory::build('About');
        $this->assertInstanceOf(\controllers\Controller_About::class, $Controller);
    }

    public function testAuthors(): void
    {
        $Controller = ControllerFactory::build('Authors');
        $this->assertInstanceOf(\controllers\Controller_Authors::class, $Controller);
    }

    public function testContacts(): void
    {
        $Controller = ControllerFactory::build('Contacts');
        $this->assertInstanceOf(\controllers\Controller_Contacts::class, $Controller);
    }

    public function testLogin(): void
    {
        $Controller = ControllerFactory::build('Login');
        $this->assertInstanceOf(\controllers\Controller_Login::class, $Controller);
    }

    public function testMain(): void
    {
        $Controller = ControllerFactory::build('Main');
        $this->assertInstanceOf(\controllers\Controller_Main::class, $Controller);
    }

    public function testProfile(): void
    {
        $Controller = ControllerFactory::build('Profile');
        $this->assertInstanceOf(\controllers\Controller_Profile::class, $Controller);
    }
}