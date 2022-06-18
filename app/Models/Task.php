<?php

namespace App\Models;

class Task extends BaseModel
{
    protected $fillable = [
        'uuid',
        'name',
        'description',
        'priority',
        'status',
        'admin_id',
        'user_id',
        'company_id'
    ];
}
