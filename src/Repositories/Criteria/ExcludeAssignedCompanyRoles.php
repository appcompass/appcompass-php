<?php

namespace AppCompass\Repositories\Criteria;

use AppCompass\Interfaces\RepositoryInterface;

class ExcludeAssignedCompanyRoles extends AbstractCriteria
{

    private $user_id;

    public function __construct($user_id)
    {
        $this->user_id = $user_id;
    }

    public function apply($model, RepositoryInterface $repo)
    {
        $query = $model
            ->select('roles.*')
            ->join('role_user', function ($join) {
                $join->on('role_user.role_id', '=', 'roles.id')
                    ->whereNull('role_user.company_id')
                ;
            })->join('users', function ($join) {
                $join->on('users.id', '=', 'role_user.user_id')
                    ->where('users.id', $this->user_id)
                ;
            })
        ;

        return $query;
    }

}
