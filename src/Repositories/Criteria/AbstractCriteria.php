<?php

namespace AppCompass\Repositories\Criteria;

use AppCompass\Interfaces\RepositoryInterface;

abstract class AbstractCriteria
{

    public abstract function apply($model, RepositoryInterface $repository);
}