<?php

namespace P3in\Repositories;

use App\User;
use P3in\Interfaces\UsersRepositoryInterface;

class UsersRepository extends AbstractRepository implements UsersRepositoryInterface
{

//    const SEE_OWNED = 1;

//    const EDIT_OWNED = 1;

    protected $owned_key = 'id';

    protected $view_types = ['Table', 'Card'];

    public function __construct(User $model)
    {
        $this->model = $model;
    }
}
