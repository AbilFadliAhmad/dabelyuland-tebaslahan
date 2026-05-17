<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Services\EmailService;
use App\Services\WhatsappService;
use Illuminate\Support\Facades\Validator;


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
        // dd($request->all());
        // 1. Validasi Input Dasar
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:users,name',
            
            // Email wajib jika whatsapp tidak ada
            'email' => 'required_without:whatsapp|nullable|email|unique:users,email',
            
            // Whatsapp wajib jika email tidak ada
            'whatsapp' => 'required_without:email|nullable|unique:users,nowa',
            
            // Perhatikan: Input kamu "123" sedangkan syaratnya "min:8"
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

        // 3. Simpan User
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'nowa' => $request->whatsapp,
            'role' => 'user',
            'is_verified' => false,
        ]);

        // Bersihkan Cache
        Cache::forget('otp_' . $contact);

        return response()->json(['message' => 'Registrasi berhasil']);
    }

    // Proses login
    public function login(Request $request)
    {
        // Validasi input, nanti validasi ini akan dipindahkan ke request
        $request->validate([
            'name' => 'required|string',
            'password' => 'required|string',
        ], [
            'name.required' => 'Nama atau Email wajib diisi',
            'password.required' => 'Password wajib diisi',
        ]);

        // Siapkan credentials dengan mengambil key name dan password dari request pada form input
        $credentials = $request->only('name', 'password');

        // Verifikasi password dulu
        if (Auth::attempt($credentials, $request->has('remember'))) {
            // AMbil detail user dari session_id
            $user = Auth::user();

            // Check apakah user sudah diverifikasi atau belum
            if ($user->is_verified === false) {
                // Logout dan buat ulang session_id
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors(['login' => 'Anda harus diverifikasi oleh admin terlebih dahulu.'])->withInput();
            }

            // Buat ulang session_idnya agar lebih aman
            $request->session()->regenerate();

            // Redirect sesuai role
            if ($user->role === 'admin') {
                return redirect()->intended('/admin');
            } elseif ($user->role === 'user') {
                return redirect()->route('user.index'); // Halaman user
            } else {
                // Tangani jika ada user dengan role nyasar atau tidak dikenal
                Auth::logout();

                // Hancurkan sesi secara total agar tidak ada jejak login yang tertinggal
                $request->session()->invalidate();

                // Ganti CSRF token untuk mencegah serangan form injection
                $request->session()->regenerateToken();

                return back()->withErrors(['name' => 'Role akun tidak valid.'])->withInput();
            }
        }

        // Jika Password atau Nama salah (Gunakan pesan generic agar aman dari Hacker)
        return back()->withErrors([
            'name' => 'Akun atau Password yang Anda masukkan salah.',
        ])->withInput($request->only('name'));
    }

    // Logout akun
    public function logout()
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('login');
    }

    public function sendOtp(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'contact' => 'required',
            'type' => 'required|in:wa,email',
        ]);

        $contact = $request->contact;

        // Generate 6 digit angka acak
        $otp = rand(100000, 999999);

        // Simpan OTP di cache selama 5 menit (300 detik)
        Cache::put('otp_' . $contact, $otp, 300);

        // Kirim OTP
        if ($request->type == 'wa') {
            $response = WhatsappService::sendOtp($contact, $otp);
        } else {
            $response = EmailService::sendOtp($contact, $otp);
        }

        if ($response['status'] == true) {
            return response()->json(['message' => 'OTP berhasil dikirim']);
        }

        return response()->json(['message' => 'Gagal mengirim OTP'], 500);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required',
            'otp' => 'required|numeric'
        ]);

        $storedOtp = Cache::get('otp_' . $request->phone);

        if ($storedOtp && $storedOtp == $request->otp) {
            // Jika cocok, hapus OTP dari cache agar tidak bisa dipakai lagi
            Cache::forget('otp_' . $request->phone);
            
            return response()->json(['message' => 'Verifikasi berhasil!']);
        }

        return response()->json(['message' => 'Kode OTP salah atau sudah kadaluwarsa'], 400);
    }
}
