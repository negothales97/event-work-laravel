<?php

namespace App\Repositories;

use App\Models\Company;
use App\Repositories\Contracts\CompanyRepository;
use Prettus\Repository\Eloquent\BaseRepository;

class CompanyRepositoryEloquent extends BaseRepository implements CompanyRepository
{
    public function model()
    {
        return Company::class;
    }
}
