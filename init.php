<?php

error_reporting(E_ERROR | E_PARSE);

require str_replace("\\", DIRECTORY_SEPARATOR, __DIR__.'/vendor/autoload.php');

spl_autoload_register(function ($className) {
    $path = __DIR__ . '/' . $className . '.php';
    $path = str_replace("\\", DIRECTORY_SEPARATOR, $path);
    if (is_file($path)) {
        require_once $path;
    }
});

$dotenv = Dotenv\Dotenv::createImmutable(str_replace("\\", DIRECTORY_SEPARATOR,__DIR__.'/'));
$dotenv->load();

if ($argc != 2 || in_array($argv[1], array('--help', '-help', '-h', '-?'))) {
    ?>

    Available commands:

    prepareSQLite - create SQLite db if not exists
    prepareSampleData - fill db with starting samples of users, posts, comments

    Using:
    <?php echo $argv[0]; ?> <option>

    <option> Options  --help, -help, -h,
        or -? shows current help data.

    <?php
} else if ($argv[1]=='prepareSQLite'){
    include ('app'.DIRECTORY_SEPARATOR.'core'.DIRECTORY_SEPARATOR.'config.php');

    $path = \core\Config::get_db_host();
    if (file_exists($path))
    {
        print_r("SQL already prepared" . PHP_EOL);
        die();
    }
    else
    {
        $fileDB = fopen($path,'wb+');
        fclose($fileDB);

        $pdo = new \PDO("sqlite:" . $path);

        print_r('CREATING SQL TABLES' . PHP_EOL);

        $queryStringArray = getCreateQueryArray();

        foreach ($queryStringArray as $key => $value)
        {
            $query = $pdo->prepare($value);
            if (!$query->execute())
            {
                print_r($key.' table create error' . PHP_EOL);
                unlink($path);
                die();
            }
            else
            {
                print_r($key.' table created successfully' . PHP_EOL);
            }
        }

        print_r('Tables prepared successfully' . PHP_EOL);
    }
} else if ($argv[1]=='prepareSampleData'){
    include ('app'.DIRECTORY_SEPARATOR.'core'.DIRECTORY_SEPARATOR.'config.php');

    $path = \core\Config::get_db_host();
    if (!file_exists($path) || !filesize($path))
    {
        print_r("SQL is not prepared" . PHP_EOL);
        die();
    }
    else
    {
        $usersArray = [];
        $postsArray = [];

        $pdo = new \PDO("sqlite:" . $path);

        print_r('Samples creation starts' . PHP_EOL);

        $admin = [];
        $admin['username'] = 'admin';
        $admin['password_hash'] = password_hash('12345678',PASSWORD_DEFAULT);
        $admin['email'] = 'admin@sai-testlab.ddns.net';
        $admin['created_at'] = $admin['updated_at'] = time();
        $admin['iconPath'] = getAvatarData($admin['username']);
        $admin['description'] = 'Это администратор';

        $result = addUser($pdo, $admin);

        if (!$result)
        {
            print_r('Admin user insert error' . PHP_EOL);
            die();
        }

        print_r('Admin user with password 12345678 inserted successfully' . PHP_EOL);

        $userArray = getRandomUserData();

        foreach ($userArray as $value)
        {
            $userData = [];
            $userData['username'] = $value->Login;
            $userData['password_hash'] = password_hash($value->Password,PASSWORD_DEFAULT);
            $userData['email'] = $value->Email;
            $userData['iconPath'] = getAvatarData($userData['username']);
            $userData['description'] = $value->FirstName.' '.$value->LastName;

            $result = addUser($pdo, $userData);

            if (!$result)
            {
                print_r('User insert error' . PHP_EOL);
                die();
            }

            print_r('User '.$value->Login.' with password '.$value->Password.' inserted successfully' . PHP_EOL);
            $usersArray[] = $pdo->lastInsertId();
        }

        for ($i=0;$i<10;$i++)
        {
            if ($i/5.0 == floor($i/5))
            {
                sleep(1);
            }

            $author_id = rand(min($usersArray),max($usersArray));

            $postData = [];
            $postData['author_id'] = $author_id;
            $postData['title'] = getRandomPostTitle();
            $postData['body'] = getRandomPostText();

            $result = addPost($pdo, $postData);

            if (!$result)
            {
                print_r('Post insert error' . PHP_EOL);
                die();
            }
            print_r('Post inserted successfully' . PHP_EOL);
            $postsArray[] = $pdo->lastInsertId();
        }

        for ($i=0;$i<50;$i++)
        {
            if ($i/10.0 == floor($i/10))
            {
                sleep(1);
            }

            $author_id = rand(min($usersArray),max($usersArray));
            $post_id = rand(min($postsArray),max($postsArray));

            $commentData = [];
            $commentData['author_id'] = $author_id;
            $commentData['post_id'] = $post_id;
            $commentData['body'] = getRandomCommentText();

            $result = addComment($pdo, $commentData);

            if (!$result)
            {
                print_r('Comment insert error' . PHP_EOL);
                die();
            }
            print_r('Comment inserted successfully' . PHP_EOL);
        }

        print_r('Samples prepared successfully' . PHP_EOL);
    }
} else {
    print_r('Wrong command');
}

