<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\BaseEloquentBuilder;
use Illuminate\Database\Eloquent\SoftDeletes;

class BaseModel extends Model
{
    use SoftDeletes;

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
