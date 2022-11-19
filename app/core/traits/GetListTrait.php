<?php

namespace core\traits;


trait GetListTrait
{
    public function getList(iterable $args)
    {
        $result = [];
        $result["data"] = $this->getPage($args);
        $result["totalCount"] = $this->getCount($args);
        $result["currentCount"] = $args['offset'] + count($result["data"]);

        return $result;
    }

    abstract public function getPage(iterable $args);

    abstract public function getCount(iterable $args);

}