<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\CoinPackage;
use App\Models\User;
use App\Models\UserWallet;
use App\Models\Transaction;
use Midtrans\Config;
use Midtrans\CoreApi;

class TopupController extends Controller
{
    public function index()
    {
        // Ambil paket yang hanya berstatus AKTIF dari database
        $packages = CoinPackage::where('is_active', true)
                                ->orderBy('koin', 'asc')
                                ->get();

        // Ambil wallet user yang sedang login
        $wallet = UserWallet::where('user_id', Auth::user()->id)->first();
        $dabelyuKoin = $wallet->dabelyu_koin;

        // Kirim data $packages ke view
        return view('user.topup.index', compact('packages', 'dabelyuKoin'));
    }
   
    public function initiatePayment(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:coin_packages,id',
            'payment_method' => 'required|in:gopay,shopeepay,qris' // Sesuaikan dengan yang didukung Core API
        ]);
            
        $package = CoinPackage::findOrFail($request->package_id);
        
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');

        $orderId = 'TBL-' . strtoupper(Str::random(5)) . '-' . time();

        $paymentMethod = $request->payment_method;
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
            return response()->json([
                'status' => 'success',
                'data' => $response,
                'package_name' => "Top Up " . $package->koin . " Koin"
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function checkStatus(string $orderId)
    {
        $transaction = Transaction::where('order_id', $orderId)->first();
        if (!$transaction) return response()->json(['status' => 'not_found'], 404);

        return response()->json(['status' => $transaction->status]);
    }

    // 3. Fungsi Webhook (Callback) dari Midtrans
    public function handleWebhook(Request $request)
    {
        $serverKey = config('midtrans.server_key');
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        if ($hashed !== $request->signature_key) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $transactionStatus = $request->transaction_status;
        $orderId = $request->order_id;
        
        $transaction = Transaction::where('order_id', $orderId)->first();
        if (!$transaction) return response()->json(['message' => 'Transaction not found'], 404);

        // Update Status berdasarkan notifikasi Midtrans
        if (in_array($transactionStatus, ['capture', 'settlement'])) {
            if ($transaction->status !== 'settlement') {
                DB::transaction(function () use ($transaction, $request) {
                    $transaction->update(['status' => 'settlement']);
                    
                    // Tambah koin ke user (Asumsi koin disimpan di metadata Midtrans)
                    $user = User::find($transaction->user_id);
                    $koinToAdded = $request->metadata['koin'] ?? 0;
                    $user->increment('dabelyu_koin', $koinToAdded);
                });
            }
        } elseif (in_array($transactionStatus, ['expire', 'cancel', 'deny', 'failure'])) {
            $transaction->update(['status' => $transactionStatus]);
        }

        return response()->json(['message' => 'OK']);
    }
}