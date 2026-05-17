<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            // Indeks dan kolom dasar
            $table->id();
            $table->string('session_id');
            $table->string('judul');
            $table->string('slug')->unique(); // Untuk SEO Friendly URL
            $table->text('deskripsi')->nullable();
            $table->unsignedBigInteger('harga');
            $table->enum('status', ['draft', 'menunggu', 'aktif', 'non-aktif', 'ditolak', 'dihapus'])->default('draft');
            $table->enum('kategori', ['3 Lantai', '2 Lantai', '1 Lantai', 'Lainnya', 'Tanah Kosong', 'Sawah', 'Kebun'])->default('Lainnya');
            $table->enum('tipe', ['apartemen', 'rumah', 'ruko', 'kantor', 'gudang', 'tanah'])->default('rumah');
            $table->enum('legalitas', [
            // ============================================================
            // TIPE UMUM (Dapat digunakan untuk Rumah, Ruko, Apartemen, & Tanah)
            // ============================================================
            'SHM',           // Hak Milik (Paling Kuat, Selamanya - Rumah/Tanah)
            'HGB',           // Hak Guna Bangunan (Wajib untuk PT/Developer - Rumah/Ruko/Apartemen)
            'HP',            // Hak Pakai (Instansi Pemerintah / WNA)
            'SHMSRS',        // Strata Title (Khusus Apartemen/Rusun/Kios Mall)
            'AJB',           // Akta Jual Beli (Proses transisi ke Sertifikat)
            'PPJB',          // Perjanjian Pengikatan Jual Beli (Biasanya Apartemen Indent)

            // ============================================================
            // KHUSUS TIPE TANAH / LAHAN (Hanya berlaku untuk Properti Tanah)
            // ============================================================
            'HGU',           // Hak Guna Usaha (Perkebunan/Pertanian Skala Besar)
            'HPL',           // Hak Pengelolaan (Lahan Pemerintah/Kawasan Industri)
            'Girik',         // Bukti Pajak Lama (Lahan adat/belum bersertifikat)
            'Petok_D',       // Sebutan Girik untuk wilayah Jatim/Jateng
            'Letter_C',      // Catatan pendaftaran tanah di desa
            'Eigendom',      // Hak milik zaman kolonial (Verponding)
            'Sultan_Ground', // Tanah milik Keraton (Khusus Daerah Istimewa Yogyakarta)
            
            'Lainnya'
        ])->default('Lainnya');
            
            // Dimensi properti
            $table->integer('luas_tanah')->default(0);
            $table->integer('luas_bangunan')->default(0);
            $table->integer('jumlah_kamar_tidur')->default(0);
            $table->integer('jumlah_kamar_mandi')->default(0);
            
            // Lokasi Detail
            $table->text('alamat_detail')->nullable();
            $table->string('kota')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            
            // Relasi dengan tabel lain
            $table->foreignId('user_id')->constrained('users', 'id')->cascadeOnDelete();
            
            // Tipe Transaksi
            $table->enum('transaksi', ['Dijual', 'Disewa'])->default('Dijual');
            $table->boolean('is_tersedia')->default(true); 
            
            // Waktu
            $table->timestamps();   

            // Soft Delete
            $table->date('deleted_at')->nullable();
            $table->string('reasen_delete')->nullable();

            // Index
            $table->unique(['user_id', 'session_id'], 'user_session_unique');
            $table->index('tipe');  
            $table->index('harga');
            $table->index('status');
            $table->index('kota');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};