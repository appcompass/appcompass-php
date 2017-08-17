<?php

namespace P3in\Controllers;

use P3in\Repositories\FormsRepository;

class FormsController extends AbstractController
{
    public function __construct(FormsRepository $repo)
    {
        $this->repo = $repo;
    }
}
