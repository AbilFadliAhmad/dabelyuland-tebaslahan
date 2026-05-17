<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_wallets', function (Blueprint $table) {
            // Menggunakan user_id sebagai Primary Key (tanpa kolom ID increment)
            $table->foreignId('user_id')->primary()->constrained('users')->onDelete('cascade');
            
            // Relasi ke tabel memberships
            $table->foreignId('membership_id')->nullable()->constrained('memberships')->onDelete('set null');
            
            // Statistik Koin & Kuota
            $table->integer('dabelyu_koin')->default(0);
            $table->integer('recommendation_quota')->default(0);
            $table->integer('highlight_quota')->default(0);
            $table->integer('banner_quota')->default(0);
            $table->integer('push_quota')->default(0); // Ini adalah token_sundul
            
            // Masa aktif membership untuk dompet ini
            $table->timestamp('expired_at')->nullable();
            
            $table->timestamps();
        });

        DB::table('user_wallets')->insert([
            [
                'user_id' => 1, // Mengacu pada user@gmail.com
                'membership_id' => 1, // Paket Bronze
                'dabelyu_koin' => 100, // Saldo koin awal
                'recommendation_quota' => 1,
                'highlight_quota' => 0,
                'banner_quota' => 0,
                'push_quota' => 3, // Token Sundul
                'expired_at' => Carbon::now()->addDays(30),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_id' => 2, // Mengacu pada admin@gmail.com
                'membership_id' => 3, // Paket Gold Premium
                'dabelyu_koin' => 50000, // Saldo koin admin besar untuk testing
                'recommendation_quota' => 50,
                'highlight_quota' => 10,
                'banner_quota' => 1,
                'push_quota' => 150,
                'expired_at' => Carbon::now()->addDays(365), // Admin dibuat setahun agar tidak cepat expired
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('user_wallets');
    }
};