<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;
      protected $fillable = [
        'image',
        'is_active', // active/inactive
        'user_id', // active/inactive
    ];
}
