<?php

namespace AppCompass\AppCompass\Repositories;

use App\Company;
use AppCompass\AppCompass\Repositories\Eloquent\Repository;

class CompaniesRepository extends Repository
{
    public function getModel()
    {
        return Company::class;
    }
}
