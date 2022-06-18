<?php

namespace App\Models;

class Document extends BaseModel
{
    protected $fillable = [
        'uuid',
        'file',
        'documentable_id',
        'documentable_type',
        'size'
    ];
}
