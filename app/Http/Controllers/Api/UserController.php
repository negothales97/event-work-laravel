<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\ApiController;
use App\Http\Responses\DefaultResponse;
use App\Http\Resources\User\UserResource;
use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Services\Contracts\UserServiceInterface;

class UserController extends ApiController
{
    /**
     * @var UserServiceInterface
     */
    protected $userService;

    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }


    /**
     * Lista todos os usuários
     * GET api/users
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $per_page = request('per_page') ?? 50;
        $userServiceResponse = $this->userService->getAllPaginated($per_page);

        if (!$userServiceResponse->success) {
            return $this->errorResponseFromService($userServiceResponse);
        }

        return $this->response(
            new DefaultResponse(
                UserResource::collection($userServiceResponse->data)
            )
        );
    }

    /**
     * Realiza a criação de um usuário
     * POST api/users
     *
     * @param CreateUserRequest
     *
     * @return JsonResponse
     */
    public function store(CreateUserRequest $request): JsonResponse
    {
        $userServiceResponse = $this->userService->store($request->validated());

        if (!$userServiceResponse->success) {
            return $this->errorResponseFromService($userServiceResponse);
        }

        return $this->response(
            new DefaultResponse(
                new UserResource($userServiceResponse->data)
            )
        );
    }

    /**
     * Retorna os dados de um usuário cadastrado no banco de dados
     * GET api/users/{uuid}
     *
     * @param string $uuid
     *
     * @return JsonResponse
     */
    public function show(string $uuid): JsonResponse
    {
        $userServiceResponse = $this->userService->find($uuid);

        if (!$userServiceResponse->success) {
            return $this->errorResponseFromService($userServiceResponse);
        }

        return $this->response(
            new DefaultResponse(
                new UserResource($userServiceResponse->data)
            )
        );
    }

    /**
     * Atualiza um usuário no banco de dados
     * PUT api/users/{uuid}
     *
     * @param string $uuid
     * @param UpdateUserRequest
     *
     * @return JsonResponse
     */
    public function update(UpdateUserRequest $request, string $uuid): JsonResponse
    {
        $userServiceResponse = $this->userService->update($uuid, $request->validated());

        if (!$userServiceResponse->success) {
            return $this->errorResponseFromService($userServiceResponse);
        }

        return $this->response(
            new DefaultResponse(
                new UserResource($userServiceResponse->data)
            )
        );
    }

    /**
     * Realiza a exclusão de um usuário
     * DELETE api/users/{uuid}
     *
     * @param string $uuid
     *
     * @return JsonResponse
     */
    public function destroy(string $uuid): JsonResponse
    {
        $userServiceResponse = $this->userService->delete($uuid);

        if (!$userServiceResponse->success) {
            return $this->errorResponseFromService($userServiceResponse);
        }

        return $this->response(
            new DefaultResponse()
        );
    }
}
