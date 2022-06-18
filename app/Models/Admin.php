<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;

class Admin extends BaseAuthModel
{
    use HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'password'
    ];

    protected $hidden = [
        'password'
    ];
}
