<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\BaseEloquentBuilder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BaseModel extends Model
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
