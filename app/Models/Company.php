<?php

namespace App\Models;

class Company extends BaseModel
{
    protected $fillable = [
        'uuid',
        'name',
        'cnpj',
    ];
}
