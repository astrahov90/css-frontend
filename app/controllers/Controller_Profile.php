<?php

namespace controllers;

class Controller_Profile extends \core\Controller
{
    function action_index()
    {
        $this->checkAuthorizationRedirectLogin();

        if (!$_SESSION['isAuthorized']) {
            $location = ((!empty($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . "/login?redirect=" . $_SERVER["REQUEST_URI"] . (!empty($_SERVER['QUERY_STRING']) ? "&" . $_SERVER['QUERY_STRING'] : "");
            header('Location: ' . $location);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->checkCSRFToken();
            $this->checkAuthorization();

            if (isset($_FILES['avatar'])) {
                $uploaddir = '/files/users/';
                $uploadfile = $uploaddir . uniqid(rand(), false) . '.' . pathinfo(basename($_FILES['avatar']['name']), PATHINFO_EXTENSION);

                move_uploaded_file($_FILES['avatar']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . $uploadfile);

                $this->model->updateUserAvatar($_SESSION["userId"], $uploadfile);
            }
        }

        $data = [];
        $data['author'] = $this->model->get($_SESSION['userId']);

        $this->setCSRFToken();
        $this->twig->addGlobal('session', $_SESSION);
        echo $this->twig->render(str_replace('\\', DIRECTORY_SEPARATOR,'profile.html'), $data);
    }

}