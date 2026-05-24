<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Membership;
use App\Models\UserWallet;
use App\Models\Transaction;
use Midtrans\Config;
use Midtrans\CoreApi;
use Midtrans\Transaction as MidtransTransaction;

class UserMembershipController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $Memberships = Membership::orderBy('price', 'asc')->get();
        $currentMembership = UserWallet::with('membership')->where('user_id', $user->id)->first();
        // dd($currentMembership->expired_at);
        // 1. CARI TRANSAKSI PENDING (Gunakan Composite Index: user_id -> tipe -> status)
        $pendingTransaction = Transaction::where('user_id', $user->id)
                                           ->where('tipe', 'membership')
                                           ->where('status', 'pending')
                                           ->first();

        $midtransDetail = null;
        if ($pendingTransaction) {
            Config::$serverKey = config('midtrans.server_key');
            Config::$isProduction = config('midtrans.is_production');

            try {
                $liveStatus = (object) MidtransTransaction::status($pendingTransaction->order_id);
                $midtransStatus = $liveStatus->transaction_status;

                // Sinkronisasi Otomatis
                if (in_array($midtransStatus, ['settlement', 'capture'])) {
                    $pendingTransaction->update(['status' => 'settlement']);
                    $pendingTransaction = null; 
                } elseif (in_array($midtransStatus, ['expire', 'cancel', 'deny'])) {
                    $pendingTransaction->update(['status' => $midtransStatus]);
                    $pendingTransaction = null; 
                } else {
                    // JIKA MASIH PENDING: Bangun ulang objek untuk kebutuhan Blade
                    $savedActions = json_decode($pendingTransaction->payment_info);

                    $midtransDetail = (object) [
                        'order_id' => $pendingTransaction->order_id,
                        'gross_amount' => $liveStatus->gross_amount,
                        'koin' => $liveStatus->metadata->koin ?? 0,
                        'payment_type' => $liveStatus->payment_type,
                        'transaction_status' => $liveStatus->transaction_status,
                        'expiry_time' => $liveStatus->expiry_time,
                        'metadata' => $liveStatus->metadata,
                        'actions' => $savedActions // Data aman dari DB lokal
                    ];
                }
            } catch (\Exception $e) {
                Log::error("Gagal sinkronisasi Midtrans Membership: " . $e->getMessage());
            }
        }

        return view('user.membership.index', compact('user', 'Memberships', 'currentMembership', 'midtransDetail'));
    }

    public function initiatePayment(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:memberships,id', // Validasi ke tabel memberships
            'payment_method' => 'required|in:gopay,shopeepay,qris'
        ]);
            
        $userId = Auth::id();
        $package = Membership::findOrFail($request->package_id);

        // 1. CEK TRANSAKSI PENDING (Composite Index)
        $hasPending = Transaction::where('user_id', $userId)
            ->where('tipe', 'membership')
            ->where('status', 'pending')
            ->exists();

        if ($hasPending) {
            return response()->json([
                'status' => 'warning',
                'message' => 'Masih ada tagihan membership yang sedang menunggu pembayaran. Selesaikan atau batalkan terlebih dahulu.'
            ], 422); 
        }
        
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');

        $orderId = 'MBR-' . strtoupper(Str::random(5)) . '-' . time();
        $paymentMethod = $request->payment_method;

        // INSERT DATA AWAL KE LOKAL
        $localTransaction = Transaction::create([
            'user_id' => $userId,
            'order_id' => $orderId,
            'tipe' => 'membership',
            'status' => 'pending',
            'price' => $package->price,
        ]);

        $params = [
            'payment_type' => $paymentMethod,
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $package->price, // Asumsi kolom harga di tabel memberships adalah 'price'
            ],
            'expiry' => [
                'start_time' => date("Y-m-d H:i:s O"),
                'unit' => 'minute',
                'duration' => 15
            ],
            'metadata' => [
                'user_id' => $userId,
                'membership_price' => $package->price, // Simpan nama paket
                'membership_discount' => $package->discount, // Simpan nama paket
                'membership_name' => $package->name, // Simpan nama paket
                'membership_id' => $package->id, // Simpan nama paket
            ]
        ];

        if ($paymentMethod !== 'qris') {
            $params[$paymentMethod] = [
                'enable_callback' => true,
                'callback_url' => route('user.membership.index')
            ];
        } 

        try {
            $response = CoreApi::charge($params);
            
            // Simpan actions untuk jaga-jaga (khususnya GoPay)
            $localTransaction->update([
                'payment_info' => json_encode($response->actions)
            ]);
            
            return response()->json([
                'status' => 'success',
                'data' => $response,
                'package_name' => "Langganan " . $package->name
            ]);

        } catch (\Exception $e) {
            if ($localTransaction) $localTransaction->delete();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function checkStatus($orderId)
    {
        $transaction = Transaction::where('order_id', $orderId)->first();
        if (!$transaction) return response()->json(['status' => 'not_found'], 404);

        return response()->json(['status' => $transaction->status]);
    }

    public function cancelPayment(Request $request)
    {
        $transaction = Transaction::where('user_id', Auth::id())
            ->where('order_id', $request->order_id)
            ->where('status', 'pending')
            ->first();

        if ($transaction) {
            Config::$serverKey = config('midtrans.server_key');
            Config::$isProduction = config('midtrans.is_production');

            try {
                // Beri tahu Midtrans untuk membatalkan tagihan
                MidtransTransaction::cancel($transaction->order_id);
            } catch (\Exception $e) {
                Log::warning("Gagal membatalkan di Midtrans (mungkin sudah kedaluwarsa): " . $e->getMessage());
            }

            $transaction->update(['status' => 'cancel']);
            return response()->json(['status' => 'success', 'message' => 'Transaksi berhasil dibatalkan.']);
        }
        return response()->json(['status' => 'error', 'message' => 'Transaksi tidak ditemukan.'], 404);
    }
}