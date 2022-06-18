<?php

namespace App\Services;

use App\Services\Responses\InternalError;
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


        return new ServiceResponse(
            false,
            "Ocorreu um erro inesperado. Por favor, tente novamente",
            $data,
            null,
            [
                new InternalError(
                    $e->getMessage(),
                    $code,
                )
            ]
        );
    }
}
