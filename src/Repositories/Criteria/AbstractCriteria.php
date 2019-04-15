<?php

namespace AppCompass\AppCompass\Repositories\Criteria;

use AppCompass\AppCompass\Interfaces\RepositoryInterface;

abstract class AbstractCriteria
{

    public abstract function apply($model, RepositoryInterface $repository);
}