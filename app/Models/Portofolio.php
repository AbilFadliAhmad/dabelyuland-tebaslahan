<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Portofolio extends Model
{
   use HasFactory;

    protected $fillable = ['gambar', 'judul', 'pemilik', 'alamat', 'tipe']; // Kolom yang bisa diisi

    // Jika ingin menggunakan factory, tambahkan ini:
    protected static function newFactory()
    {
        // return \Database\Factories\PortfolioFactory::new();
    }
}
