<?php

namespace App\Http\Responses;

use Exception;
use App\Services\Responses\InternalError;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Responses\Contracts\ResponseInterface;
use Illuminate\Http\Resources\Json\ResourceCollection;

class DefaultResponse implements ResponseInterface
{
    /**
     * @var array
     */
    private $parameters;

    /**
     * @param mixed                $data    Dados de retorno
     * @param bool                 $success Processado com sucesso
     * @param array<InternalError> $errors  Lista de erros internos acontecidos
     * @param int                  $code    HTTP Code response
     */
    public function __construct(
        $data = null,
        bool $success = true,
        array $errors = [],
        int $code = 200
    ) {
        $this->parameters = [
            'success' => $success,
            'request' => asset(request()->path(), true),
            'method' => strtoupper(request()->method()),
            'code' => $code,
        ];

        if ($data instanceof ResourceCollection && $data->resource instanceof LengthAwarePaginator) {
            $paginator = $data->resource;
            // Adiciona os parametros get da url, ex: filtros para paginação
            $paginator->appends(request()->query());

            $this->parameters = array_merge($this->parameters, $paginator->toArray());

            if ($this->parameters['total'] === 0) {
                $this->parameters['data'] = null;
            }
        } else {
            $this->parameters['data'] = $data;
        }

        if (count($errors)) {
            $this->parameters['errors'] = array_map(function ($error) {
                if (!$error instanceof InternalError) {
                    throw new Exception('Error inserido não é do tipo InternalError');
                }

                return $error->toArray();
            }, $errors);
        }
    }

    /**
     * Retorna o array de parametros dessa classe
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->parameters;
    }

    /**
     * Metodo para pegar algum parametro declarado na classe,
     * retorna null se não existir
     *
     * @param  string $parameter
     *
     * @return mixed
     */
    public function __get($parameter)
    {
        return $this->parameters[$parameter] ?? null;
    }
}
