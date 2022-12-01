<?php

namespace controllers;

use core\RedisCache;

class Controller_Login extends \core\Controller
{
    function action_index()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->checkCSRFToken();
            if (isset($_POST["username"]) && isset($_POST["password"])) {
                $userData = $this->model->get($_POST["username"]);


                if (password_verify($_POST["password"], $userData['password_hash'])) {
                    $this->setAuthorization($userData);

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
                    try // for test duties, bad idea(
                    {
                        header('Location: ' . $location);
                    }
                    catch (\Exception)
                    {

                    }
                } else {
                    $data = [];
                    $data['error'] = "Имя или пароль неверные.";

                    $this->setCSRFToken();
                    $this->twig->addGlobal('session', $_SESSION);
                    echo $this->twig->render(str_replace('\\', DIRECTORY_SEPARATOR,'login.html'), $data);
                }
            }
        } else {
            $this->setCSRFToken();
            $this->twig->addGlobal('session', $_SESSION);
            echo $this->twig->render(str_replace('\\', DIRECTORY_SEPARATOR,'login.html'));
        }

    }

    function action_logout()
    {
        $this->checkAuthorization();
        $this->checkMethodPost();
        $this->checkCSRFToken();

        $this->clearAuthorization();

        try // for test duties, bad idea(
        {
            header('Location: /');
        }
        catch (\Exception)
        {

        }
    }

    function action_register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->checkCSRFToken();
            if (isset($_REQUEST["username"]) && isset($_REQUEST["password"]) && isset($_REQUEST["password_again"])
                && isset($_REQUEST["email"]) && isset($_REQUEST["description"])) {
                $checkUser = $this->model->get($_REQUEST["username"]);

                if ($checkUser !== false) {
                    $data = [];
                    $data['error'] = "Имя уже занято.";

                    echo $this->twig->render(str_replace('\\', DIRECTORY_SEPARATOR,'register.html'), $data);
                    return;
                }

                $iconPath = null;
                if (isset($_FILES['avatar'])) {
                    $uploaddir = '/files/users/';
                    $uploadfile = $uploaddir . uniqid(rand(), false) . '.' . pathinfo(basename($_FILES['avatar']['name']), PATHINFO_EXTENSION);

                    move_uploaded_file($_FILES['avatar']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . $uploadfile);

                    $iconPath = $uploadfile;
                }

                $result = $this->model->create(['username' => $_REQUEST['username'],
                    'password' => $_REQUEST['password'],
                    'email' => $_REQUEST['email'],
                    'description' => $_REQUEST['description'],
                    'iconPath' => $iconPath]);

                if (!$result)
                {
                    header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad request');
                    return;
                }

                RedisCache::clearCache('*authors-getList*');

                $newUser = $this->model->get($_REQUEST['username']);

                $this->setAuthorization($newUser);

                $url = ((!empty($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . "/profile/";

                header('Location: ' . $url);
            }
        } else {
            $this->setCSRFToken();
            $this->twig->addGlobal('session', $_SESSION);
            echo $this->twig->render(str_replace('\\', DIRECTORY_SEPARATOR,'register.html'));
        }

    }
}