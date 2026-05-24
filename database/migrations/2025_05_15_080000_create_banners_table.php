<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            // Users
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Kolom dasar
            $table->string('image')->nullable(); // path gambar
            $table->enum('status', ['aktif', 'nonaktif', 'menunggu'])->default('menunggu'); 

            // Waktu
            $table->date('expired_at')->default(now()->addDays(7));
            $table->timestamps();

            // Jumlah koin yang diberikan
            $table->integer('amount_dabelyu_koin')->default(0);
            $table->integer('amount_token')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
