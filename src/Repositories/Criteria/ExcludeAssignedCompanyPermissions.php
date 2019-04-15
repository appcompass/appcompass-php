<?php

namespace AppCompass\AppCompass\Repositories\Criteria;

use AppCompass\AppCompass\Interfaces\RepositoryInterface;

class ExcludeAssignedCompanyPermissions extends AbstractCriteria
{

    private $user_id;

    public function __construct($user_id)
    {
        $this->user_id = $user_id;
    }

    public function apply($model, RepositoryInterface $repo)
    {
        $query = $model
            ->select('permissions.*')
            ->join('permission_user', function ($join) {
                $join->on('permission_user.permission_id', '=', 'permissions.id')
                    ->whereNull('permission_user.company_id')
                ;
            })->join('users', function ($join) {
                $join->on('users.id', '=', 'permission_user.user_id')
                    ->where('users.id', $this->user_id)
                ;
            })
        ;

        return $query;
    }

}
