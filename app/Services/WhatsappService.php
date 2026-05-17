<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappService
{
    public static function sendOtp($target, $otp)
    {
        try {
            $token = env('FONNTE_TOKEN');

            // 1. Variasi pesan agar tidak terbaca sebagai spam yang identik
            $templates = [
                "Halo! Kode verifikasi TebasLahan Anda adalah: *$otp*. Rahasiakan kode ini.",
                "Ini kode OTP TebasLahan Anda: *$otp*. Jangan berikan kepada siapapun.",
                "Keamanan Akun: Gunakan kode *$otp* untuk masuk ke sistem TebasLahan.",
                "Berikut adalah kode verifikasi Anda: *$otp*. Berlaku selama 5 menit.",
                "TebasLahan OTP: *$otp*. Masukkan kode ini segera untuk melanjutkan."
            ];

            // Ambil satu pesan secara acak
            $message = $templates[array_rand($templates)];

            // 2. Kirim Request dengan Timeout agar tidak nge-hang jika server Fonnte lambat
            $response = Http::withoutVerifying()->timeout(10)->withHeaders([
                'Authorization' => $token,
            ])->post('https://api.fonnte.com/send', [
                'target'      => $target,
                'message'     => $message,
                'countryCode' => '62',
            ]);

            // 3. Cek jika API memberikan respon sukses (200 OK)
            if ($response->successful()) {
                return $response->json();
            }

            // Jika API Fonnte mengembalikan error (misal: 400, 401, 500)
            return [
                'status'     => false,
                'message'    => 'API Fonnte mengembalikan kesalahan.',
                'debug_info' => $response->json(), // Sangat berguna untuk debugging API
                'http_code'  => $response->status()
            ];

        } catch (\Throwable $th) {
            // 4. Log error ke file storage/logs/laravel.log
            Log::error("Fonnte OTP Error: " . $th->getMessage(), [
                'target' => $target,
                'file'   => $th->getFile(),
                'line'   => $th->getLine()
            ]);

            // Kembalikan response error untuk debugging di Postman/Frontend
            return [
                'status'      => false,
                'message'     => 'Terjadi kesalahan pada server backend.',
                'error_debug' => $th->getMessage(), // Pesan error asli
                'line_debug'  => $th->getLine(),    // Baris kode yang error
                'file_debug'  => $th->getFile()     // Lokasi file yang error
            ];
        }
    }
}