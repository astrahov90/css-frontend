<?php

namespace tests;

/**
 * @testdox Testing of posts controller
 */
class ModelFixtures extends ControllerFixtures
{
    /**
     * @testdox Setup method
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::prepareDB();
    }

    private static function prepareDB(): void
    {
        foreach (self::getCreateQueryArray() as $key => $value)
        {
            $query = self::$pdo->prepare($value);
            if (!$query->execute())
            {
                print_r($key.' table create error' . PHP_EOL);
                throw new \Exception('DB fill error');
            }
        }

        $usersArray = [];
        $postsArray = [];

        $admin = [];
        $admin['username'] = 'admin';
        $admin['password_hash'] = password_hash('12345678',PASSWORD_DEFAULT);
        $admin['email'] = 'admin@sai-testlab.ddns.net';
        $admin['created_at'] = $admin['updated_at'] = time();
        $admin['iconPath'] = "";
        $admin['description'] = 'Это администратор';

        $result = self::addUser(self::$pdo, $admin);

        if (!$result)
        {
            print_r('Admin user insert error' . PHP_EOL);
            die();
        }

        $userArray = self::getRandomUserData();

        foreach ($userArray as $value)
        {
            $userData = [];
            $userData['username'] = $value->Login;
            $userData['password_hash'] = password_hash($value->Password,PASSWORD_DEFAULT);
            $userData['email'] = $value->Email;
            $userData['iconPath'] = "";
            $userData['description'] = $value->FirstName.' '.$value->LastName;

            $result = self::addUser(self::$pdo, $userData);

            if (!$result)
            {
                print_r('User insert error' . PHP_EOL);
                die();
            }

            $usersArray[] = self::$pdo->lastInsertId();
        }

        for ($i=0;$i<10;$i++)
        {
            $author_id = rand(min($usersArray),max($usersArray));

            $postData = [];
            $postData['author_id'] = $author_id;
            $postData['title'] = self::getRandomHashKey();
            $postData['body'] = self::getRandomHashKey();

            $result = self::addPost(self::$pdo, $postData);

            if (!$result)
            {
                print_r('Post insert error' . PHP_EOL);
                die();
            }
            $postsArray[] = self::$pdo->lastInsertId();
        }

        for ($i=0;$i<10;$i++)
        {
            $author_id = rand(min($usersArray),max($usersArray));
            $post_id = min($postsArray);

            $commentData = [];
            $commentData['author_id'] = $author_id;
            $commentData['post_id'] = $post_id;
            $commentData['body'] = self::getRandomHashKey();

            $result = self::addComment(self::$pdo, $commentData);

            if (!$result)
            {
                print_r('Comment insert error' . PHP_EOL);
                die();
            }
        }

        self::addRating(self::$pdo, $postsArray);

        print_r(PHP_EOL.'Fixtures prepared successfully' . PHP_EOL);
    }

    private static function getCreateQueryArray():iterable {
        $queryStringArray = [];
        $queryStringArray['USERS'] = '-- auto-generated definition
            CREATE TABLE IF NOT EXISTS users
            (
              id                   integer      not null
                primary key
                                                autoincrement,
              username             varchar(255) not null
                unique,
              auth_key             varchar(32)  not null,
              password_hash        varchar(255) not null,
              password_reset_token varchar(255)
                unique,
              email                varchar(255) not null
                unique,
              status               smallint     default 10 not null,
              created_at           integer      not null,
              updated_at           integer      not null,
              verification_token   varchar(255) default NULL,
              iconPath             varchar(255) default NULL,
              description          text         default NULL
            );
            
    ';

        $queryStringArray['POSTS'] = '-- auto-generated definition
            CREATE TABLE IF NOT EXISTS posts
            (
              id         integer not null
                primary key
              autoincrement,
              author_id  INTEGER not null
                references users
                  on delete cascade,
              title      varchar(255),
              body       text,
              created_at integer
            );
            
            create index "idx-posts-author_id"
              on posts (author_id);';

        $queryStringArray['COMMENTS'] = '-- auto-generated definition
            CREATE TABLE IF NOT EXISTS comments
            (
              id         integer not null
                primary key
              autoincrement,
              author_id  INTEGER not null
                references users
                  on delete cascade,
              post_id    INTEGER not null
                references posts
                  on delete cascade,
              body       text,
              created_at integer
            );
            
            create index "idx-comments-author_id"
              on comments (author_id);
            
            create index "idx-comments-posts_id"
              on comments (post_id);';

        $queryStringArray['POSTS_LIKES'] = '-- auto-generated definition
            CREATE TABLE IF NOT EXISTS posts_likes
            (
              id        integer not null
                primary key
              autoincrement,
              author_id INTEGER not null
                references users
                  on delete cascade,
              post_id   INTEGER not null
                references posts
                  on delete cascade,
              rating    integer
            );
            
            create index "idx-posts_likes-author_id"
              on posts_likes (author_id);
            
            create index "idx-posts_likes-post_id"
              on posts_likes (post_id);
            
            create unique index "idx-unique-post_likes-author_id-posts_id"
              on posts_likes (author_id, post_id);';

        return $queryStringArray;
    }

    private static function getRandomUserData()
    {
        $curl = curl_init('https://api.randomdatatools.ru/?unescaped=false&params=LastName,FirstName,Login,Password,Email&count=2');

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_HEADER, false);

        $result = curl_exec($curl);

        curl_close($curl);

        return json_decode($result);
    }

    private static function addUser(\PDO $pdo, array $userData)
    {
        $queryString = "-- auto-generated definition
                INSERT INTO users 
                (username, auth_key, password_hash, password_reset_token, email,
                 status, created_at, updated_at, verification_token, iconPath, description) 
                VALUES (:username, :auth_key, :password_hash, :password_reset_token, :email,
                 :status, :created_at, :updated_at, :verification_token, :iconPath, :description);";

        $query = $pdo->prepare($queryString);
        $query->bindParam('username', $userData['username']);
        $query->bindValue('auth_key', self::getRandomHashKey());
        $query->bindParam('password_hash', $userData['password_hash']);
        $query->bindValue('password_reset_token', self::getRandomHashKey() . '_' . time());
        $query->bindParam('email', $userData['email']);
        $query->bindValue('status', 10);
        $query->bindValue('created_at', time());
        $query->bindValue('updated_at', time());
        $query->bindParam('iconPath', $userData['iconPath']);
        $query->bindParam('description', $userData['description']);
        $query->bindValue('verification_token', self::getRandomHashKey() . '_' . time());

        return $query->execute();
    }

    private static function addPost(\PDO $pdo, array $postData)
    {
        $queryString = "-- auto-generated definition
                INSERT INTO posts 
                (author_id, title, body, created_at) 
                VALUES (:author_id, :title, :body, :created_at);";

        $query = $pdo->prepare($queryString);
        $query->bindParam('author_id', $postData['author_id']);
        $query->bindParam('title', $postData['title']);
        $query->bindParam('body', $postData['body']);
        $query->bindValue('created_at', time()+random_int(0,60));
        return $query->execute();
    }

    private static function addComment(\PDO $pdo, array $commentData)
    {
        $queryString = "-- auto-generated definition
                INSERT INTO comments 
                (author_id, post_id, body, created_at) 
                VALUES (:author_id, :post_id, :body, :created_at);";

        $query = $pdo->prepare($queryString);
        $query->bindParam('author_id', $commentData['author_id']);
        $query->bindParam('post_id', $commentData['post_id']);
        $query->bindParam('body', $commentData['body']);
        $query->bindValue('created_at', time()+random_int(0,60));
        return $query->execute();
    }

    private static function addRating(\PDO $pdo, array $postArray)
    {
        $queryString = "-- auto-generated definition
                INSERT INTO posts_likes 
                (author_id, post_id, rating) 
                VALUES (:author_id, :post_id, :rating);";

        $query = $pdo->prepare($queryString);
        $query->bindValue('author_id', 1);
        $query->bindValue('post_id', max($postArray));
        $query->bindValue('rating', 1);
        return $query->execute();
    }

    private static function getRandomHashKey()
    {
        return substr(strtr(base64_encode(random_bytes(32)), '+/', '-_'), 0, 32);
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
    }
}