<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Catalog extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category_id',
        'description',
        'care_level',
        'face_shape',
        'hair_type',
        'tips',
        'image_url',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
