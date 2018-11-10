<?php

namespace AppCompass\Repositories;

use App\Company;
use AppCompass\Repositories\Eloquent\Repository;

class CompaniesRepository extends Repository
{
    public function getModel()
    {
        return Company::class;
    }
}
