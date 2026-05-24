<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
use App\Models\ServicePrice;
use App\Models\UserWallet;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\FcmController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BannerController extends Controller
{
    public function index()
    {
        $images = Banner::all();
        return view('partials.banner.list', compact('images'));
    }

    public function create()
    {
        $isEdit = false;
        $servicePrices = ServicePrice::where('jenis_layanan', 'banner')->get();
        $tokenBanner = UserWallet::where('user_id', Auth::user()->id)->value('banner_quota') ?? 0;
        return view('partials.banner.form', compact('isEdit', 'servicePrices', 'tokenBanner'));
    }

    public function store(Request $request)  
    {
        // 1. Validasi Input
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:8048', // Maksimal 8MB
            'service_price_id' => 'required|exists:service_prices,id',
        ]);

        $user = Auth::user();
        
        // 2. Ambil Data Harga Layanan & Dompet User
        $servicePrice = DB::table('service_prices')->where('id', $request->service_price_id)->first();
        $wallet = DB::table('user_wallets')->where('user_id', $user->id)->first();

        if (!$servicePrice) {
            return back()->with('error', 'Tarif layanan tidak ditemukan.');
        }

        $amountToken = 0;
        $amountKoin = 0;

        // 3. LOGIKA PEMBAYARAN: Cek Kuota Token Banner Terlebih Dahulu
        if ($wallet && $wallet->banner_quota > 0) {
            // Jika punya token, potong token
            $amountToken = 1;
            DB::table('user_wallets')->where('user_id', $user->id)->decrement('banner_quota', 1);
        } else {
            // Jika token habis/tidak punya, cek saldo Koin
            if (!$wallet || $wallet->dabelyu_koin < $servicePrice->biaya_koin) {
                return back()->with('error', 'Koin Anda tidak mencukupi. Silakan gunakan Token Banner atau Top Up Koin terlebih dahulu.');
            }
            
            // Jika koin cukup, potong koin
            $amountKoin = $servicePrice->biaya_koin;
            DB::table('user_wallets')->where('user_id', $user->id)->decrement('dabelyu_koin', $amountKoin);
        }

        // 4. Proses Upload Gambar
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('banners', 'public');
        }

        // 5. Penentuan Status & Expired Date
        $status = $user->role === 'admin' ? 'aktif' : 'menunggu';
        $expiredAt = Carbon::now('Asia/Jakarta')->addDays($servicePrice->jumlah_hari)->endOfDay()->toDateTimeString();

        // 6. Simpan Data ke Database
        Banner::create([
            'image'               => $imagePath,
            'status'              => $status,
            'expired_at'          => $expiredAt,
            'amount_token'        => $amountToken,
            'amount_dabelyu_koin' => $amountKoin,
            'user_id'             => $user->id,
        ]);

        // 7. Kirim Notifikasi ke Admin (Jika pengunggah bukan admin)
        if ($user->role !== 'admin') {
            // Ganti 'FcmController' dengan nama Class tempat fungsi sendNotification kamu berada
            FcmController::sendNotification(
                'admin', // Topic yang dituju
                'Pengajuan Banner Baru', 
                "Ada pengajuan banner properti baru dari {$user->name} yang memerlukan persetujuan.", 
                route('account.banner.index') // Asumsi nama route sesuai instruksi
            );
        }

        // 8. Redirect Sukses
        $pesanSukses = $status === 'aktif' 
            ? 'Banner berhasil ditambahkan dan langsung aktif.' 
            : 'Banner berhasil ditambahkan dan sedang menunggu persetujuan admin.';

        return redirect()->route('account.banner.index')->with('success', $pesanSukses);
    }

    public function edit(Banner $banner)
    {
        $isEdit = true;
        return view('partials.banner.form', compact('banner'), compact('isEdit'));
    }

    public function update(Request $request, Banner $banner)
    {
        $validatedData = $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Maksimal 2MB
            'status' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($banner->image) {
                Storage::disk('public')->delete($banner->image);
            }
            $validatedData['image'] = $request->file('image')->store('banners', 'public');
        }

        $banner->update($validatedData);

        return redirect()->route('banner.index')->with('success', 'Banner berhasil diperbarui.');
    }

    public function toggleStatus(int $id)
    {
        // Sesuaikan nama Model dengan model bannermu
        $banner = \App\Models\Banner::findOrFail($id); 

        if($banner->status == 'menunggu') {
            return response()->json([
                'success' => false,
                'message' => 'Banner belum diterbitkan'
            ]);
        } 
        
        $banner->status = $banner->status == 'aktif' ? 'nonaktif' : 'aktif';
        $banner->save();

        return response()->json([
            'success' => true,
            'message' => 'Status berhasil diubah'
        ]);
    }

    public function verifyBanner(Request $request) 
    {
        $validated = $request->validate([
            'banner_id' => 'required|exists:banners,id',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $banner = Banner::findOrFail($validated['banner_id']);

        if ($validated['status'] === 'aktif') {
            // Jika diterima, ubah status jadi aktif
            $banner->update(['status' => 'aktif']);

            // Kirim Notifikasi Diterima
            $topic = 'user_' . $banner->user_id;
            $title = 'Banner Disetujui! 🎉';
            $body = 'Pengajuan banner properti Anda telah disetujui oleh Admin dan sekarang sedang tayang.';
            
            FcmController::sendNotification($topic, $title, $body, route('account.banner.index'));

            return redirect()->back()->with('success', 'Banner berhasil diverifikasi dan diaktifkan.');

        } else {
            // Jika ditolak (nonaktif), hapus banner dan refund
            $this->processDeletionAndRefund($banner, 'Ditolak oleh Admin pada saat verifikasi.');
            
            return redirect()->back()->with('success', 'Banner berhasil ditolak dan dihapus. Saldo/Token telah dikembalikan ke agen.');
        }
    }

    /**
     * FUNGSI 2: Menghapus Banner (Bisa oleh Admin/User)
     */
    public function destroy(int $id)
    {
        $banner = Banner::findOrFail($id);
        
        // Hapus dan refund
        $this->processDeletionAndRefund($banner, 'Dihapus secara manual.');

        return redirect()->back()->with('success', 'Banner berhasil dihapus dan pengembalian (jika ada) telah diproses.');
    }

    /**
     * FUNGSI HELPER: Logika Utama Penghapusan, Refund, dan Notifikasi
     */
    private function processDeletionAndRefund(Banner $banner, string $reason)
    {
        $now = Carbon::now();
        $expiredAt = Carbon::parse($banner->expired_at);
        $userId = $banner->user_id;

        $refundToken = 0;
        $refundKoin = 0;
        $pesanRefund = '';

        // 1. CEK MASA AKTIF UNTUK REFUND (Hanya direfund jika belum expired)
        if ($now->lessThan($expiredAt)) {
            
            if ($banner->amount_token > 0) {
                // Skenario A: Menggunakan Token Banner (Kembalikan 1 Token utuh)
                $refundToken = $banner->amount_token;
                DB::table('user_wallets')->where('user_id', $userId)->increment('banner_quota', $refundToken);
                $pesanRefund = "1 Token Banner Anda telah dikembalikan.";
                
            } elseif ($banner->amount_dabelyu_koin > 0) {
                // Skenario B: Menggunakan Koin (Hitung refund proporsional)
                $createdAt = Carbon::parse($banner->created_at);
                $totalDays = $createdAt->diffInDays($expiredAt);
                
                // Menghitung sisa hari dari HARI INI sampai EXPIRED
                $remainingDays = $now->diffInDays($expiredAt); 

                if ($totalDays > 0 && $remainingDays > 0) {
                    // Rumus: (Total Koin / Total Hari) * Sisa Hari
                    $refundKoin = floor(($banner->amount_dabelyu_koin / $totalDays) * $remainingDays);
                    
                    if ($refundKoin > 0) {
                        DB::table('user_wallets')->where('user_id', $userId)->increment('dabelyu_koin', $refundKoin);
                        $pesanRefund = "Koin sejumlah " . number_format($refundKoin, 0, ',', '.') . " telah dikembalikan (Sisa masa tayang: {$remainingDays} hari).";
                    }
                }
            }
        } else {
            $pesanRefund = "Tidak ada pengembalian karena masa aktif banner telah habis.";
        }

        // 2. HAPUS FILE FISIK DARI STORAGE (Jika ada)
        if ($banner->image && Storage::disk('public')->exists($banner->image)) {
            Storage::disk('public')->delete($banner->image);
        }

        // 3. HAPUS RECORD DARI DATABASE
        $banner->delete();

        // 4. KIRIM NOTIFIKASI KE USER
        $topic = 'user_' . $userId;
        $title = 'Pemberitahuan Banner Properti ⚠️';
        $body = "Banner Anda telah dihapus dari sistem. Alasan: {$reason} {$pesanRefund}";
        
        FcmController::sendNotification($topic, $title, $body, route('account.banner.index'));
    }

    public function generateAi(Request $request)
    {
        try {
            $prompt = $request->prompt;

            if (empty($prompt)) {
                return response()->json(['status' => 'error', 'error' => 'Prompt tidak boleh kosong.'], 400);
            }

            // 1. Bersihkan dan format prompt agar aman di URL
            $encodedPrompt = urlencode($prompt);
            
            // 2. Buat angka acak agar gambar yang dihasilkan selalu berbeda
            $seed = rand(1, 1000000);
            
            // 3. Gunakan API Pollinations (Format Banner: Lebar 1024 x Tinggi 512)
            $url = "https://image.pollinations.ai/prompt/{$encodedPrompt}?seed={$seed}&width=1200&height=400&nologo=true";

            // 4. Tembak URL-nya menggunakan GET
            $response = Http::withoutVerifying()
                ->timeout(60) // Beri waktu 60 detik karena AI butuh waktu melukis
                ->get($url);

            if ($response->successful()) {
                // Ubah gambar jadi base64
                $base64Image = base64_encode($response->body());
                
                return response()->json([
                    'status' => 'success',
                    'url'    => "data:image/jpeg;base64,$base64Image"
                ]);
            }

            return response()->json([
                'status' => 'error',
                'error'  => 'Gagal mengambil gambar dari AI Server (Status: ' . $response->status() . ')'
            ], $response->status());

        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'error'  => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }

    public function generateAiKombinasi(Request $request)
    {
        try {
            $images = $request->file('ai_refs');
            
            if (!$images || count($images) < 1) {
                return response()->json(['status' => 'error', 'error' => 'Minimal wajib upload 1 gambar referensi.']);
            }

            $mainImage = $images[0];

            // Pastikan file gambar aman dan ada
            if (!$mainImage->isValid()) {
                return response()->json(['status' => 'error', 'error' => 'File gambar tidak valid atau rusak.']);
            }

            // KUNCI 1: Pastikan CLOUDINARY_URL di .env sudah terisi dengan benar
            // Formatnya wajib: cloudinary://API_KEY:API_SECRET@CLOUD_NAME
            if (!env('CLOUDINARY_URL')) {
                return response()->json(['status' => 'error', 'error' => 'Konfigurasi CLOUDINARY_URL di .env belum diatur.']);
            }

            // KUNCI 2: Inisialisasi SDK Cloudinary Asli
            $mesinCloudinary = new \Cloudinary\Cloudinary(env('CLOUDINARY_URL'));

            // KUNCI 3: Upload menggunakan UploadApi bawaan SDK asli (Jauh lebih stabil)
            $uploadResult = $mesinCloudinary->uploadApi()->upload($mainImage->getRealPath(), [
                'folder' => 'tebaslahan_ai'
            ]);

            // Mengambil public_id langsung dari array response resmi Cloudinary
            $publicId = $uploadResult['public_id'];

            // 4. Proses Manipulasi AI (Generative Fill)
            $aiImageUrl = $mesinCloudinary->image($publicId)
                ->resize(
                    \Cloudinary\Transformation\Resize::pad()
                        ->width(1200)
                        ->height(400)
                        ->background(\Cloudinary\Transformation\Background::generativeFill())
                )
                ->delivery(\Cloudinary\Transformation\Delivery::format(\Cloudinary\Transformation\Format::auto()))
                ->delivery(\Cloudinary\Transformation\Delivery::quality(\Cloudinary\Transformation\Quality::auto()))
                ->toUrl();

            // Tambah teks jika diaktifkan ke depannya
            // $text = $request->input('ai_text_title');
            // if ($text) {
            //     $aiImageUrl = $mesinCloudinary->image($publicId)
            //         ->resize(
            //             \Cloudinary\Transformation\Resize::pad()->width(1200)->height(400)->background(\Cloudinary\Transformation\Background::generativeFill())
            //         )
            //         ->overlay(
            //             \Cloudinary\Transformation\Overlay::text(
            //                 new \Cloudinary\Transformation\Text\TextLayer()
            //                     ->fontFamily("Arial")->fontSize(50)->fontWeight("bold")->text($text)
            //             )->gravity(\Cloudinary\Transformation\Gravity::south())->y(50)
            //         )
            //         ->toUrl();
            // }

            return response()->json([
                'status' => 'success',
                'url'    => $aiImageUrl 
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error', 
                'error' => 'Error Cloudinary: ' . $e->getMessage() . ' (Baris: ' . $e->getLine() . ')'
            ], 500);
        }
    }
}