<?php

namespace AppCompass\Repositories;

use AppCompass\Models\Resource;
use AppCompass\Repositories\Eloquent\Repository;

class ResourcesRepository extends Repository
{
    public function getModel()
    {
        return Resource::class;
    }
}
