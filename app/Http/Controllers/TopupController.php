<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\CoinPackage;
use App\Models\UserWallet;
use App\Models\Transaction;
use Midtrans\Config;
use Midtrans\CoreApi;
use Midtrans\Transaction as MidtransTransaction;

class TopupController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        // Ambil paket yang hanya berstatus AKTIF dari database
        $packages = CoinPackage::where('is_active', true)
                                ->orderBy('koin', 'asc')
                                ->get();

        // Ambil wallet user yang sedang login
        $wallet = UserWallet::where('user_id', $user->id)->first();
        $dabelyuKoin = $wallet->dabelyu_koin;
        // dd($wallet);

        // Ambil transaksi pending user
        $pendingTransaction = Transaction::where('user_id', $user->id)
                                           ->where('status', 'pending')
                                           ->first();
        $midtransDetail = null;
        // 2. JIKA ADA TRANSAKSI PENDING, AMBIL DETAIL LIVE DARI MIDTRANS
        if ($pendingTransaction) {
            Config::$serverKey = config('midtrans.server_key');
            Config::$isProduction = config('midtrans.is_production');

            try {
                // 1. Ambil status live (untuk cek apakah sudah settlement/expire)
                $liveStatus = (object) MidtransTransaction::status($pendingTransaction->order_id);
                $midtransStatus = $liveStatus->transaction_status;

                // 2. Sinkronisasi Otomatis jika status berubah
                if (in_array($midtransStatus, ['settlement', 'capture'])) {
                    $pendingTransaction->update(['status' => 'settlement']);
                    $wallet->increment('dabelyu_koin', $liveStatus->metadata->koin ?? 0);
                    $pendingTransaction = null; 
                } elseif (in_array($midtransStatus, ['expire', 'cancel', 'deny'])) {
                    $pendingTransaction->update(['status' => $midtransStatus]);
                    $pendingTransaction = null; 
                } else {
                    // 3. JIKA STATUS MASIH PENDING: Bangun ulang objek untuk kebutuhan Blade
                    // decode actions dari DB lokal kita yang datanya permanen
                    $savedActions = json_decode($pendingTransaction->payment_info);

                    $midtransDetail = (object) [
                        'koin' => $liveStatus->metadata->koin ?? 0,
                        'order_id' => $pendingTransaction->order_id,
                        'gross_amount' => $liveStatus->gross_amount,
                        'payment_type' => $liveStatus->payment_type,
                        'transaction_status' => $liveStatus->transaction_status,
                        'expiry_time' => $liveStatus->expiry_time,
                        'actions' => $savedActions // Diambil dari DB lokal, GoPay dijamin aman!
                    ];
                }

            } catch (\Exception $e) {
                // Jika gagal terhubung ke Midtrans (misal koneksi internet putus), 
                // tangkap errornya agar web kamu tidak crash / blank putih
                Log::error("Gagal mengambil status transaksi Midtrans: " . $e->getMessage());
            }
        }
        // Kirim data $packages ke view
        return view('user.topup.index', compact('packages', 'dabelyuKoin', 'midtransDetail'));
    }
   
    public function initiatePayment(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:coin_packages,id',
            'payment_method' => 'required|in:gopay,shopeepay,qris' // Sesuaikan dengan yang didukung Core API
        ]);
            
        $userId = Auth::id();
        $package = CoinPackage::findOrFail($request->package_id);

        // 1. CEK TRANSAKSI PENDING MENGGUNAKAN COMPOSITE INDEX (Urutan: user_id -> tipe -> status)
        $hasPending = Transaction::where('user_id', $userId)
        ->where('tipe', 'koin')
        ->where('status', 'pending')
        ->exists();

        if ($hasPending) {
            return response()->json([
                'status' => 'warning',
                'message' => 'Masih ada transaksi yang sedang menunggu pembayaran. Bayar atau batalkan transaksi lama Anda terlebih dahulu untuk membuka transaksi baru.'
            ], 422); // HTTP 422 Unprocessable Entity
        }
        
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');

        $orderId = 'TBL-' . strtoupper(Str::random(5)) . '-' . time();
        $paymentMethod = $request->payment_method;

        $localTransaction = Transaction::create([
            'user_id' => $userId,
            'order_id' => $orderId,
            'tipe' => 'koin',
            'status' => 'pending',
            'price' => $package->harga
        ]);

        $params = [
            'payment_type' => $paymentMethod,
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $package->harga,
            ],
            'expiry' => [
            'start_time' => date("Y-m-d H:i:s O"),
            'unit' => 'minute',
            'duration' => 15
            ],
            // Tambahkan metadata untuk mempermudah tracking
            'metadata' => [
                'user_id' => Auth::id(),
                'koin' => $package->koin
            ]
        ];

        if ($paymentMethod !== 'qris') {
            $params[$paymentMethod] = [
                'enable_callback' => true,
                'callback_url' => route('user.topup.index')
            ];
        } 

        try {
            $response = CoreApi::charge($params);
            // Update payment_info di transaksi lokal
            $localTransaction->update([
                'payment_info' => json_encode($response->actions)
            ]);
            return response()->json([
                'status' => 'success',
                'data' => $response,
                'package_name' => "Top Up " . $package->koin . " Koin"
            ]);
        } catch (\Exception $e) {
            // Jika gagal, hapus transaksi lokal
            if ($localTransaction) $localTransaction->delete();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function checkStatus(string $orderId)
    {
        $transaction = Transaction::where('order_id', $orderId)->first();
        if (!$transaction) return response()->json(['status' => 'not_found'], 404);

        return response()->json(['status' => $transaction->status]);
    }
}