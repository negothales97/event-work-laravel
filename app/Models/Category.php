<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'name',
        'color',
        'status',
        'parent_id'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'parent_id', 'id');
    }

    public function categories()
    {
        return $this->hasMany(Category::class, 'parent_id', 'id');
    }
}
