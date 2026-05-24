<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            // Primary Key (BIGINT UNSIGNED)
            $table->id(); 
            
            // SIAPA (Actor)
            $table->unsignedBigInteger('user_id');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            
            // APA (Menggunakan Enum sesuai struktur bisnis TebasLahan)
            $table->enum('type', ['properti', 'highlight', 'rekomendasi', 'topup', 'membership', 'sesi', 'service', 'banner', 'kontak', 'testimoni', 'portofolio', 'news', 'quest']);
            $table->enum('action', ['create', 'update', 'delete']);
            
            // DETAIL
            $table->text('description')->nullable();
            $table->timestamps(); // otomatis membuat created_at dan updated_at

            // =========================================================================
            // STRATEGI OPTIMASI INDEKS
            // =========================================================================
            
            // INDEX 1: Untuk fitur pencarian/filtering wajib isi
            $table->index(['type', 'action', 'created_at'], 'idx_audit_filtering');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};