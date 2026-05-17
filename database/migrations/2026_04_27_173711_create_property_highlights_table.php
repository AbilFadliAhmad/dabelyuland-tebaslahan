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
        Schema::create('property_highlights', function (Blueprint $table) {
            $table->foreignId('property_id')->primary()->constrained('properties')->onDelete('cascade');
            $table->foreignId('user_id')->index()->constrained('users')->onDelete('cascade');
            
            $table->point('location'); // Koordinat disalin dari properti saat aktivasi
            $table->spatialIndex('location');

            $table->timestamp('pushed_at');
            $table->timestamp('expired_at');
            $table->integer('amount_dabelyu_koin')->default(0);
            $table->integer('amount_token')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_highlights');
    }
};
