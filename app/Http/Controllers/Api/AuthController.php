<?php

namespace App\Http\Controllers\Api;

use App\Http\Responses\DefaultResponse;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Auth\LoginResource;
use App\Services\Contracts\AuthServiceInterface;
use Illuminate\Http\JsonResponse;

class AuthController extends ApiController
{
    /**
     * @var AuthServiceInterface
     */
    protected $authService;

    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
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
}
