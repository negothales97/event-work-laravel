<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\ApiController;
use App\Http\Responses\DefaultResponse;
use App\Http\Requests\ResetPassword\ResetPasswordRequest;
use App\Services\Contracts\ResetPasswordServiceInterface;
use App\Http\Requests\ResetPassword\ChangePasswordRequest;

class ResetPasswordController extends ApiController
{
    /**
     * @var ResetPasswordServiceInterface
     */
    protected $resetPasswordService;

    public function __construct(ResetPasswordServiceInterface $resetPasswordService)
    {
        $this->resetPasswordService = $resetPasswordService;
    }

    /**
     * Envia o link com token para o reset de senha
     * POST api/forgot-password
     *
     * @param ResetPasswordRequest $request
     *
     * @return JsonResponse
     */
    public function sendResetLink(ResetPasswordRequest $request): JsonResponse
    {
        $resetPasswordService = $this->resetPasswordService->sendResetLink($request->validated());

        if (!$resetPasswordService->success) {
            return $this->errorResponseFromService($resetPasswordService);
        }

        return $this->response(
            new DefaultResponse()
        );
    }

    /**
     * Reseta a senha do usuário
     * POST api/reset-password
     *
     * @param ChangePasswordRequest $request
     *
     * @return JsonResponse
     */
    public function resetPassword(ChangePasswordRequest $request): JsonResponse
    {
        $resetPasswordService = $this->resetPasswordService->resetPassword($request->validated());

        if (!$resetPasswordService->success) {
            return $this->errorResponseFromService($resetPasswordService);
        }

        return $this->response(
            new DefaultResponse()
        );
    }
}
