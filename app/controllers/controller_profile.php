<?php

class Controller_Profile extends \core\Controller
{
    function __construct($pdo)
    {
        parent::__construct();
        $this->model = new Model_Profile($pdo);
        $this->view = new \core\View();
    }

    function action_index()
    {
        if (!$_SESSION['isAuthorized'])
        {
            $location = ((!empty($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST']."/login?redirect=".$_SERVER["REQUEST_URI"].(!empty($_SERVER['QUERY_STRING'])?"&".$_SERVER['QUERY_STRING']:"");
            header('Location: '.$location);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST'){
            if (isset($_FILES['avatar']))
            {
                $uploaddir = '/files/users/';
                $uploadfile = $uploaddir . uniqid(rand(), false).'.'.pathinfo(basename($_FILES['avatar']['name']), PATHINFO_EXTENSION);

                move_uploaded_file($_FILES['avatar']['tmp_name'], $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.$uploadfile);

                $this->model->updateUserAvatar($_SESSION["userId"], $uploadfile);
            }
        }

        $data = [];
        $data['author'] = $this->model->getUserInfo($_SESSION['userId']);

        $this->view->generate('app/views/profile_view.php', "template_view.php", $data);
    }

}