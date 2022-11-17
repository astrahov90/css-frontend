<?php

class Controller_Login extends \core\Controller
{
    function __construct($pdo)
    {
        parent::__construct();
        $this->model = new Model_Login($pdo);
        $this->view = new \core\View();
    }

    function action_index()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_REQUEST["username"]) && isset($_REQUEST["password"])) {
                $userData = $this->model->getUser($_REQUEST["username"]);
                if (password_verify($_REQUEST["password"], $userData['password_hash'])) {
                    $_SESSION["isAuthorized"] = true;
                    $_SESSION["userId"] = $userData["id"];
                    $_SESSION["userName"] = $userData["username"];

                    $url = ((!empty($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];

                    $queryArr = [];
                    parse_str($_SERVER['QUERY_STRING'], $queryArr);

                    $location = $url;
                    if (array_key_exists('redirect', $queryArr)) {
                        $location = $url . $queryArr['redirect'];
                        unset($queryArr['redirect']);
                        if (count($queryArr)) {
                            $location .= '?' . join('&', $queryArr);
                        }
                    }
                    header('Location: ' . $location);
                } else {
                    $data = [];
                    $data['error'] = "Имя или пароль неверные.";
                    $this->view->generate('app/views/login_view.php', "template_view.php", $data);
                }
            }
        } else {
            $this->view->generate('app/views/login_view.php', "template_view.php");
        }

    }

    function action_logout()
    {
        session_destroy();

        $url = ((!empty($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];

        $queryArr = [];
        parse_str($_SERVER['QUERY_STRING'], $queryArr);

        $location = $url;
        if (array_key_exists('redirect', $queryArr)) {
            $location = $url . $queryArr['redirect'];
            unset($queryArr['redirect']);
            if (count($queryArr)) {
                $location .= '?' . join('&', $queryArr);
            }
        }
        header('Location: ' . $location);
    }

    function action_register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_REQUEST["username"]) && isset($_REQUEST["password"]) && isset($_REQUEST["password_again"])
                && isset($_REQUEST["email"]) && isset($_REQUEST["description"])) {
                $checkUser = $this->model->getUser($_REQUEST["username"]);

                if ($checkUser !== false) {
                    $data = [];
                    $data['error'] = "Имя уже занято.";
                    $this->view->generate('app/views/register_view.php', "template_view.php", $data);
                    die();
                }

                $iconPath = null;
                if (isset($_FILES['avatar'])) {
                    $uploaddir = '/files/users/';
                    $uploadfile = $uploaddir . uniqid(rand(), false) . '.' . pathinfo(basename($_FILES['avatar']['name']), PATHINFO_EXTENSION);

                    move_uploaded_file($_FILES['avatar']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . $uploadfile);

                    $iconPath = $uploadfile;
                }

                $newUser = $this->model->addUser($_REQUEST['username'], $_REQUEST['password'], $_REQUEST['email'], $_REQUEST['description'], $iconPath);

                $_SESSION["isAuthorized"] = true;
                $_SESSION["userId"] = $newUser["id"];
                $_SESSION["userName"] = $newUser["username"];

                $url = ((!empty($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . "/profile/";

                header('Location: ' . $url);
            }
        } else {
            $this->view->generate('app/views/register_view.php', "template_view.php");
        }

    }
}