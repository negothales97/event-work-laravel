<?php

namespace App\Services\Contracts;

use App\Services\Responses\ServiceResponse;

interface CompanyServiceInterface
{
    public function create(array $data): ServiceResponse;
}
