<?php

namespace P3in\Repositories;

use P3in\Models\Resource;
use P3in\Repositories\Eloquent\Repository;

class ResourcesRepository extends Repository
{
    public function getModel()
    {
        return Resource::class;
    }
}
