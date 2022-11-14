<?php

error_reporting(E_ERROR | E_PARSE);

session_start();
include 'core/config.php';

spl_autoload_register(function($className) {
    $path = __DIR__ . '/'  . $className . '.php';
    $path = str_replace("\\",DIRECTORY_SEPARATOR, $path);
    if (is_file($path)) {
        require_once $path;
    }
});

$pdo = null;
try
{
    $pdo = (new \db\SQLiteConnection())->connect();
    if ($pdo == null)
        throw new \HttpException();

}
catch (\Exception $e)
{

}

require_once 'core/model.php';
require_once 'core/view.php';
require_once 'core/controller.php';
require_once 'core/router.php';

\core\Router::start($pdo);

function render_template($template, $params)
{
    extract($params);

    ob_start();
    include ($template);
    $result = ob_get_contents();
    ob_end_clean();

    return $result;
}
