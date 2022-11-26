<?php

namespace core\traits;


trait GetListTrait
{
    public function getList(iterable $args)
    {
        $result = [];
        $result['meta'] = [];
        $result['data'] = $this->getPage($args);
        $result['meta']['total'] = $this->getCount($args);
        $result['meta']['to'] = $args['offset'] + count($result['data']);

        return $result;
    }

    abstract public function getPage(iterable $args);

    abstract public function getCount(iterable $args);

}