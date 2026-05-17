<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // order_id dari Midtrans (Contoh: TBL-XXXX-123456)
            $table->string('order_id')->unique(); 
            
            $table->enum('tipe', ['membership', 'koin']);
            
            // Status resmi standar Midtrans
            $table->enum('status', [
                'pending', 
                'settlement', // Berhasil
                'expire',     // Kedaluwarsa/Waktu Habis
                'cancel',     // Dibatalkan oleh user/sistem
                'deny',       // Ditolak pihak bank/fraud
                'refund',
                'failure'
            ])->default('pending');

            $table->timestamps();

            // ==========================================
            // OPTIMASI INDEXING (Sesuai Kebutuhan Anda)
            // ==========================================
            
            // 1. Index Komposit untuk Cek Transaksi Pending (Sangat Cepat)
            $table->index(['user_id', 'tipe', 'status']);
            
            // 2. Index Komposit untuk Riwayat Berurut (Sangat Cepat)
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};