<?php

namespace App\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class UserCriteria.
 *
 * @package namespace App\Criteria;
 */
class UserCriteria implements CriteriaInterface
{
    /**
     * Apply criteria in query repository
     *
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        if (request()->has('name') && request('name') !== null) {
            $model = $model->where('name', 'like', '%' . request('name') . '%');
        }
        if (request()->has('email') && request('email') !== null) {
            $model = $model->where('email', 'like', '%' . request('email') . '%');
        }
        if (request()->has('role_id') && request('role_id') !== null) {
            $model = $model->where('role_id', request('role_id'));
        }
        if (request()->has('phone') && request('phone') !== null) {
            $model = $model->where('phone', 'like', '%' . request('phone'));
        }

        return $model;
    }
}
