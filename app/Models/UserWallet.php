<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserWallet extends Model
{
    protected $table = 'user_wallets';
    
    // Jika primary key kamu bukan 'id', kamu WAJIB deklarasikan ini
    protected $primaryKey = 'user_id'; 
    
    // Jika primary key bukan auto-increment (karena pake user_id)
    public $incrementing = false; 

    protected $guarded = [];

    public function membership() {
        return $this->belongsTo(Membership::class, 'membership_id', 'id');
    }
}