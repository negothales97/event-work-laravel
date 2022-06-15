<?php

namespace App\Http\Responses\Contracts;

interface ResponseInterface
{
    public function toArray(): array;
    public function __get($parameter);
}
