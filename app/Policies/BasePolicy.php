<?php

namespace App\Policies;

use Illuminate\Support\Str;
use App\Exceptions\PolicyServiceException;
use App\Repositories\Contracts\RoleRepository;
use App\Repositories\Contracts\UserRepository;

class BasePolicy
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var RoleRepository
     */
    protected $roleRepository;

    public function __construct(
        UserRepository $userRepository,
        RoleRepository $roleRepository
    ) {
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
    }

    public function isSuperAdmin(int $userId): bool
    {
        $role = $this->roleRepository->findByField('name', 'superAdmin')->first();
        $user = $this->userRepository->find($userId)->firstOrFail();

        return $user->role_id === $role->id;
    }

    public function isAdmin(int $userId): bool
    {
        $role = $this->roleRepository->findByField('name', 'admin')->first();
        $user = $this->userRepository->find($userId)->firstOrFail();

        return $user->role_id === $role->id;
    }

    /**
     * Padrão de resposta para erros de validação das políticas
     *
     * @param string $message
     * @param integer $code
     * @param integer|string $idEntity
     * @param integer|null $idUser
     */
    protected function deny(string $message, int $code, $idEntity = null, int $idUser = null): void
    {
        $explodedClass = explode('\\', get_called_class());
        $className = end($explodedClass);
        $title = strtoupper(str_replace('Service', '', $className));

        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $function = $trace[count($trace) - 1]['function'];
        $action = strtoupper(Str::snake($function) . '_' . $title);


        throw new PolicyServiceException($message, $code);
    }
}
