<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Services\EmailService;
use App\Services\WhatsappService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\UserWallet;
use App\Http\Controllers\FCMController;


class SesiController extends Controller
{
    // Halaman login / register
    public function index()
    {
        return view('partials.auth.login');
    }

    // Proses Simpan data saat register akun
    public function register(Request $request)
    {
        // 1. Validasi Input Dasar
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:users,name',
            'email' => 'required_without:whatsapp|nullable|email|unique:users,email',
            'whatsapp' => 'required_without:email|nullable|unique:users,nowa',
            'password' => 'required|min:8|confirmed',
            'otp' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        // 2. Verifikasi OTP dari Cache
        $contact = $request->email ?? $request->whatsapp;
        $storedOtp = Cache::get('otp_' . $contact);

        if (!$storedOtp || $storedOtp != $request->otp) {
            return response()->json(['message' => 'Kode OTP salah atau sudah kadaluwarsa'], 400);
        }

        // 3. Simpan User dan Dompet menggunakan Database Transaction
        DB::beginTransaction();

        try {
            // Buat User Baru
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'nowa' => $request->whatsapp,
                'role' => 'user',
                'is_verified' => false,
            ]);

            // Buat Dompet Baru yang terhubung dengan ID User di atas
            UserWallet::create([
                'user_id' => $user->id,
                'membership_id' => 1, // Default diset ke 1
                // Kolom kuota (dabelyu_koin, recommendation_quota, dll) akan otomatis 
                // bernilai 0 sesuai dengan nilai "Default" di database kamu.
            ]);

            // Audit Log: Pendaftaran Akun Sesi Baru
            DB::table('audit_logs')->insert([
                'user_id'     => $user->id,
                'type'        => 'sesi',
                'action'      => 'create',
                'description' => "Pengguna baru bernama {$user->name} ({$contact}) telah berhasil mendaftarkan akun baru melalui verifikasi OTP.",
                'created_at'  => now(),
                'updated_at'  => now()
            ]);

            // Jika sampai di baris ini tanpa error, permanenkan semua perubahan ke database
            DB::commit();

            // Bersihkan Cache OTP setelah registrasi berhasil
            Cache::forget('otp_' . $contact);

            // Siapkan data notifikasi FCM Semantik untuk Admin
            $fcmTopic = 'admin';
            $fcmTitle = 'Notifikasi Keamanan: Registrasi Agen Baru';
            $fcmBody  = "Agen baru dengan nama '{$user->name}' telah mendaftar dan menunggu verifikasi agar dapat aktif di platform.";
            $fcmUrl   = route('admin.list.users');

            // =========================================================================
            // ASYNC TRICK UNTUK API JSON RESPONSE
            // =========================================================================
            // Ambil payload response mentah yang akan dikirim ke browser client
            $response = response()->json(['message' => 'Registrasi berhasil']);

            // Kirim raw header & content ke browser instan tanpa mematikan script
            echo $response->getContent();

            header("Connection: close");
            header("Content-Length: " . strlen($response->getContent()));
            header("Content-Type: application/json");

            if (function_exists('fastcgi_finish_request')) {
                fastcgi_finish_request();
            }

            // =========================================================================
            // ASYNC BACKGROUND PROCESS (USER SUDAH MENERIMA RESPONS 200 OK)
            // =========================================================================
            try {
                set_time_limit(60);
                FCMController::sendNotification($fcmTopic, $fcmTitle, $fcmBody, $fcmUrl);
            } catch (\Exception $e) {
                Log::error('FCM Register Background Error: ' . $e->getMessage());
            }

