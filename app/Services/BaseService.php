<?php

namespace App\Services;

use App\Models\Log;
use Illuminate\Support\Str;
use App\Services\Responses\InternalError;
use App\Exceptions\PolicyServiceException;
use App\Services\Responses\ServiceResponse;

class BaseService
{
    /**
     * Retorno de erro padrão
     *
     * @param  Throwable $e
     * @param  string    $acao
     * @param  string|array    $data
     *
     * @return array
     */
    protected function defaultErrorReturn(
        \Throwable $e,
        $data = [],
        $code = null,
        $logLevel = 'ERROR'
    ): ServiceResponse {

        $log = Log::create([
            'code' => $code,
            'data' => json_encode($data),
            'log_level' => $logLevel,
            'exception' => $e,
            'user_id' => user('id')
        ]);

        return new ServiceResponse(
            false,
            "Ocorreu um erro inesperado. Por favor, tente novamente",
            $data,
            $log->uuid,
            [
                new InternalError(
                    $e->getMessage(),
                    $code,
                    $log->uuid
                )
            ]
        );
    }
}
