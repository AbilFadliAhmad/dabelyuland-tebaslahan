<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmailService
{
    public static function sendOtp($target, $otp)
    {
        try {
            // Memanggil template HTML dan mengirim variabel $otp
            Mail::send('email.otp', ['otp' => $otp], function ($message) use ($target) {
                $message->to($target)
                        ->subject('Kode Verifikasi Keamanan - TebasLahan');
            });

            return [
                'status'  => true,
                'message' => 'Email OTP berhasil dikirim.'
            ];

        } catch (\Throwable $th) {
            // Log error diubah untuk mendeteksi error Resend
            Log::error("Resend SMTP Error: " . $th->getMessage());

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