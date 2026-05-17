<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('coin_packages', function (Blueprint $table) {
            $table->id();
            $table->integer('koin'); // Jumlah koin
            $table->integer('harga'); // Harga dalam Rupiah
            $table->string('badge')->nullable(); // Teks promo (cth: Lebih Hemat)
            $table->string('theme')->default('basic'); // basic, popular, dark, gold
            $table->string('image')->default('koin-1.png'); // Nama file gambar
            $table->string('desc')->nullable(); // Deskripsi singkat
            $table->string('saving')->nullable(); // Teks hemat (cth: Hemat Rp 5.000)
            $table->boolean('is_best')->default(false); // Apakah ini paket terbaik?
            $table->boolean('is_active')->default(true); // Status aktif/nonaktif
            $table->timestamps();
        });

        DB::table('coin_packages')->insert([
            [
                'koin' => 60,
                'harga' => 1500,
                'badge' => 'Silver',
                'theme' => 'basic',
                'image' => 'koin-1.png',
                'is_best' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'koin' => 200,
                'harga' => 45000,
                'badge' => null,
                'theme' => 'basic',
                'image' => 'koin-1.png',
                'is_best' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'koin' => 500,
                'harga' => 99000,
                'badge' => null,
                'theme' => 'popular',
                'image' => 'koin-1.png',
                'is_best' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'koin' => 800,
                'harga' => 149000,
                'badge' => null,
                'theme' => 'dark',
                'image' => 'koin-1.png',
                'is_best' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'koin' => 1500,
                'harga' => 249000,
                'badge' => null,
                'theme' => 'gold',
                'image' => 'koin-1.png',
                'is_best' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

    }

    public function down()
    {
        Schema::dropIfExists('coin_packages');
    }
};