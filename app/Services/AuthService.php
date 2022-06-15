<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Services\Responses\ServiceResponse;
use App\Repositories\Contracts\UserRepository;
use App\Services\Contracts\AuthServiceInterface;

class AuthService extends BaseService implements AuthServiceInterface
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Realiza o login do usuário
     *
     * @param string $email
     * @param string $password
     *
     * @return ServiceResponse
     */
    public function login(string $email, string $password): ServiceResponse
    {
        $user = $this->userRepository
            ->findWhere(['email' => $email])
            ->first();

        if (is_null($user)) {
            return new ServiceResponse(
                false,
                'Usuário ou senha inválidos',
                compact('email')
            );
        }

        if (!Hash::check($password, $user->password)) {
            return new ServiceResponse(
                false,
                'Usuário ou senha inválidos',
                compact('email')
            );
        }

        return new ServiceResponse(
            true,
            'Usuário logado',
            $user
        );
    }

    /**
     * Gera o token do usuário
     *
     * @param User $user
     *
     * @return ServiceResponse
     */
    public function generateToken(User $user): ServiceResponse
    {
        try {
            $token = $user->createToken("userToken");

            return new ServiceResponse(
                true,
                'Usuário logado',
                [
                    'token' => $token->plainTextToken,
                    'user' => $user
                ]
            );
        } catch (\Throwable $e) {
            return $this->defaultErrorReturn($e, compact('user'), 100500300);
        }
    }
}
