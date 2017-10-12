<?php

namespace P3in\Repositories\Criteria;

use P3in\Interfaces\RepositoryInterface;

abstract class Criteria
{

    public abstract function apply($model, RepositoryInterface $repository);
}