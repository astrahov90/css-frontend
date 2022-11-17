<?php

namespace core\interfaces;


interface IModelGetList
{
    public function getList(iterable $args);

    public function getPage(iterable $args);

    public function getCount(iterable $args);
}