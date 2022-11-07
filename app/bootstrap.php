<?php

session_start();
include 'core/config.php';
spl_autoload_register();

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


#$postsModel = new \models\Posts($pdo);
#$usersModel = new \models\Users($pdo);

function render_template($template, $params)
{
    extract($params);

    ob_start();
    include ($template);
    $result = ob_get_contents();
    ob_end_clean();

    return $result;
}
