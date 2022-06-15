<?php

namespace App\Services\Contracts;

use App\Services\Responses\ServiceResponse;

interface ResetPasswordServiceInterface
{
    public function sendResetLink(array $data): ServiceResponse;
    public function resetPassword(array $data): ServiceResponse;
    public function checkIfPasswordIsEqualsOldPassword(string $email, string $password): ServiceResponse;
}
