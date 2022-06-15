<?php

namespace App\Services;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use App\Services\Responses\ServiceResponse;
use App\Repositories\Contracts\UserRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Services\Contracts\ResetPasswordServiceInterface;

class ResetPasswordService extends BaseService implements ResetPasswordServiceInterface
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
     * Envia email de recuperação de senha
     *
     * @param array $data
     *
     * @return ServiceResponse
     */
    public function sendResetLink(array $data): ServiceResponse
    {
        try {
            $status =  Password::sendResetLink($data);
        } catch (\Exception $e) {
            return $this->defaultErrorReturn($e);
        }
        return new ServiceResponse(
            $status === Password::RESET_LINK_SENT,
            __($status),
        );
    }

    /**
     * Reseta a senha do usuário
     *
     * @param array $data
     *
     * @return ServiceResponse
     */
    public function resetPassword(array $data): ServiceResponse
    {
        try {
            $checkSamePasswordResponse = $this
                ->checkIfPasswordIsEqualsOldPassword($data['email'], $data['password']);
        } catch (\Exception $e) {
            return $this->defaultErrorReturn($e, compact('data'));
        }

        if (!$checkSamePasswordResponse->success) {
            return $checkSamePasswordResponse;
        }

        $status = Password::reset($data, function ($user, $password) {
            $user->forceFill([
                'password' => $password
            ]);

            $user->save();
            event(new PasswordReset($user));
        });

        return new ServiceResponse(
            $status === Password::PASSWORD_RESET,
            __($status),
        );
    }

    /**
     * Verifica se a senha que o usuário está tentando mudar é igual a sua senha anterior
     *
     * @param string $email
     * @param string $password
     *
     * @return ServiceResponse
     */
    public function checkIfPasswordIsEqualsOldPassword(string $email, string $password): ServiceResponse
    {
        try {
            $user = $this->userRepository->findByField('email', $email)->first();

            if (is_null($user)) {
                throw new ModelNotFoundException;
            }
        } catch (ModelNotFoundException $e) {
            return new ServiceResponse(
                false,
                'Usuário não localizado'
            );
        } catch (\Throwable $e) {
            return $this->defaultErrorReturn($e, compact('email'));
        }

        if (Hash::check($password, $user->password)) {
            return new ServiceResponse(
                false,
                'Sua senha não pode ser igual a senha antiga.',
            );
        }

        return new ServiceResponse(
            true,
            'Senha válida'
        );
    }
}
