<?php

namespace App\Services\Contracts;

use App\Services\Responses\ServiceResponse;

interface CategoryServiceInterface
{
    public function find(string $uuid): ServiceResponse;
    public function store(array $data): ServiceResponse;
    public function delete(string $uuid): ServiceResponse;
    public function update(string $uuid, array $data): ServiceResponse;
    public function getAllPaginated(int $per_page = 50): ServiceResponse;
}
