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
    public function up()
{
    Schema::create('service_prices', function (Blueprint $table) {
        $table->id();
        $table->enum('jenis_layanan', ['highlight', 'rekomendasi', 'sundul', 'banner'])->index();
        $table->string('nama_durasi');   // misal: 1 Minggu
        $table->integer('jumlah_hari');  // misal: 7
        $table->integer('biaya_koin');   // misal: 60
        $table->timestamps();
    });

    // Insert Data Master Langsung di Migration
    DB::table('service_prices')->insert([
        // Sundul
        ['jenis_layanan' => 'sundul', 'nama_durasi' => 'Token Sundul', 'jumlah_hari' => 0, 'biaya_koin' => 10, 'created_at' => now(), 'updated_at' => now()],

        // Highlight
        ['jenis_layanan' => 'highlight', 'nama_durasi' => '1 Minggu', 'jumlah_hari' => 7, 'biaya_koin' => 50, 'created_at' => now(), 'updated_at' => now()],
        ['jenis_layanan' => 'highlight', 'nama_durasi' => '2 Minggu', 'jumlah_hari' => 14, 'biaya_koin' => 90, 'created_at' => now(), 'updated_at' => now()],
        ['jenis_layanan' => 'highlight', 'nama_durasi' => '1 Bulan', 'jumlah_hari' => 30, 'biaya_koin' => 170, 'created_at' => now(), 'updated_at' => now()],
        
        // Rekomendasi
        ['jenis_layanan' => 'rekomendasi', 'nama_durasi' => '1 Minggu', 'jumlah_hari' => 7, 'biaya_koin' => 30, 'created_at' => now(), 'updated_at' => now()],
        ['jenis_layanan' => 'rekomendasi', 'nama_durasi' => '2 Minggu', 'jumlah_hari' => 14, 'biaya_koin' => 55, 'created_at' => now(), 'updated_at' => now()],
        ['jenis_layanan' => 'rekomendasi', 'nama_durasi' => '1 Bulan', 'jumlah_hari' => 30, 'biaya_koin' => 100, 'created_at' => now(), 'updated_at' => now()],

        // Banner
        ['jenis_layanan' => 'banner', 'nama_durasi' => '1 Minggu', 'jumlah_hari' => 7, 'biaya_koin' => 100, 'created_at' => now(), 'updated_at' => now()],
        ['jenis_layanan' => 'banner', 'nama_durasi' => '2 Minggu', 'jumlah_hari' => 14, 'biaya_koin' => 180, 'created_at' => now(), 'updated_at' => now()],
        ['jenis_layanan' => 'banner', 'nama_durasi' => '1 Bulan', 'jumlah_hari' => 30, 'biaya_koin' => 350, 'created_at' => now(), 'updated_at' => now()],

    ]);
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_prices');
    }
};
