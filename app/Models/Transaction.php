<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $primaryKey = 'order_id';

    // 2. Beritahu Laravel bahwa primary key-nya BUKAN berjenis integer increment
    public $incrementing = false;

    // 3. Tentukan tipe data primary key-nya (karena varchar/string)
    protected $keyType = 'string';

    protected $guarded = [];

    protected $fillable = [
        'user_id',
        'order_id',
        'tipe',
        'status',
        'payment_info',
        'price'
    ];
}
