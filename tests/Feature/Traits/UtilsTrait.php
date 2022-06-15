<?php

namespace Tests\Feature\Traits;

use App\Models\User;

trait UtilsTrait
{
    public function createUser()
    {
        $user = User::factory()->create()->first();

        return $user;
    }

    public function createUserToken()
    {
        $user = $this->createUser();

        $token = $user->createToken('teste')->plainTextToken;

        return $token;
    }
}
