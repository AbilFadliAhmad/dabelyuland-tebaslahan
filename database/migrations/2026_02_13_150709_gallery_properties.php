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
        Schema::create('gallery_properties', function (Blueprint $table) {
            // Indeks dan kolom dasar
            $table->id();
            $table->string('image_path');
            $table->integer('sort');
            
            // Relasi dengan properti
            $table->foreignId('property_id')->constrained('properties', 'id')->cascadeOnDelete();

            // Waktu
            $table->timestamps();

            // Menambahkan index
            $table->unique(['property_id', 'sort'], 'prop_sort_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
