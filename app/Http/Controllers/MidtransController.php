<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Membership;
use App\Models\UserWallet;
use Midtrans\Config;
use Illuminate\Http\Request;
use App\Models\Transaction;
use Midtrans\Transaction as MidtransTransaction;


class MidtransController extends Controller
{
    public function handleWebhook(Request $request)
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');

        // 1. Verifikasi Signature Key untuk Keamanan
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . config('midtrans.server_key'));
        
        if ($hashed !== $request->signature_key) {
            Log::warning("Midtrans Webhook: Invalid Signature untuk Order ID " . $request->order_id);
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $transactionStatus = $request->transaction_status;
        $orderId = $request->order_id;
        
        // 2. Cari transaksi lokal berdasarkan order_id
        $transaction = Transaction::where('order_id', $orderId)->first();
        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        // 3. Logika Perubahan Status
        if (in_array($transactionStatus, ['capture', 'settlement'])) {
            // Pastikan belum pernah disettlement sebelumnya untuk mencegah double-update
            if ($transaction->status !== 'settlement') {
                try {
                    // Tarik data detail dari Midtrans untuk mendapatkan metadata (karena payload webhook kadang tidak mengirim metadata lengkap)
                    $midtransDetail = (object) MidtransTransaction::status($orderId);
                    $transaction->tipe == 'membership' 
                    ? $this->handleSuccessfulPaymentMembership($transaction, $midtransDetail->metadata->membership_id) 
                    : $this->handleSuccessfulPaymentKoin($transaction, $midtransDetail->metadata->koin);
                    Log::info("Webhook Success: Order ID {$orderId} dengan tipe {$transaction->tipe} berhasil disettlement.");
                } catch (\Exception $e) {
                    Log::error("Webhook Error Processing Settlement: " . $e->getMessage());
                }
            }
        } elseif (in_array($transactionStatus, ['expire', 'cancel', 'deny', 'failure'])) {
            $transaction->update(['status' => $transactionStatus]);
        }

        // 4. Balas Midtrans dengan status 200 OK agar mereka berhenti mengirim ulang (retry)
        return response()->json(['message' => 'OK']);
    }

    private function handleSuccessfulPaymentMembership(Transaction $transaction, int $packageId)
    {
        DB::transaction(function () use ($transaction, $packageId) {
            $transaction->update(['status' => 'settlement']);
            
            // 1. Ambil dompet user (Wallet) terlebih dahulu
            $wallet = UserWallet::firstOrCreate(['user_id' => $transaction->user_id]);
            
            // 2. Ambil Membership Lama menggunakan ID yang ada di dompet
            $oldMembership = Membership::find($wallet->membership_id);
            
            // 3. Ambil Membership Baru
            $newMembership = Membership::find($packageId);

            if ($newMembership) {
                // 4. Buat fungsi anonim (closure) untuk menghitung rumus secara otomatis
                $calculateQuota = function ($field) use ($oldMembership, $newMembership, $wallet) {
                    // Jika tidak ada paket lama (user baru), anggap kuota lama 0
                    $oldQuota = $oldMembership ? $oldMembership->$field : 0;
                    $currentWalletQuota = $wallet->$field;
                    $newQuota = $newMembership->$field;

                    // Rumus: (Kuota Lama - Kuota Saat Ini) = Kuota yang sudah terpakai
                    // Menggunakan max(0, ...) untuk mencegah nilai minus jika admin pernah menambah kuota manual
                    $usedQuota = max(0, $oldQuota - $currentWalletQuota);

                    // Rumus Akhir: Kuota Baru - Kuota Terpakai
                    // Menggunakan max(0, ...) agar jika user "downgrade" ke paket yang lebih kecil, kuotanya jadi 0 (tidak minus)
                    return max(0, $newQuota - $usedQuota);
                };

                // 5. Eksekusi update dompet dengan hasil kalkulasi
                $wallet->update([
                    'membership_id'        => $newMembership->id,
                    'recommendation_quota' => $calculateQuota('recommendation_quota'),
                    'highlight_quota'      => $calculateQuota('highlight_quota'),
                    'banner_quota'         => $calculateQuota('banner_quota'),
                    'push_quota'           => $calculateQuota('push_quota'),
                    'expired_at'           => now()->addDays($newMembership->duration_days)
                ]);
            }
        });
    }

    private function handleSuccessfulPaymentKoin(Transaction $transaction, int $koin)
    {
        DB::transaction(function () use ($transaction, $koin) {
            $transaction->update(['status' => 'settlement']);
            $wallet = UserWallet::firstOrCreate(['user_id' => $transaction->user_id]);
            $wallet->update([
                'dabelyu_koin' => $wallet->dabelyu_koin + $koin,
            ]);
        });
    }
}
