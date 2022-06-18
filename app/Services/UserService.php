<?php

namespace App\Services;

use App\Criteria\UserCriteria;
use App\Repositories\Contracts\RoleRepository;
use Illuminate\Support\Facades\DB;
use App\Services\Responses\ServiceResponse;
use App\Repositories\Contracts\UserRepository;
use App\Services\Contracts\UserServiceInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserService extends BaseService implements UserServiceInterface
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
     * Traz todos os usuarios do banco paginados
     *
     * @param int $per_page
     *
     * @return ServiceResponse
     */
    public function getAllPaginated(int $per_page = 50): ServiceResponse
    {
        try {
            $users = $this->userRepository
                ->pushCriteria(UserCriteria::class)
                ->paginate($per_page);
        } catch (\Throwable $e) {
            return $this->defaultErrorReturn($e);
        }

        return new ServiceResponse(
            true,
            'Lista de usuários',
            $users
        );
    }

    /**
     * Cria um usuário no banco de dados
     *
     * @param array $data
     *
     * @return ServiceResponse
     */
    public function create(array $data): ServiceResponse
    {
        DB::beginTransaction();
        try {
            if (!isset($data['role_id'])) {
                $role = app(RoleRepository::class)->findWhere(['name' => 'admin'])->first();
                $data['role_id'] = $role->uuid;
            }
            $user = $this->userRepository->create($data);
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->defaultErrorReturn($e, $data);
        }
        DB::commit();

        return new ServiceResponse(
            true,
            'Usuário criado com sucesso',
            $user
        );
    }

    /**
     * Retorna os dados de um usuário cadastrado no banco
     *
     * @param string $uuid
     *
     * @return ServiceResponse
     */
    public function find(string $uuid): ServiceResponse
    {
        try {
            $user = $this->userRepository->findByField('uuid', $uuid)->first();

            if (is_null($user)) {
                throw new ModelNotFoundException;
            }
        } catch (ModelNotFoundException $e) {
            return new ServiceResponse(
                false,
                'Usuário não localizado'
            );
        } catch (\Throwable $e) {
            return $this->defaultErrorReturn($e, compact('uuid'));
        }

        return new ServiceResponse(
            true,
            'Usuário localizado com sucesso',
            $user
        );
    }

    /**
     * Atualiza os dados de um usuário já cadastrado no banco
     *
     * @param string $uuid
     * @param array $attributes
     *
     * @return ServiceResponse
     */
    public function update(string $uuid, array $data): ServiceResponse
    {
        DB::beginTransaction();
        try {
            $userResponse = $this->find($uuid);

            if (!$userResponse->success) {
                return $userResponse;
            }

            $user = $userResponse->data;
            $this->userRepository->update($data, $user->id);
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->defaultErrorReturn($e, compact('uuid', 'data'));
        }
        DB::commit();
        return new ServiceResponse(
            true,
            'Usuário atualizado com sucesso',
            $user
        );
    }

    /**
     * Realiza a exclusão de um usuário do banco
     *
     * @param string $uuid
     *
     * @return ServiceResponse
     */
    public function delete(string $uuid): ServiceResponse
    {
        DB::beginTransaction();
        try {
            $userResponse = $this->find($uuid);
            if (!$userResponse->success) {
                return $userResponse;
            }

            $user = $userResponse->data;
            $this->userRepository->delete($user->id);
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->defaultErrorReturn($e, compact('uuid'));
        }
        DB::commit();
        return new ServiceResponse(
            true,
            'Usuário removido com sucesso'
        );
    }
}
