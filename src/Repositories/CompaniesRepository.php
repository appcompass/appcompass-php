<?php

namespace P3in\Repositories;

use App\Company;
use P3in\Repositories\Eloquent\Repository;

class CompaniesRepository extends Repository
{
    protected $owned_key = 'id';
    protected $view_types = ['Table', 'Card'];
    protected $with = [];

    public function getModel()
    {
        return Company::class;
    }
}
