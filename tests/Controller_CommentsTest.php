<?php

namespace tests;

use core\ControllerFactory;
use stdClass;

/**
 * @testdox Testing of comments controller
 */
class Controller_CommentsTest extends ModelFixtures
{
    /**
     * @testdox Setup method
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::setController('Comments');
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
        $firstArray = $this->loadCommentsListData(0, 5, 1);
        $this->assertCount($pageSize, $firstArray);

        $postItem = $firstArray[0];
        $this->testCommentItemFields($postItem);

        $secondArray = $this->loadCommentsListData(5, 5, 1);
        $this->assertCount($pageSize, $secondArray);

        $this->assertNotEquals($firstArray, $secondArray);

        $this->assertLessThanOrEqual($secondArray[0]->created_at, $firstArray[0]->created_at);
    }

    private function loadCommentsListData(int $offset, int $pageSize, int $postId): iterable
    {
        $_REQUEST['offset'] = $offset;
        $_REQUEST['postId'] = $postId;

        $getListReturn = self::$controller->runAction('getCommentsByPost');

        $this->assertNotEmpty($getListReturn);

        $getListReturnArray = json_decode($getListReturn);

        $this->assertIsObject($getListReturnArray);

        $this->assertObjectHasAttribute('meta',$getListReturnArray);

        $this->assertObjectHasAttribute('to',$getListReturnArray->meta);
        $this->assertEquals(min(10,$pageSize+$offset), $getListReturnArray->meta->to);

        $this->assertObjectHasAttribute('total',$getListReturnArray->meta);
        $this->assertEquals(10, $getListReturnArray->meta->total);

        $this->assertObjectHasAttribute('data',$getListReturnArray);

        return $getListReturnArray->data;
    }
    private function testCommentItemFields(stdClass $postItem): void
    {
        $this->assertIsObject($postItem);

        $this->assertObjectHasAttribute('id', $postItem);
        $this->assertObjectHasAttribute('body', $postItem);
        $this->assertObjectHasAttribute('created_at', $postItem);
        $this->assertObjectHasAttribute('authorId', $postItem);
        $this->assertObjectHasAttribute('authorName', $postItem);
        $this->assertObjectHasAttribute('iconPath', $postItem);
    }

    public function testAddComment(): void
    {
        $this->setPostMethod();
        $this->setAuthorized();
        $this->setToken();

        $_POST['body'] = 'test';
        $_POST['postId'] = '1';

        $commentAddResult = self::$controller->runAction('addCommentToPost');

        $this->assertNotEmpty($commentAddResult);

        $commentItem = json_decode($commentAddResult);
        $this->testCommentItemFields($commentItem);

        $this->assertEquals($_POST['body'], $commentItem->body);
        $this->assertEquals($_POST['postId'], $commentItem->post_id);
        $this->assertEquals($_SESSION['userId'], $commentItem->authorId);
    }

}