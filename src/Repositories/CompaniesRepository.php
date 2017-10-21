<?php

namespace P3in\Repositories;

use App\Company;
use P3in\Repositories\Eloquent\Repository;

class CompaniesRepository extends Repository
{
    public function getModel()
    {
        return Company::class;
    }
}
