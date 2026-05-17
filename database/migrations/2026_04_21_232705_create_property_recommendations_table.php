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
        Schema::create('property_recommendations', function (Blueprint $table) {
            $table->foreignId('property_id')->primary()->constrained('properties')->onDelete('cascade');            
            $table->foreignId('user_id')->index()->constrained('users')->onDelete('cascade');
            
            // Menggunakan 'pushed_at' sebagai istilah pengganti 'sundul'
            $table->timestamp('pushed_at');

            // Jumlah koin yang diberikan
            $table->integer('amount_dabelyu_koin')->default(0);
            $table->integer('amount_token')->default(0);

            
            // Masa berlaku fitur rekomendasi
            $table->timestamp('expired_at');
            
            $table->timestamps();

            // Index untuk optimasi query sorting
            $table->index('pushed_at');       
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_recommendations');
    }
};
