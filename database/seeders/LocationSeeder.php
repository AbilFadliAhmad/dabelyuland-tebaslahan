<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Mengambil data Provinsi dari wilayah.id...');
        $provincesResponse = Http::withoutVerifying()->get('https://wilayah.id/api/provinces.json');

        if (!$provincesResponse->successful()) {
            $this->command->error('Gagal mengambil data provinsi! Pastikan koneksi internet stabil.');
            return;
        }

        $provinces = $provincesResponse->json('data');

        // Variabel penampung untuk proses Bulk Insert/Upsert
        $citiesToUpsert = [];
        $apiRegencyToCleanName = []; // Mapping kode API -> Nama Bersih (Misal: 3507 -> Malang)

        // ==========================================
        // 1. PROSES KOTA / KABUPATEN
        // ==========================================
        $this->command->warn('Memproses data Kota & Kabupaten...');
        $barCities = $this->command->getOutput()->createProgressBar(count($provinces));

        foreach ($provinces as $province) {
            $regenciesResponse = Http::withoutVerifying()->get("https://wilayah.id/api/regencies/{$province['code']}.json");
            
            if ($regenciesResponse->successful()) {
                $regencies = $regenciesResponse->json('data');
                
                foreach ($regencies as $regency) {
                    // Bersihkan awalan KOTA atau KABUPATEN (Sesuai request Anda)
                    $cleanName = trim(str_ireplace(['KOTA ', 'KABUPATEN '], '', $regency['name']));

                    // Gunakan Array Associative agar otomatis menimpa duplikat (Merge)
                    $citiesToUpsert[$cleanName] = [
                        'name' => $cleanName
                    ];

                    // Simpan mapping kode API ke nama bersih untuk pencarian kecamatan nanti
                    $apiRegencyToCleanName[$regency['code']] = $cleanName;
                }
                }
                $this->command->info('Data Bersih terbaru: '. json_encode($apiRegencyToCleanName));
                $this->command->info('Data Kota terbaru: '. json_encode($citiesToUpsert));
                $barCities->advance(); // Update progress bar
        }
        $barCities->finish();
        $this->command->line("\n"); // Baris baru

        // Simpan ke Database
        $this->command->info('Menyimpan data Kota ke database (Upsert)...');
        DB::table('cities')->upsert(
            array_values($citiesToUpsert),
            ['name'], // Unique Key
            ['name']  // Update jika ada
        );


        // ==========================================
        // 2. PROSES KECAMATAN (SUBDISTRICTS)
        // ==========================================
        $this->command->warn('Memproses data Kecamatan (Ini akan memakan waktu karena banyak request API)...');
        $barDistricts = $this->command->getOutput()->createProgressBar(count($apiRegencyToCleanName));

        // Ambil data Kota dari DB kita (yang sudah dibersihkan) untuk mendapatkan ID aslinya
        $dbCities = DB::table('cities')->pluck('id', 'name')->toArray();
        $subdistrictsToUpsert = [];

        foreach ($apiRegencyToCleanName as $apiCode => $cleanCityName) {
            // Cocokkan nama bersih dengan ID kota di database kita
            $localCityId = $dbCities[$cleanCityName] ?? null;

            if ($localCityId) {
                $districtsResponse = Http::withoutVerifying()->get("https://wilayah.id/api/districts/{$apiCode}.json");
                
                if ($districtsResponse->successful()) {
                    $districts = $districtsResponse->json('data');
                    
                    foreach ($districts as $district) {
                        $subdistrictsToUpsert[] = [
                            'city_id' => $localCityId,
                            'name'    => $district['name']
                        ];
                    }
                }
            }
            $barDistricts->advance(); // Update progress bar
        }
        $barDistricts->finish();
        $this->command->line("\n"); // Baris baru

        // Simpan ke Database (Lakukan Chunk agar memori tidak jebol karena datanya 7000+)
        $this->command->info('Menyimpan data Kecamatan ke database...');
        $chunks = array_chunk($subdistrictsToUpsert, 500);
        
        foreach ($chunks as $chunk) {
            DB::table('subdistricts')->upsert(
                $chunk,
                ['city_id', 'name'], // Kombinasi unik dari Migration yang kita buat sebelumnya
                ['name']
            );
        }

        $this->command->info('🎉 Selesai! Seluruh data Kota dan Kecamatan berhasil di-seed dan disatukan.');
    }
}