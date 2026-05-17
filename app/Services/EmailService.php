<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmailService
{
    public static function sendOtp($target, $otp)
    {
        try {
            // Template pesan variatif untuk menghindari filter spam email
            $templates = [
                "Kode verifikasi Anda adalah: <b>$otp</b>. Segera masukkan kode ini di aplikasi TebasLahan.",
                "Halo! Gunakan kode <b>$otp</b> untuk memverifikasi akun TebasLahan Anda.",
                "Ini adalah kode OTP Anda: <b>$otp</b>. Kode ini rahasia, jangan berikan kepada siapapun."
            ];

            $messageContent = $templates[array_rand($templates)];

            // Mengirim email menggunakan fungsi bawaan Laravel
            Mail::send([], [], function ($message) use ($target, $messageContent) {
                $message->to($target)
                    ->subject('Kode Verifikasi TebasLahan')
                    // Mengirim sebagai HTML agar tampilan lebih bagus
                    ->html($messageContent);
            });

            return [
                'status'  => true,
                'message' => 'Email OTP berhasil dikirim.'
            ];

        } catch (\Throwable $th) {
            // Log error untuk debugging di storage/logs/laravel.log
            Log::error("Gmail SMTP Error: " . $th->getMessage());

            return [
                'status'      => false,
                'message'     => 'Gagal mengirim email OTP.',
                'error_debug' => $th->getMessage(),
                'file_debug'  => $th->getFile(),
                'line_debug'  => $th->getLine()
            ];
        }
    }
}