<?php

namespace P3in\Interfaces;

use P3in\Repositories\Criteria\AbstractCriteria;

interface CriteriaInterface
{

    public function skipCriteria($status = true);

    public function getCriteria();

    public function getByCriteria(AbstractCriteria $criteria);

    public function pushCriteria(AbstractCriteria $criteria);

    public function applyCriteria();
}