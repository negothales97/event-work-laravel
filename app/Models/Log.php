<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Log extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'code',
        'user_id',
        'data',
        'log_level',
        'exception'
    ];

    public function setExceptionAttribute($e)
    {
        if (!is_null($e)) {
            $this->attributes['exception'] = json_encode([
                'msg' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
        }
    }
}
