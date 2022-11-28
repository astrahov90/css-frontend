<?php

namespace core;

class View
{
    function generate($content_view, $template_view, $data = null)
    {

        if (is_array($data)) {
            extract($data);
        }

        $title = "Пикомемсы - ваш сайт развлечений";

        $requestMethod = $_SERVER['REQUEST_METHOD'];
        if ($requestMethod === 'GET')
        {
            $_SESSION['token'] = md5(uniqid(mt_rand(), true));
        }

        include 'app/views/' . $template_view;
    }
}
