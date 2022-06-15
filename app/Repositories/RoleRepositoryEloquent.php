<?php

namespace App\Repositories;

use App\Models\Role;
use App\Repositories\Contracts\RoleRepository;
use Prettus\Repository\Eloquent\BaseRepository;

class RoleRepositoryEloquent extends BaseRepository implements RoleRepository
{
    public function model()
    {
        return Role::class;
    }
}
