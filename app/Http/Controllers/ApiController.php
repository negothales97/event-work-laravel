<?php

namespace App\Http\Controllers;

use App\Traits\ResponseHelpers;
use Illuminate\Http\JsonResponse;
use App\Http\Responses\DefaultResponse;
use App\Services\Responses\InternalError;
use App\Services\Responses\ServiceResponse;
use App\Http\Responses\Contracts\ResponseInterface;
use Illuminate\Foundation\Validation\ValidatesRequests;

class ApiController
{
    use ResponseHelpers;
    use ValidatesRequests;

    /**
     * Helper para ser usado na resposta de todas as controllers filhas
     *
     * @param  ResponseInterface $response
     *
     * @return JsonResponse
     */
    public function response(ResponseInterface $response): JsonResponse
    {
        return response()->json($response->toArray(), $response->code);
    }

    /**
     * Helper para montar response de error a partir de um ServiceResponse
     *
     * @param  ServiceResponse $serviceResponse
     *
     * @return JsonResponse
     */
    public function errorResponseFromService(ServiceResponse $serviceResponse, $data = null): JsonResponse
    {
        $errors = $serviceResponse->internalErrors;
        if (!count($errors)) {
            $errors = [
                new InternalError(
                    $serviceResponse->message,
                    null,
                    $serviceResponse->idLog
                )
            ];
        }

        return $this->response(new DefaultResponse(
            $data,
            false,
            $errors,
            count($serviceResponse->internalErrors) ? 200 : 500
        ));
    }
}
