<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoinPackage extends Model
{
    use HasFactory;
    protected $fillable = [
        'koin', 'harga', 'badge', 'theme', 'image', 'desc', 'saving', 'is_best', 'is_active'
    ];
}
