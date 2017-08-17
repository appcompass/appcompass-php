<?php

namespace P3in\Controllers;

use P3in\Repositories\UsersRepository;

class UsersController extends AbstractController
{
    public function __construct(UsersRepository $repo)
    {
        $this->repo = $repo;
    }
}
