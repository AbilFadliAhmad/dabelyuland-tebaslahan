<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Property;
use App\Models\UserWallet;
use App\Models\PropertyRecommendation;
use App\Models\PropertyHighlight;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


use Illuminate\Http\Request;
// use App\Models\Property; // Nanti sesuaikan dengan model properti kamu
// use App\Models\Highlight; // Nanti sesuaikan dengan model highlight kamu

class HighlightController extends Controller
{
      function index()
    {
        $highlights = PropertyHighlight::with(['user', 'property', 'mainImage'])
                                        ->limit(20)
                                        ->get(); 

        // $recommendationIds = DB::table('property_recommendations')->select('property_id')->orderBy('pushed_at', 'desc')->limit(20)->get();
        $recommendations = PropertyRecommendation::with(['user', 'property', 'mainImage'])
                                                    ->limit(20)
                                                    ->orderBy('pushed_at', 'desc')
                                                    ->get(); 

        $wallets = db::table('user_wallets')->select('dabelyu_koin', 'highlight_quota', 'recommendation_quota', 'push_quota')->where('user_id', Auth::user()->id)->first();
        $dabelyu_koin = $wallets->dabelyu_koin;
        $highlight_quota = $wallets->highlight_quota;
        $recommendation_quota = $wallets->recommendation_quota;
        $push_quota = $wallets->push_quota;

        // Pastikan path view sesuai dengan tempat kamu menyimpan file list.blade.php tadi
        return view('partials.highlight.list', compact('highlights', 'recommendations', 'dabelyu_koin', 'highlight_quota', 'recommendation_quota', 'push_quota')); 
    }

    public function create() {
        $service = DB::table('service_prices')->whereIn('jenis_layanan', ['highlight', 'rekomendasi'])->get(); 
        $wallet = DB::table('user_wallets')->where('user_id', Auth::user()->id)->first();
        return view('partials.highlight.form', compact('service', 'wallet'));
    }

    public function searchProperties(Request $request) 
    {
        $query = $request->get('q');
        $user = Auth::user();
        
        // Jika admin, ambil user_id dari request (jika ada). Jika user biasa, paksa ke id sendiri.
        $userId = $user->role === 'admin' ? $request->get('user_id') : $user->id;

        $properties = Property::where('judul', 'LIKE', "%{$query}%")
            ->select('id', 'judul', 'harga', 'user_id')
            ->with('mainImage')
            ->where('status', 'aktif')
            // Gunakan when agar query user_id hanya jalan jika $userId ada isinya
            ->when($userId, function ($q) use ($userId) {
                return $q->where('user_id', $userId); // Gunakan perbandingan langsung (angka)
            })
            ->limit(10)
            ->get()
            ->map(function($prop) {
                return [
                    'id' => $prop->id,
                    'title' => $prop->judul,
                    'price' => $prop->harga,
                    'price_formatted' => 'Rp ' . number_format($prop->harga, 0, ',', '.'),
                    'img' => asset('storage/' . $prop->mainImage->image_path . '-image_low.webp'),
                ];
            });
        return response()->json($properties);
    }

