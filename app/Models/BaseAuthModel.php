<?php

namespace App\Models;

use Illuminate\Support\Str;
use App\Repositories\BaseEloquentBuilder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class BaseAuthModel extends Authenticatable
{
    use HasFactory;
    use SoftDeletes;

    public $incrementing = false;

    public $keyType = 'string';

    public static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->uuid = (string) Str::uuid();
        });
    }

    public function newEloquentBuilder($query)
    {
        return new BaseEloquentBuilder($query);
    }
}
