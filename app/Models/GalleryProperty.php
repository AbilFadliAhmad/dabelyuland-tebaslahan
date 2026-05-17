<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GalleryProperty extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'sort',
        'image_path',
    ];
}
