<?php

namespace P3in\Controllers;

use P3in\Repositories\CompaniesRepository;
use P3in\Repositories\Criteria\Companies\WithUserCount;

class CompaniesController extends BaseResourceController
{
    public function __construct(CompaniesRepository $repo)
    {
        $this->repo = $repo;
        $this->repo->pushCriteria(new WithUserCount());
    }
}
