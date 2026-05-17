<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Tambahkan ini untuk akses DB facade

return new class extends Migration
{
    public function up(): void
    {
        // 1. Membuat Struktur Tabel
        Schema::create('memberships', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('price');
            $table->unsignedInteger('discount')->nullable()->max(100);
            $table->integer('duration_days')->default(30);
            
            $table->integer('recommendation_quota')->default(0);
            $table->integer('push_quota')->default(0);
            $table->integer('highlight_quota')->default(0);
            $table->integer('banner_quota')->default(0);
            
            
            $table->string('badge_name')->nullable();
            $table->text('description')->nullable();
            
            $table->timestamps();
        });

        // 2. Memasukkan Data Awal (Seeding)
        DB::table('memberships')->insert([
            [
                'id' => 1,
                'name' => 'Bronze',
                'price' => 0,
                'duration_days' => 30,
                'recommendation_quota' => 1,
                'push_quota' => 3,
                'highlight_quota' => 0,
                'banner_quota' => 0,
                'badge_name' => 'Dasar',
                'description' => 'Cocok untuk mencoba layanan kami.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Silver Pro',
                'price' => 49000,
                'duration_days' => 30,
                'recommendation_quota' => 10,
                'push_quota' => 30,
                'highlight_quota' => 2,
                'banner_quota' => 0,
                'badge_name' => 'Paling Laris',
                'description' => 'Untuk agen profesional dengan banyak listing.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'Gold Premium',
                'price' => 149000,
                'duration_days' => 30,
                'recommendation_quota' => 50,
                'push_quota' => 150,
                'highlight_quota' => 10,
                'banner_quota' => 1,
                'badge_name' => 'Eksklusif',
                'description' => 'Dominasi pasar dengan visibilitas maksimal.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('memberships');
    }
};