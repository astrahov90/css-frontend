<?php

    session_start();

    $title = "Пикомемсы - ошибочная страница";

?>

<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $title ?></title>
    <link rel="stylesheet" href="/assets/css/reset.css">
    <script src="/assets/js/jquery-3.6.1.js"></script>
    <script src="/assets/js/jquery.validate.js"></script>
    <script src="/assets/js/common.js"></script>
    <link rel="icon" href="/assets/img/logo.svg">
    <link rel="stylesheet" href="/assets/css/main.css">
</head>
<body>
<?php include 'app/views/layout/header.php' ?>

<section class="section-body bg-light section-middle">
    <div class="container bg-light">
        <div class="row">
            <div class="col-12">Страница не найдена, обратитесь к разработчикам.
            </div>
        </div>
    </div>
</section>

<?php include 'app/views/layout/footer.php' ?>
</body>
</html>