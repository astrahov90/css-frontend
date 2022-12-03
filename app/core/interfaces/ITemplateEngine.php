<?php

namespace core\interfaces;

interface ITemplateEngine
{
    public function setTemplateEngine():void;

    public function render(string $templateName, array $data=[]):string;
}