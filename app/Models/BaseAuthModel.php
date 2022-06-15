<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class BaseAuthModel extends Authenticatable
{
    use SoftDeletes;

    public static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->uuid = (string) Str::uuid();
        });
    }
}
