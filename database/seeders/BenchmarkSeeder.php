<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BenchmarkSeeder extends Seeder
{
    public function run(): void
    
    {
        // Bypass Limitasi Memori
        ini_set('memory_limit', '-1');

        $kotaId = 135; // Jombang
        $kecamatanIds = [2343, 2344, 2345, 2346, 2347, 2348, 2349, 2350, 2351, 2352, 2353, 2354, 2355, 2356, 2357, 2358, 2359, 2360, 2361, 2362, 2363];
        $userId = 2; // Menggunakan user_id 2 sesuai request
        $now = now();

        $this->command->info('Memulai insert 50.000 data Buildings...');
        $this->seedBuildings($userId, $now, 50000);

        $this->command->info('Memulai insert 50.000 data Lands...');
        $this->seedLands($userId, $now, 50000);

        $this->command->info('Memulai insert 100.000 data Properties Benchmark...');
        $this->seedPropertiesBenchmark($userId, $kotaId, $kecamatanIds, $now, 100000);
        
        $this->command->info('🎉 Proses Benchmark Seeding Selesai secara total (200.000 baris)!');
    }

    private function seedBuildings($userId, $now, $total)
    {
        $data = [];
        $bar = $this->command->getOutput()->createProgressBar($total);

        for ($i = 1; $i <= $total; $i++) {
            $data[] = [
                'judul' => "Rumah Strategis Tipe $i",
                'deskripsi' => 'Deskripsi dummy untuk bangunan ke ' . $i,
                'harga' => (string)rand(100000000, 900000000),
                'luas_tanah' => (string)rand(60, 200),
                'luas_bangunan' => (string)rand(45, 150),
                'jumlah_kamar_tidur' => rand(2, 5),
                'jumlah_kamar_mandi' => rand(1, 3),
                'lokasi' => 'Jombang, Jawa Timur',
                'tipe_bangunan' => 'rumah',
                'status' => 'Dijual',
                'gambar' => null,
                'user_id' => $userId,
                'created_at' => $now,
                'updated_at' => $now,
                'status_sewa' => 'kosong',
                'tampilkan_website' => 1,
            ];

            // Insert per 1000 baris
            if ($i % 1000 == 0) {
                DB::table('buildings')->insert($data);
                $data = [];
            }
            $bar->advance();
        }
        
        // Eksekusi sisa data jika total tidak habis dibagi 1000
        if (!empty($data)) {
            DB::table('buildings')->insert($data);
        }
        
        $bar->finish();
        $this->command->line("\n"); // Baris baru
    }

    private function seedLands($userId, $now, $total)
    {
        $data = [];
        $bar = $this->command->getOutput()->createProgressBar($total);

        for ($i = 1; $i <= $total; $i++) {
            $data[] = [
                'judul' => "Tanah Kavling Jombang $i",
                'deskripsi' => 'Deskripsi dummy untuk tanah ke ' . $i,
                'harga' => (string)rand(50000000, 500000000),
                'lokasi' => 'Jombang, Jawa Timur',
                'status' => 'tersedia',
                'kategori' => 'Tanah Kosong',
                'luas_tanah' => rand(100, 1000), 
                'sertifikat' => 'SHM',
                'gambar' => null,
                'tampilkan_website' => 1,
                'user_id' => $userId,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if ($i % 1000 == 0) {
                DB::table('lands')->insert($data);
                $data = [];
            }
            $bar->advance();
        }

        if (!empty($data)) {
            DB::table('lands')->insert($data);
        }

        $bar->finish();
        $this->command->line("\n"); 
    }

    private function seedPropertiesBenchmark($userId, $kotaId, $kecamatanIds, $now, $total)
    {
        $data = [];
        $bar = $this->command->getOutput()->createProgressBar($total);

        for ($i = 1; $i <= $total; $i++) {
            $uniqueString = Str::random(7) . '-' . $i; 
            
            $tipe = ($i % 2 == 0) ? 'rumah' : 'tanah';
            $kategori = ($tipe == 'tanah') ? 'Tanah Kosong' : '1 Lantai';

            $data[] = [
                'session_id' => 'sess_' . $uniqueString,
                'judul' => "Properti Benchmark $i",
                'slug' => 'properti-benchmark-' . $uniqueString,
                'deskripsi' => 'Deskripsi benchmark ke ' . $i,
                'harga' => rand(100000000, 900000000), 
                'status' => 'aktif',
                'tipe' => $tipe,
                'kategori' => $kategori,
                'legalitas' => 'SHM',
                'luas_tanah' => rand(60, 500),
                'luas_bangunan' => ($tipe == 'rumah') ? rand(45, 200) : 0,
                'jumlah_kamar_tidur' => ($tipe == 'rumah') ? rand(2, 4) : 0,
                'jumlah_kamar_mandi' => ($tipe == 'rumah') ? rand(1, 2) : 0,
                'alamat_detail' => 'Jalan Dummy No ' . $i,
                'latitude' => null,
                'longitude' => null,
                'kota_id' => $kotaId, 
                'kecamatan_id' => $kecamatanIds[array_rand($kecamatanIds)], 
                'user_id' => $userId,
                'transaksi' => 'Dijual',
                'is_tersedia' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if ($i % 1000 == 0) {
                DB::table('properties_benchmark')->insert($data);
                $data = [];
            }
            $bar->advance();
        }

        if (!empty($data)) {
            DB::table('properties_benchmark')->insert($data);
        }

        $bar->finish();
        $this->command->line("\n"); 
    }
}