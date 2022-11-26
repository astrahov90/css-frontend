<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $title ?></title>
    <link rel="stylesheet" href="/assets/css/reset.css">
    <script type="module" src="/assets/js/app.js"></script>
    <script src="/assets/js/jquery-3.6.1.js"></script>
    <script src="/assets/js/jquery.validate.js"></script>
    <link rel="icon" href="/assets/img/logo.svg">
    <link rel="stylesheet" href="/assets/css/main.css">
</head>
<body>
<?php include 'layout/header.php' ?>

<?php include $content_view ?>

<?php include 'layout/footer.php' ?>
</body>
</html>