    public function searchAgents(Request $request)
    {
        $query = $request->get('q');
        $agents = User::where('name', 'LIKE', "%{$query}%")
                    ->select('id', 'name')
                    ->limit(10)
                    ->get()
                    ->map(function($user) {
                        return [
                            'id' => $user->id,
                            'name' => $user->name,
                        ];
                    });

        return response()->json($agents);
    }

    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'service_id'    => 'required',
            'type'        => 'required|in:highlight,rekomendasi',
        ], [
            'property_id.required' => 'Properti wajib dipilih.',
            'property_id.exists'   => 'Properti tidak ditemukan di database.',
        ]);

        // 2. Ambil data properti (Ambil ID Pemilik & Koordinat)
        $property = Property::findOrFail($request->property_id);
        $ownerId = $property->user_id;

        // 3. CEK DUPLIKASI: Mengembalikan error jika sudah aktif
        // Logic: Mengecek ke tabel masing-masing berdasarkan tipe yang dipilih
        $isAlreadyActive = ($request->type === 'highlight') 
            ? PropertyHighlight::where('property_id', $property->id)->exists()
            : PropertyRecommendation::where('property_id', $property->id)->exists();

        if ($isAlreadyActive) {
            // Mengembalikan error ke halaman sebelumnya dengan pesan spesifik
            return back()->with('error', "Gagal! Properti ini sudah terdaftar sebagai " . ucfirst($request->type) . " yang aktif.");
        }

        $servicePrice = (object) DB::table('service_prices')->where('id', $request->service_id)->first();

        if (!$servicePrice || $servicePrice->jenis_layanan !== $request->type) {
            return redirect()->back()->with('error', 'Paket durasi atau jenis layanan tidak valid.');
        }

        $costInKoin = $servicePrice->biaya_koin;
        $expiryDate = Carbon::now('Asia/Jakarta')->addDays($servicePrice->jumlah_hari)->endOfDay()->toDateTimeString();

        try {
            DB::transaction(function () use ($request, $ownerId, $property, $costInKoin, $expiryDate) {
                // Ambil wallet user (Agen yang sedang login) dengan lock
                $wallet = UserWallet::where('user_id', Auth::id())->lockForUpdate()->first();

                if (!$wallet) {
                    throw new \Exception('Dompet akun Anda tidak ditemukan.');
                }

                // 5. LOGIKA PEMBAYARAN: Token vs Koin
                $useToken = false;
                $quotaField = ($request->type === 'highlight') ? 'highlight_quota' : 'recommendation_quota';

                if ($wallet->$quotaField > 0) {
                    $wallet->decrement($quotaField, 1);
                    $useToken = true;
                } elseif ($wallet->dabelyu_koin >= $costInKoin) {
                    $wallet->decrement('dabelyu_koin', $costInKoin);
                } else {
                    throw new \Exception('Saldo Token atau Dabelyu Koin Anda tidak mencukupi.');
                }

                // 6. PERSIAPAN DATA (Common Fields)
                $data = [
                    'property_id'         => $property->id,
                    'user_id'             => $ownerId, // Diambil dari properti
                    'pushed_at'           => Carbon::now(),
                    'expired_at'          => $expiryDate,
                    'amount_token'        => $useToken ? 1 : 0,
                    'amount_dabelyu_koin' => !$useToken ? $costInKoin : 0,
                ];

                // 7. SIMPAN KE TABEL MASING-MASING
                if ($request->type === 'highlight') {
                    // Tambahkan koordinat spatial (Long, Lat) khusus untuk Highlight
                    $data['location'] = DB::raw("POINT({$property->longitude}, {$property->latitude})");
                    PropertyHighlight::create($data);
                } else {
                    PropertyRecommendation::create($data);
                }
            });

            return redirect()->route('account.highlight.index')
                            ->with('success', 'Berhasil! Properti kini berstatus ' . ucfirst($request->type));

        } catch (\Exception $e) {
            // Menangkap throw Exception di dalam transaksi dan mengembalikan sebagai error flash message
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy(string $type, int $property_id)
    {
        // 1. Tentukan Model & Quota Field
        $model = ($type === 'highlight') 
            ? \App\Models\PropertyHighlight::class 
            : \App\Models\PropertyRecommendation::class;
        
        $quotaField = ($type === 'highlight') ? 'highlight_quota' : 'recommendation_quota';

        // 2. Cari data promosi
        $promotion = $model::where('property_id', $property_id)->first();

        if (!$promotion) {
            return back()->with('error', 'Data promosi tidak ditemukan.');
        }

        // 3. KEAMANAN: Cek Otoritas
        $user = Auth::user();
        if ($user->role !== 'admin' && $promotion->user_id !== $user->id) {
            return back()->with('error', 'Anda tidak memiliki akses.');
        }

        try {
            $refundMessage = "";

            DB::transaction(function () use ($promotion, $quotaField, &$refundMessage) {
                $wallet = \App\Models\UserWallet::where('user_id', $promotion->user_id)->lockForUpdate()->first();
                
                if ($wallet) {
                    // 4. LOGIKA PENGEMBALIAN
                    if ($promotion->amount_token > 0) {
                        // Jika pakai Token: Kembalikan penuh (1 unit)
                        $wallet->increment($quotaField, $promotion->amount_token);
                        $refundMessage = "dan 1 Token dikembalikan.";
                    } 
                    elseif ($promotion->amount_dabelyu_koin > 0) {
                        // Jika pakai Koin: Hitung Pro-Rata sisa hari
                        $now = now();
                        $pushedAt = \Carbon\Carbon::parse($promotion->pushed_at);
                        $expiredAt = \Carbon\Carbon::parse($promotion->expired_at);

                        // Hitung total durasi hari asli dan sisa hari
                        $totalDuration = $pushedAt->diffInDays($expiredAt);
                        $remainingDays = $now->diffInDays($expiredAt, false); // false agar bisa negatif jika lewat

                        if ($remainingDays > 0 && $totalDuration > 0) {
                            // Rumus: (Sisa Hari / Total Hari) * Harga Awal
                            $refundAmount = max(0, floor(($remainingDays / $totalDuration) * $promotion->amount_dabelyu_koin));                            

                            if ($refundAmount > 0) {
                                $wallet->increment('dabelyu_koin', $refundAmount);
                                $refundMessage = "dan {$refundAmount} Koin dikembalikan.";
                            }
                        }
                    }
                }

                // 5. Hapus data promosi setelah refund diproses
                $promotion->delete();
            });

            return back()->with('success', "Promosi " . ucfirst($type) . " berhasil dihentikan " . $refundMessage);

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function sundul(Request $request)
    {
        $userId = Auth::id();
        $propertyId = $request->property_id;
        $type = $request->type; // 'highlight' atau 'rekomendasi'
        $method = $request->method; // 'token' atau 'coin'
        $biayaKoin = 50;

        // 1. Tentukan Model
        $model = ($type === 'highlight') 
            ? \App\Models\PropertyHighlight::class 
            : \App\Models\PropertyRecommendation::class;

        try {
            DB::transaction(function () use ($userId, $propertyId, $model, $method, $biayaKoin) {
                $wallet = \App\Models\UserWallet::where('user_id', $userId)->lockForUpdate()->first();
                $promotion = $model::where('property_id', $propertyId)->where('user_id', $userId)->first();

                if (!$promotion) throw new \Exception("Data promosi tidak ditemukan.");

                // 2. Potong Saldo
                if ($method === 'token') {
                    if ($wallet->push_quota < 1) throw new \Exception("Kuota Token Push Anda habis.");
                    $wallet->decrement('push_quota');
                } else {
                    if ($wallet->dabelyu_koin < $biayaKoin) throw new \Exception("Saldo Koin tidak mencukupi.");
                    $wallet->decrement('dabelyu_koin', $biayaKoin);
                }

                // 3. Update pushed_at (Inti dari Sundul)
                $promotion->update(['pushed_at' => now()]);
            });

            return response()->json(['success' => true, 'message' => 'Properti berhasil disundul ke urutan teratas!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}