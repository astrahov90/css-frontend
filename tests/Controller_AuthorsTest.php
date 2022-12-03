<?php

namespace tests;

use core\ControllerFactory;
use stdClass;

/**
 * @testdox Testing of authors controller
 */
class Controller_AuthorsTest extends ModelFixtures
{
    /**
     * @testdox Setup method
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::setController('Authors');
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

    /**
     * @testdox Get list method test
     */
    public function testGetList(): void
    {
        $pageSize = 5;
        $firstArray = $this->loadAuthorsListData(0, 3);
        $this->assertCount(3, $firstArray);

        $authorItem = $firstArray[0];
        $this->testAuthorItemFields($authorItem);
    }

    private function loadAuthorsListData(int $offset, int $pageSize): iterable
    {
        $_REQUEST['offset'] = $offset;

        $getListReturn = self::$controller->runAction('getAuthors');

        $this->assertNotEmpty($getListReturn);

        $getListReturnArray = json_decode($getListReturn);

        $this->assertIsObject($getListReturnArray);

        $this->assertObjectHasAttribute('meta',$getListReturnArray);

        $this->assertObjectHasAttribute('to',$getListReturnArray->meta);
        $this->assertEquals(min(3,$pageSize+$offset), $getListReturnArray->meta->to);

        $this->assertObjectHasAttribute('total',$getListReturnArray->meta);
        $this->assertEquals(3, $getListReturnArray->meta->total);

        $this->assertObjectHasAttribute('data',$getListReturnArray);

        return $getListReturnArray->data;
    }
    private function testAuthorItemFields(stdClass $authorItem): void
    {
        $this->assertIsObject($authorItem);

        $this->assertObjectHasAttribute('authorId', $authorItem);
        $this->assertObjectHasAttribute('authorName', $authorItem);
        $this->assertObjectHasAttribute('created_at', $authorItem);
        $this->assertObjectHasAttribute('iconPath', $authorItem);
        $this->assertObjectHasAttribute('posts_count', $authorItem);
        $this->assertObjectHasAttribute('description', $authorItem);
    }

    public function testGetAuthor(): void
    {
        $firstAuthor = $this->loadAuthorData(1);

        $secondAuthor = $this->loadAuthorData(2);

        $this->assertNotEquals($firstAuthor, $secondAuthor);
    }

    private function loadAuthorData(int $authorId): stdClass
    {
        $_REQUEST = [];
        $_REQUEST["authorId"] = $authorId;

        $getPostReturn = self::$controller->runAction('getAuthor');

        $this->assertNotEmpty($getPostReturn);

        $postItem = json_decode($getPostReturn);
        $this->testAuthorItemFields($postItem);

        return $postItem;
    }

}