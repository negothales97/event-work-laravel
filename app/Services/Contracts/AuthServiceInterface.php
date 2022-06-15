<?php

namespace App\Services\Contracts;

use App\Models\User;
use App\Services\Responses\ServiceResponse;

interface AuthServiceInterface
{
    public function login(string $email, string $password): ServiceResponse;
    public function generateToken(User $user): ServiceResponse;
}
