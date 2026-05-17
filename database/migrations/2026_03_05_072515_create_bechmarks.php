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
         Schema::create('properties_benchmark', function (Blueprint $table) {
            // Indeks dan kolom dasar
            $table->id();
            $table->string('session_id');
            $table->string('judul');
            $table->string('slug')->unique(); // Untuk SEO Friendly URL
            $table->text('deskripsi')->nullable();
            $table->unsignedBigInteger('harga');
            $table->enum('status', ['draft', 'menunggu', 'aktif', 'non-aktif', 'ditolak'])->default('draft');
            $table->enum('tipe', ['apartemen', 'rumah', 'ruko', 'kantor', 'gudang', 'tanah'])->default('rumah');
            $table->enum('kategori', ['3 Lantai', '2 Lantai', '1 Lantai', 'Lainnya', 'Tanah Kosong', 'Sawah', 'Kebun'])->default('Lainnya');
            $table->enum('legalitas', [
                'SHM',         // Hak Milik (Paling Kuat, Selamanya)
                'HGB',         // Hak Guna Bangunan (Jangka Waktu, Wajib untuk PT/Developer)
                'HP',          // Hak Pakai (Instansi Pemerintah / WNA)
                'SHMSRS',      // Strata Title (Apartemen/Rusun/Kios Mall)
                'HGU',         // Hak Guna Usaha (Perkebunan/Pertanian Skala Besar)
                'HPL',         // Hak Pengelolaan (Lahan Pemerintah/Kawasan Industri)
                'Girik',       // Bukti Pajak Lama
                'Petok_D',     // Istilah Girik di Jatim/Jateng
                'Letter_C',    // Register Desa
                'Eigendom',    // Surat Tanah Zaman Belanda (Verponding)
                'Sultan_Ground', // Khusus Daerah Yogyakarta (Tanah Keraton)
                'AJB',         // Akta Jual Beli (Belum naik status ke Sertifikat)
                'PPJB',        // Perjanjian Pengikatan Jual Beli (Biasanya Apartemen Indent)
                'Lainnya'
            ])->default('Lainnya');
            
            // Dimensi properti
            $table->integer('luas_tanah')->default(0);
            $table->integer('luas_bangunan')->default(0);
            $table->integer('jumlah_kamar_tidur')->default(0);
            $table->integer('jumlah_kamar_mandi')->default(0);
            
            // Lokasi Detail
            $table->text('alamat_detail')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            
            // Relasi dengan tabel lain
            $table->foreignId('kota_id')->nullable()->constrained('cities');
            $table->foreignId('kecamatan_id')->nullable()->constrained('subdistricts');
            $table->foreignId('user_id')->constrained('users', 'id')->cascadeOnDelete();
            
            // Tipe Transaksi
            $table->enum('transaksi', ['Dijual', 'Disewa'])->default('Dijual');
            $table->boolean('is_tersedia')->default(true); 
            
            // Waktu
            $table->timestamps();

            // Index
            $table->unique(['user_id', 'session_id'], 'user_session_unique');
            $table->index('kota_id');
            $table->index('kecamatan_id');
            $table->index('tipe');  
            $table->index('harga');
            $table->index('status');

            // Search Index (untuk pencarian)
            $table->fulltext(['judul', 'deskripsi']);
        });
        Schema::create('buildings', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->string('harga');
            $table->string('luas_tanah')->nullable();
            $table->string('luas_bangunan')->nullable();
            $table->integer('jumlah_kamar_tidur')->nullable();
            $table->integer('jumlah_kamar_mandi')->nullable();
            $table->string('lokasi');
            $table->enum('tipe_bangunan', ['rumah', 'apartemen', 'ruko', 'kantor', 'gudang'])->default('rumah');
            $table->enum('status', ['Dijual', 'Disewa'])->default('Dijual');
            $table->json('gambar')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
            $table->string('status_sewa')->default('kosong');
            $table->boolean('tampilkan_website')->default(true);
            
            // Indexing dasar jika diperlukan untuk benchmark
            $table->index('user_id');
        });
        Schema::create('lands', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->string('harga');
            $table->string('lokasi');
            $table->string('status')->default('tersedia');
            $table->string('kategori')->default('tanah_kosong');
            $table->decimal('luas_tanah', 15, 2);
            $table->string('sertifikat')->nullable();
            $table->json('gambar')->nullable();
            $table->boolean('tampilkan_website')->default(true);
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            // Indexing dasar jika diperlukan untuk benchmark
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties_benchmarks');
        Schema::dropIfExists('buildings');
        Schema::dropIfExists('lands');
    }
};
