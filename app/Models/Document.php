<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Document extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'file',
        'documentable_id',
        'documentable_type',
        'size'
    ];
}
