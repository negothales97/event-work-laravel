<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends BaseModel
{
    use HasFactory;

    protected $fillable =
    [
        'uuid',
        'name',
        'label'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
