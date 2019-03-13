<?php

namespace AppCompass\Controllers;

use AppCompass\Policies\ResourcesPolicy;
use AppCompass\Repositories\Criteria\HasCompanyIfNotAdmin;
use AppCompass\Repositories\UsersRepository;
use Auth;

class UsersController extends AbstractBaseResourceController
{
    protected $param_name = 'user';

    public function __construct(UsersRepository $repo)
    {
        $this->repo = $repo;
        if (strpos(php_sapi_name(), 'cli') !== false) {
            $this->repo->pushCriteria(new HasCompanyIfNotAdmin(Auth::user()));
        }
    }

    public function getPolicy()
    {
        return ResourcesPolicy::class;
    }
}
