<?php

namespace tests;

use core\ControllerFactory;
use stdClass;

/**
 * @testdox Testing of posts controller
 */
class Controller_PostsTest extends ModelFixtures
{
    /**
     * @testdox Setup method
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::setController('Posts');
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
        $_REQUEST['newest'] = true;

        $pageSize = 5;
        $firstArray = $this->loadPostListData(0, 5);
        $this->assertCount($pageSize, $firstArray);

        $postItem = $firstArray[0];
        $this->testPostItemFields($postItem);

        $secondArray = $this->loadPostListData(5, 5);
        $this->assertCount($pageSize, $secondArray);

        $this->assertNotEquals($firstArray, $secondArray);

        $this->assertGreaterThan($secondArray[0]->created_at, $firstArray[0]->created_at);

        $thirdArray = $this->loadPostListData(10, 5);
        $this->assertCount(0, $thirdArray);

        $_REQUEST = [];
        $_REQUEST['best'] = true;
        $firstBestArray = $this->loadPostListData(0, 5);
        $this->assertCount($pageSize, $firstBestArray);

        $this->assertNotEquals($firstArray, $firstBestArray);

        $this->assertGreaterThanOrEqual($firstBestArray[1]->likes_count, $firstBestArray[0]->likes_count);
    }

    private function loadPostListData(int $offset, int $pageSize): iterable
    {
        $_REQUEST['offset'] = $offset;

        $getListReturn = self::$controller->runAction('getPosts');

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

    public function testGetPost(): void
    {
        $firstPost = $this->loadPostData(1);

        $lastPost = $this->loadPostData(10);

        $this->assertNotEquals($firstPost, $lastPost);
    }

    private function loadPostData(int $postId): stdClass
    {
        $_REQUEST = [];
        $_REQUEST["postId"] = $postId;

        $getPostReturn = self::$controller->runAction('getPost');

        $this->assertNotEmpty($getPostReturn);

        $postItem = json_decode($getPostReturn);
        $this->testPostItemFields($postItem);

        return $postItem;
    }

    private function testPostItemFields(stdClass $postItem): void
    {
        $this->assertIsObject($postItem);

        $this->assertObjectHasAttribute('id', $postItem);
        $this->assertObjectHasAttribute('title', $postItem);
        $this->assertObjectHasAttribute('body', $postItem);
        $this->assertObjectHasAttribute('created_at', $postItem);
        $this->assertObjectHasAttribute('authorId', $postItem);
        $this->assertObjectHasAttribute('authorName', $postItem);
        $this->assertObjectHasAttribute('iconPath', $postItem);
        $this->assertObjectHasAttribute('likes_count', $postItem);
        $this->assertObjectHasAttribute('comments_count', $postItem);
        $this->assertObjectHasAttribute('comments_count_text', $postItem);
    }

    public function testRating(): void
    {
        $firstPostId = 7;
        $secondPostId = 8;

        $this->setGetMethod();

        $getRatingReturn = self::$controller->runAction('rating', $firstPostId);

        $this->assertNotEmpty($getRatingReturn);

        $ratingItem = json_decode($getRatingReturn);
        $this->assertIsObject($ratingItem);
        $this->assertObjectHasAttribute('rating', $ratingItem);
        $this->assertIsInt($ratingItem->rating);
        $this->assertEquals(0, $ratingItem->rating);

        $getRatingReturn = self::$controller->runAction('rating', $secondPostId);

        $this->assertNotEmpty($getRatingReturn);

        $ratingItem = json_decode($getRatingReturn);
        $this->assertIsObject($ratingItem);
        $this->assertObjectHasAttribute('rating', $ratingItem);
        $this->assertIsInt($ratingItem->rating);
        $this->assertEquals(0, $ratingItem->rating);

        $this->setPostMethod();
        $this->setAuthorized();
        $this->setToken();

        self::$controller->runAction('like', $firstPostId);
        self::$controller->runAction('dislike', $secondPostId);

        $this->setGetMethod();
        $this->setGuest();

        $getRatingReturn = self::$controller->runAction('rating', $firstPostId);

        $this->assertNotEmpty($getRatingReturn);

        $ratingItem = json_decode($getRatingReturn);
        $this->assertIsObject($ratingItem);
        $this->assertObjectHasAttribute('rating', $ratingItem);
        $this->assertIsInt($ratingItem->rating);
        $this->assertEquals(1, $ratingItem->rating);

        $getRatingReturn = self::$controller->runAction('rating', $secondPostId);

        $this->assertNotEmpty($getRatingReturn);

        $ratingItem = json_decode($getRatingReturn);
        $this->assertIsObject($ratingItem);
        $this->assertObjectHasAttribute('rating', $ratingItem);
        $this->assertIsInt($ratingItem->rating);
        $this->assertEquals(-1, $ratingItem->rating);
    }

    public function testAddPost(): void
    {
        $this->setPostMethod();
        $this->setAuthorized();
        $this->setToken();

        $_POST['body'] = 'test';
        $_POST['title'] = 'test';

        $postAddResult = self::$controller->runAction('addPost');

        $this->assertNotEmpty($postAddResult);

        $postItem = json_decode($postAddResult);
        $this->testPostItemFields($postItem);

        $this->assertEquals($_POST['title'], $postItem->title);
        $this->assertEquals($_POST['body'], $postItem->body);
        $this->assertEquals($_SESSION['userId'], $postItem->authorId);

        $this->setGetMethod();
        $this->setGuest();

        $loadedPostItem = $this->loadPostData($postItem->id);

        $this->assertEquals($loadedPostItem, $loadedPostItem);
    }

}