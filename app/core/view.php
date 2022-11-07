<?php

namespace core;
class View
{
    function generate($content_view, $template_view, $data = null)
    {

        if(is_array($data)) {
            extract($data);
        }

        $title = "Пикомемсы - ваш сайт развлечений";

        include 'app/views/'.$template_view;
    }
}
