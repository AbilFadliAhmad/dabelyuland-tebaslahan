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
        Schema::create('property_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->integer('views_count')->default(0);
            $table->integer('whatsapp_clicks_count')->default(0); // Tombol WA yang diklik
            
            // KOLOM SUMBER TRAFFIC (Dari mana mereka datang)
            $table->integer('source_wa')->default(0);
            $table->integer('source_fb')->default(0);
            $table->integer('source_ig')->default(0);
            $table->integer('source_twitter')->default(0);
            $table->integer('source_other')->default(0);
            
            $table->unique(['property_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_product_analytics');
    }
};
