<?php

namespace core;

abstract class Model
{
    protected $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    protected function bbCodeDecode($curText)
    {
        $curText = preg_replace("/(\[b\])(.+?)(\[\/b\])/i", "<span style='font-weight: bold;'>$2</span>", $curText);
        $curText = preg_replace("/(\[i\])(.+?)(\[\/i\])/i", "<span style='font-style: italic;'>$2</span>", $curText);
        $curText = preg_replace("/(\[u\])(.+?)(\[\/u\])/i", "<span style='text-decoration: underline;'>$2</span>", $curText);
        $curText = preg_replace("/(\[s\])(.+?)(\[\/s\])/i", "<span style='text-decoration: line-through;'>$2</span>", $curText);
        $curText = preg_replace("/(\[quote\])(.+?)(\[\/quote\])/i", "<blockquote>$2</blockquote>", $curText);
        $curText = preg_replace("/(\[img\])(.+?)(\[\/img\])/i", "<img src='$2'>", $curText);
        $curText = preg_replace("/(\[url\])(.+?)(\[\/url\])/i", "<a href='$2'>$2</a>", $curText);
        $curText = preg_replace("/(\[url=(.+?)\])(.+?)(\[\/url\])/i", "<a href='$2'>$3</a>", $curText);
        $curText = preg_replace("/(\[color='(.+?)'\])(.+?)(\[\/color\])/i", "<span style='color: $2;'>$3</span>", $curText);

        return $curText;
    }

    protected function getRandomHashKey(){
        return substr(strtr(base64_encode(random_bytes(32)), '+/', '-_'), 0, 32);
    }

    protected function postWork($elem)
    {
        return $elem;
    }

}