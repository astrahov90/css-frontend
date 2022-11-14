<?php

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

    $path = \core\Config::PATH_TO_SQLITE_FILE;
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

    $path = \core\Config::PATH_TO_SQLITE_FILE;
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

        $userArray = getRandomUserData();

        foreach ($userArray as $value)
        {
            $iconPath = getAvatarData($value->Login);

            $queryString = "-- auto-generated definition
                INSERT INTO users 
                (name, description, signDate, iconPath, login, password) 
                VALUES (:login, :description, :signDate, :iconPath, :login, :password);";
            $query = $pdo->prepare($queryString);
            $query->bindValue('iconPath', $iconPath);
            $query->bindValue('login', $value->Login);
            $query->bindValue('description', $value->FirstName.' '.$value->LastName);
            $query->bindValue('signDate', time());
            $query->bindValue('password', password_hash($value->Password,PASSWORD_DEFAULT));
            if (!$query->execute())
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

            $user = rand(min($usersArray),max($usersArray));
            $postTitle = getRandomPostTitle();
            $postText = getRandomPostText();

            $queryString = "-- auto-generated definition
                INSERT INTO posts 
                (author, title, text, pubDate) 
                VALUES (:author, :title, :text, :pubDate);";
            $query = $pdo->prepare($queryString);
            $query->bindValue('author', $user);
            $query->bindValue('title', $postTitle);
            $query->bindValue('text', $postText);
            $query->bindValue('pubDate', time());
            if (!$query->execute())
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

            $user = rand(min($usersArray),max($usersArray));
            $post = rand(min($postsArray),max($postsArray));
            $commentText = getRandomPostText();

            $queryString = "-- auto-generated definition
                INSERT INTO comments 
                (author, post, text, pubDate) 
                VALUES (:author, :post, :text, :pubDate);";
            $query = $pdo->prepare($queryString);
            $query->bindValue('author', $user);
            $query->bindValue('post', $post);
            $query->bindValue('text', $commentText);
            $query->bindValue('pubDate', time());
            if (!$query->execute())
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
            create table users
            (
              id          INTEGER
                primary key
              autoincrement,
              name        TEXT not null,
              description TEXT,
              signDate    TIMESTAMP,
              iconPath    TEXT,
              login       VARCHAR(255),
              password    VARCHAR(255)
            );
            
            create unique index users_id_uindex
              on users (id);';

    $queryStringArray['POSTS'] = '-- auto-generated definition
            create table posts
            (
              id      INTEGER
                primary key
              autoincrement,
              title   TEXT not null,
              text    TEXT not null,
              pubDate TIMESTAMP,
              author  INTEGER
                constraint posts_users_id_fk
                references users
            );
            
            create unique index posts_id_uindex
              on posts (id);';

    $queryStringArray['COMMENTS'] = '-- auto-generated definition
            create table comments
            (
              id      INTEGER
                primary key
              autoincrement,
              post    INTEGER
                constraint comments_posts_id_fk
                references posts,
              author  INTEGER
                constraint comments_users_id_fk
                references users,
              text    TEXT,
              pubDate TIMESTAMP
            );
            
            create unique index comments_id_uindex
              on comments (id);';

    $queryStringArray['POSTS_LIKES'] = '-- auto-generated definition
            create table posts_likes
            (
              id     INTEGER
                primary key
              autoincrement,
              post   INTEGER not null
                constraint post
                references posts,
              rating INTEGER,
              author INTEGER
                constraint posts_likes_users_id_fk
                references users
            );
            
            create unique index posts_likes_id_uindex
              on posts_likes (id);';

    return $queryStringArray;
}

function getRandomUserData()
{
    $curl = curl_init('https://api.randomdatatools.ru/?unescaped=false&params=LastName,FirstName,Login,Password&count=5');

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
?>