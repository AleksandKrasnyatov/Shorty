<?php

namespace app\repositories;

use app\models\Link;

class LinkRepository extends Repository
{
    public function __construct()
    {
        $this->model = new Link();
    }
}