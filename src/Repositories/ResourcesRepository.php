<?php

namespace AppCompass\AppCompass\Repositories;

use AppCompass\AppCompass\Models\Resource;
use AppCompass\AppCompass\Repositories\Eloquent\Repository;

class ResourcesRepository extends Repository
{
    public function getModel()
    {
        return Resource::class;
    }
}
