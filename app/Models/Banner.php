<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;
      protected $fillable = [
        'image',
        'user_id', // active/inactive
        'status',
        'expired_at',
        'amount_dabelyu_koin',
        'amount_token',
    ];
}
