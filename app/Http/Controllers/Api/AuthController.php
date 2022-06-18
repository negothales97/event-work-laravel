<?php

namespace App\Http\Controllers\Api;

use App\Http\Responses\DefaultResponse;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\User\Auth\RegisterRequest;
use App\Http\Resources\Auth\LoginResource;
use App\Http\Resources\User\UserResource;
use App\Services\Contracts\AuthServiceInterface;
use App\Services\Contracts\CompanyServiceInterface;
use App\Services\Contracts\UserServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class AuthController extends ApiController
{
    /**
     * @var AuthServiceInterface
     */
    protected $authService;

    /**
     * @var UserServiceInterface
     */
    protected $userService;

    /**
     * @var CompanyServiceInterface
     */
    protected $companyService;

    public function __construct(
        AuthServiceInterface $authService,
        UserServiceInterface $userService,
        CompanyServiceInterface $companyService
    ) {
        $this->authService = $authService;
        $this->userService = $userService;
        $this->companyService = $companyService;
    }

    /**
     * Realiza o login do usuário
     *
     * @param LoginRequest $request
     *
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $authResponse = $this->authService->login($request->email, $request->password);

        if (!$authResponse->success) {
            return $this->errorResponseFromService($authResponse);
        }

        $tokenResponse = $this->authService->generateToken($authResponse->data);

        if (!$tokenResponse->success) {
            return $this->errorResponseFromService($tokenResponse);
        }

        return $this->response(new DefaultResponse(
            new LoginResource(
                $tokenResponse->data
            )
        ));
    }

    /**
     * @param RegisterRequest $request
     *
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        DB::beginTransaction();

        $companyResponse = $this->companyService->create([
            'name' => $request->company_name,
            'cnpj' => $request->cnpj
        ]);
        if (!$companyResponse->success) {
            DB::rollback();
            return $this->errorResponseFromService($companyResponse);
        }

        $data = $request->except('cnpj', 'company_name');
        $data['company_id'] = $companyResponse->data->uuid;

        $userResponse = $this->userService->create($data);
        if (!$userResponse->success) {
            DB::rollBack();
            return $this->errorResponseFromService($userResponse);
        }

        DB::commit();

        return $this->response(new DefaultResponse(
            new UserResource($userResponse->data)
        ));
    }
}
