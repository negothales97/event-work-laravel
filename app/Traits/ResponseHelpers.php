<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use App\Http\Responses\DefaultResponse;

trait ResponseHelpers
{
    /**
     * Success json response helper
     *
     * @param $result
     * @param $message
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendResponse($data, $message = '', $status = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ], $status);
    }

    /**
     * Error json response helper
     *
     * @param $message
     * @param array $data
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendError($message, array $data = [], $status = 500)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data'    => $data,
        ], $status);
    }

    /**
     * Only data json response helper
     *
     * @param  $data
     * @param  integer $status
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendData($data, $status = 200)
    {
        return response()->json($data, $status);
    }

    /**
     * Helper para montar response de autenticação
     *
     * @param array $errors
     * @return JsonResponse
     */
    public function unauthenticatedErrorResponse(array $errors): JsonResponse
    {
        $defaultResponse = new DefaultResponse(
            null,
            false,
            $errors
        );

        return response()->json($defaultResponse->toArray(), $defaultResponse->code);
    }
}