function getCreateQueryArray() {
    $queryStringArray = [];
    $queryStringArray['USERS'] = '-- auto-generated definition
        create table user
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
        create table posts
        (
          id         integer not null
            primary key
          autoincrement,
          author_id  INTEGER not null
            references user
              on delete cascade,
          title      varchar(255),
          body       text,
          created_at integer
        );
        
        create index "idx-posts-author_id"
          on posts (author_id);';

    $queryStringArray['COMMENTS'] = '-- auto-generated definition
        create table comments
        (
          id         integer not null
            primary key
          autoincrement,
          author_id  INTEGER not null
            references user
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
        create table posts_likes
        (
          id        integer not null
            primary key
          autoincrement,
          author_id INTEGER not null
            references user
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

function getRandomUserData()
{
    $curl = curl_init('https://api.randomdatatools.ru/?unescaped=false&params=LastName,FirstName,Login,Password,Email&count=5');

    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_HEADER, false);

    $result = curl_exec($curl);

    curl_close($curl);

    return json_decode($result);
}

function getAvatarData($param)
{
    $uploaddir = '/files/users/';
    $uploadfile = $uploaddir . uniqid(rand(), false).'.png';

    $file = fopen(str_replace('/',DIRECTORY_SEPARATOR,__DIR__.$uploadfile),'wb+');
    $curl = curl_init('https://api.multiavatar.com/'.$param.'.png');

    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_HEADER, false);

    $result = curl_exec($curl);

    curl_close($curl);

    fputs($file, $result);
    fclose($file);

    return $uploadfile;
}

function getRandomPostTitle()
{
    $curl = curl_init('https://fish-text.ru/get?type=title&number=1');

    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_HEADER, false);

    $result = curl_exec($curl);

    curl_close($curl);

    return json_decode($result)->text;
}

function getRandomPostText()
{
    $curl = curl_init('https://fish-text.ru/get?type=paragraph&number=1');

    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_HEADER, false);

    $result = curl_exec($curl);

    curl_close($curl);

    return json_decode($result)->text;
}

function getRandomCommentText()
{
    $curl = curl_init('https://fish-text.ru/get?type=sentence&number=1');

    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_HEADER, false);

    $result = curl_exec($curl);

    curl_close($curl);

    return json_decode($result)->text;
}

function addUser(\PDO $pdo, array $userData)
{
    $queryString = "-- auto-generated definition
                INSERT INTO user 
                (username, auth_key, password_hash, password_reset_token, email,
                 status, created_at, updated_at, verification_token, iconPath, description) 
                VALUES (:username, :auth_key, :password_hash, :password_reset_token, :email,
                 :status, :created_at, :updated_at, :verification_token, :iconPath, :description);";

    $query = $pdo->prepare($queryString);
    $query->bindParam('username', $userData['username']);
    $query->bindValue('auth_key', getRandomHashKey());
    $query->bindParam('password_hash', $userData['password_hash']);
    $query->bindValue('password_reset_token', getRandomHashKey() . '_' . time());
    $query->bindParam('email', $userData['email']);
    $query->bindValue('status', 10);
    $query->bindValue('created_at', time());
    $query->bindValue('updated_at', time());
    $query->bindParam('iconPath', $userData['iconPath']);
    $query->bindParam('description', $userData['description']);
    $query->bindValue('verification_token', getRandomHashKey() . '_' . time());

    return $query->execute();
}

function addPost(\PDO $pdo, array $postData)
{
    $queryString = "-- auto-generated definition
                INSERT INTO posts 
                (author_id, title, body, created_at) 
                VALUES (:author_id, :title, :body, :created_at);";

    $query = $pdo->prepare($queryString);
    $query->bindParam('author_id', $postData['author_id']);
    $query->bindParam('title', $postData['title']);
    $query->bindParam('body', $postData['body']);
    $query->bindValue('created_at', time());
    return $query->execute();
}

function addComment(\PDO $pdo, array $commentData)
{
    $queryString = "-- auto-generated definition
                INSERT INTO comments 
                (author_id, post_id, body, created_at) 
                VALUES (:author_id, :post_id, :body, :created_at);";

    $query = $pdo->prepare($queryString);
    $query->bindParam('author_id', $commentData['author_id']);
    $query->bindParam('post_id', $commentData['post_id']);
    $query->bindParam('body', $commentData['body']);
    $query->bindValue('created_at', time());
    return $query->execute();
}

function getRandomHashKey()
{
    return substr(strtr(base64_encode(random_bytes(32)), '+/', '-_'), 0, 32);
}
?>