            exit;
        } catch (\Exception $e) {
            // Jika terjadi error (misal: koneksi putus, tabel hilang), batalkan SEMUA perubahan
            DB::rollBack();

            // (Opsional) Log error untuk kebutuhan proses debug
            \Illuminate\Support\Facades\Log::error('Register Error: ' . $e->getMessage());

            return response()->json(['message' => 'Terjadi kesalahan sistem saat mendaftar, silakan coba lagi.'], 500);
        }
    }

    
    // Proses login
    public function login(Request $request)
    {
        // 1. Validasi Input Dasar
        $request->validate([
            'name' => 'required|string',
            'password' => 'required|string',
        ], [
            'name.required' => 'Nama atau Email wajib diisi',
            'password.required' => 'Password wajib diisi',
        ]);

        $credentials = $request->only('name', 'password');

        // 2. Verifikasi Kredensial Keamanan Akun
        if (Auth::attempt($credentials, true)) {
            $user = Auth::user();

            // Cek apakah akun sudah diverifikasi oleh admin
            if ($user->is_verified === false) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors(['login' => 'Anda harus diverifikasi oleh admin terlebih dahulu.'])->withInput();
            }

            // Buat ulang session_id agar lebih aman dari Session Fixation
            $request->session()->regenerate();

            // 3. Proses Pembungkusan Operasi Database & Audit Log
            DB::transaction(function () use ($user) {
                // Audit Log: Sesi Masuk Aplikasi (Enum 'sesi', 'create')
                DB::table('audit_logs')->insert([
                    'user_id'     => $user->id,
                    'type'        => 'sesi',
                    'action'      => 'create',
                    'description' => "Pengguna {$user->name} dengan hak akses [{$user->role}] berhasil masuk ke dalam sistem.",
                    'created_at'  => now(),
                    'updated_at'  => now()
                ]);
            });

            // 4. Sinkronisasi Token FCM (Dilakukan secara prosedural sebelum redirect)
            $fcmToken = $request->input('fcm_token');
            if ($fcmToken && !empty($fcmToken)) {
                try {
                    $topicToSubscribe = ($user->role === 'admin') ? 'admin' : 'user_' . $user->id;
                    FCMController::subscribeTopic($fcmToken, $topicToSubscribe);
                } catch (\Exception $e) {
                    // Catat eror jika Firebase macet, namun jangan gagalkan proses masuk user
                    Log::error('FCM Login Sync Error: ' . $e->getMessage());
                }
            }

            // Set flash message untuk memicu SweetAlert2 di halaman dashboard tujuan
            session()->flash('success', "Selamat Datang Kembali, {$user->name}!");

            // 5. Pengalihan Halaman (Redirect) Berdasarkan Role Menggunakan Fitur Bawaan Laravel
            if ($user->role === 'admin') {
                return redirect()->intended('/admin'); // Membuka dashboard admin
            } elseif ($user->role === 'user') {
                return redirect()->route('user.index'); // Membuka index properti agen
            } else {
                // Antisipasi jika ada akun dengan role tidak dikenal
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors(['name' => 'Role akun tidak valid.'])->withInput();
            }
        }

        // Jika Kredensial Salah
        return back()->withErrors([
            'name' => 'Akun atau Password yang Anda masukkan salah.',
        ])->withInput($request->only('name'));
    }

    // Logout akun
    public function logout(Request $request)
    {
        $user = Auth::user();
        $fcmToken = $request->input('fcm_token');

        if ($user) {
            // Audit Log: Penghapusan/Keluar Sesi Aplikasi
            DB::table('audit_logs')->insert([
                'user_id'     => $user->id,
                'type'        => 'sesi',
                'action'      => 'delete', // Menggunakan enum 'delete' karena sesi dihancurkan
                'description' => "Pengguna {$user->name} telah keluar dari sistem dan mengakhiri sesi aktif.",
                'created_at'  => now(),
                'updated_at'  => now()
            ]);
        }

        // Proses logout dan pembersihan session Laravel secara fisik
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Set flash message sukses keluar
        session()->flash('success', 'Anda telah berhasil keluar dari akun.');
        $request->session()->save(); // Amankan data flash sebelum exit

        // =========================================================================
        // ASYNC TRICK UNTUK LOGOUT REDIRECT
        // =========================================================================
        header("Location: " . url('login'));
        header("Connection: close");
        header("Content-Length: 0");

        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }

        // =========================================================================
        // ASYNC BACKGROUND PROCESS (PROSES UNSUBSCRIBE FCM SETELAH USER KELUAR)
        // =========================================================================
        try {
            set_time_limit(60);

            if ($user && $fcmToken && !empty($fcmToken)) {
                $topicToUnsubscribe = ($user->role === 'admin') ? 'admin' : 'user_' . $user->id;
                FCMController::unsubscribeTopic($fcmToken, $topicToUnsubscribe);
            }
        } catch (\Exception $e) {
            Log::error('FCM Logout Background Error: ' . $e->getMessage());
        }

        exit;
    }

    public function sendOtp(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'contact' => 'required',
            'type' => 'required|in:wa,email',
        ]);


        $contact = $request->contact;

        // Cek Rate Limit (Anti-Spam OTP)
        if (Cache::has('otp_rate_limit_' . $contact)) {
            return response()->json(['message' => 'Tunggu beberapa saat sebelum meminta OTP baru.'], 429);
        }

        // Generate 6 digit angka acak
        $otp = rand(100000, 999999);

        // Simpan OTP di cache selama 5 menit (300 detik)
        Cache::put('otp_' . $contact, $otp, 300);
        // Kunci request selama 60 detik agar user tidak spam klik
        Cache::put('otp_rate_limit_' . $contact, true, 60);

        // 1. Tentukan URL API Laravel kamu yang akan mengeksekusi OTP
        $webhookUrl = config('app.url') . '/api/internal/process-otp';

        // 2. Publish antrean ke Upstash QStash
        // 2. Ambil Kredensial dari .env
        $qStashToken = config('services.qstash.token');
        $qStashBaseUrl = config('services.qstash.url');

        // 3. Publish antrean ke Upstash QStash
        Http::withToken($qStashToken)
            ->post("{$qStashBaseUrl}/v2/publish/{$webhookUrl}", [
                'contact' => $contact,
                'otp' => $otp,
                'type' => $request->type
            ]);

        // 3. Kembalikan respon sukses instan ke Frontend (User tidak perlu menunggu WA/Email terkirim)
        return response()->json(['message' => 'OTP berhasil diproses dan akan segera dikirim.', 'status' => 'success']);
    }


    public function forgotPasswordReq(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'contact' => 'required',
            'type' => 'required|in:wa,email',
        ]);

        // Cek apakah ada User dengan Username dan Kontak (WA/Email) yang benar-benar cocok
        $user = User::where('name', $request->name)
            ->where(function ($query) use ($request) {
                if ($request->type == 'wa') {
                    $query->where('nowa', $request->contact);
                } else {
                    $query->where('email', $request->contact);
                }
            })->first();

        if (!$user) {
            return response()->json(['message' => 'Data pengguna tidak ditemukan atau tidak cocok dengan sistem kami.'], 404);
        }

        $contact = $request->contact;

        // Cek Batas Spam
        if (Cache::has('otp_rate_limit_' . $contact)) {
            return response()->json(['message' => 'Tunggu beberapa saat sebelum meminta OTP baru.'], 429);
        }

        // Tembak OTP ke Message Broker (Upstash)
        $otp = rand(100000, 999999);
        Cache::put('otp_' . $contact, $otp, 300); // 5 menit
        Cache::put('otp_rate_limit_' . $contact, true, 60);

        $webhookUrl = config('app.url') . '/api/internal/process-otp';
        $qStashToken = env('QSTASH_TOKEN');
        $qStashBaseUrl = env('QSTASH_URL', 'https://qstash.upstash.io');

        Http::withoutVerifying()
            ->withToken($qStashToken)
            ->post("{$qStashBaseUrl}/v2/publish/{$webhookUrl}", [
                'contact' => $contact,
                'otp' => $otp,
                'type' => $request->type
            ]);

        return response()->json(['message' => 'Permintaan diverifikasi. Kode OTP sedang dikirim.']);
    }

    /**
     * LANGKAH 2 LUPA SANDI: Verifikasi OTP dan Berikan Token Izin Ganti Sandi
     */
    public function verifyForgotOtp(Request $request)
    {
        $request->validate([
            'contact' => 'required',
            'otp' => 'required|numeric'
        ]);

        $storedOtp = Cache::get('otp_' . $request->contact);

        if (!$storedOtp || $storedOtp != $request->otp) {
            return response()->json(['message' => 'Kode OTP salah atau sudah kadaluwarsa.'], 400);
        }

        // OTP Benar! Buat "Kunci Spesial" (Token) agar user bisa mengakses form reset sandi
        $resetToken = Str::random(60);
        Cache::put('reset_token_' . $request->contact, $resetToken, 300);

        // Hapus OTP lama karena sudah terpakai
        Cache::forget('otp_' . $request->contact);

        return response()->json([
            'message' => 'Verifikasi berhasil.',
            'reset_token' => $resetToken
        ]);
    }

    /**
     * LANGKAH 3 LUPA SANDI: Timpa Password Lama
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'contact' => 'required',
            'reset_token' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        // Pastikan user punya Token Akses yang valid (Bukan Hacker yang melompati proses OTP)
        $validToken = Cache::get('reset_token_' . $request->contact);

        if (!$validToken || $validToken !== $request->reset_token) {
            return response()->json(['message' => 'Sesi atur ulang sandi tidak valid atau telah habis waktu.'], 403);
        }

        $user = User::where('name', $request->name)
            ->where(function ($q) use ($request) {
                $q->where('nowa', $request->contact)->orWhere('email', $request->contact);
            })->first();

        if (!$user) {
            return response()->json(['message' => 'Pengguna tidak ditemukan.'], 404);
        }

        DB::transaction(function () use ($user, $request) {
            // 1. Perbarui Sandi
            $user->update([
                'password' => Hash::make($request->password)
            ]);

            // 2. Audit Log: Pembaruan Kredensial Keamanan Akun
            DB::table('audit_logs')->insert([
                'user_id'     => $user->id,
                'type'        => 'sesi', // Berhubungan dengan manajemen sesi/kredensial
                'action'      => 'update',
                'description' => "Pengguna {$user->name} ({$request->contact}) telah berhasil memperbarui kata sandi akun melalui fitur lupa kata sandi.",
                'created_at'  => now(),
                'updated_at'  => now()
            ]);
        });

        // Hanguskan Kunci Akses (Token)
        Cache::forget('reset_token_' . $request->contact);

        return response()->json(['message' => 'Kata sandi berhasil diperbarui.']);
    }

    // Fungsi Pengiriman OTP yang dieksekusi message broker
    public function processOtp(Request $request)
    {
        // 1. Tangkap Payload dari Upstash
        $contact = $request->input('contact');
        $otp = $request->input('otp');
        $type = $request->input('type');

        try {
            // 2. Eksekusi pengiriman pesan
            if ($type == 'wa') {
                $response = WhatsappService::sendOtp($contact, $otp);
            } else {
                $response = EmailService::sendOtp($contact, $otp);
            }

            // 3. PENTING: Cek jika service lokal kita mengembalikan status false
            if (isset($response['status']) && $response['status'] === false) {
                // Kita harus "memaksa" error (throw exception) agar memicu HTTP 500
                throw new \Exception($response['message'] ?? 'Layanan API Pihak Ketiga Gagal');
            }

            // Kembalikan 200 OK ke Upstash agar antrean ditandai sebagai Selesai (Delivered)
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error("QStash Webhook Error: " . $e->getMessage());

            // Kembalikan HTTP 500. Upstash QStash sangat cerdas: jika dia menerima status 500, 
            // dia akan otomatis melakukan RETRY (mencoba mengirim ulang) beberapa detik/menit kemudian.
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
