<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Registrasi | Dabelyuland</title>

    {{-- Font & Icons --}}
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    {{-- Tailwind CSS (Vite V4) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- SweetAlert2 untuk UI Notifikasi --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* Animasi Transisi Form Tanpa Refresh */
        .form-section {
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            width: 100%;
        }

        .form-hidden {
            opacity: 0;
            transform: translateY(15px);
            position: absolute;
            top: 0;
            left: 0;
            visibility: hidden;
            pointer-events: none;
        }

        .form-active {
            opacity: 1;
            transform: translateY(0);
            position: relative;
            visibility: visible;
            pointer-events: auto;
        }

        /* Modal Animasi */
        .modal-enter {
            animation: modalFadeIn 0.3s ease-out forwards;
        }

        .modal-exit {
            animation: modalFadeOut 0.3s ease-in forwards;
        }

        @keyframes modalFadeIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes modalFadeOut {
            from {
                opacity: 1;
                transform: scale(1);
            }

            to {
                opacity: 0;
                transform: scale(0.95);
            }
        }

        /* Font & Shadow Utilities */
        .font-heading {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .font-body {
            font-family: 'Inter', sans-serif;
        }

        /* Sembunyikan panah up/down di input number OTP */
        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type=number] {
            -moz-appearance: textfield;
        }
    </style>
</head>

<body class="font-body bg-white text-gray-800 overflow-hidden antialiased">
    <div class="flex h-screen w-full relative">

        {{-- ==========================================================
             SISI KIRI: Visual Non-Foto (Gradient & Abstract Shapes)
             ========================================================== --}}
        <div
            class="hidden lg:flex lg:w-5/12 relative bg-gradient-to-br from-[#0d9488] via-[#0f766e] to-[#042f2e] items-center justify-center overflow-hidden">
            <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-teal-400/30 rounded-full blur-3xl"></div>
            <div class="absolute bottom-[-10%] right-[-10%] w-[500px] h-[500px] bg-white/10 rounded-full blur-3xl">
            </div>

            <div class="relative z-10 flex flex-col items-center text-center px-10">
                <div
                    class="w-28 h-28 bg-white rounded-[24px] flex items-center justify-center shadow-2xl mb-8 transform hover:scale-105 transition-transform duration-500">
                    <img src="{{ asset('frontside/img/icon/logo-green.svg') }}" alt="Logo Dabelyuland"
                        class="w-16 h-16">
                </div>
                <h2 class="font-heading text-3xl font-bold text-white mb-4 tracking-tight">Dabelyuland</h2>
                <div class="w-12 h-1 bg-teal-400 rounded-full mb-5 mx-auto"></div>
                <p class="text-teal-50 text-base leading-relaxed font-light max-w-xs mx-auto">
                    Platform ekosistem properti terpercaya. Temukan, pasarkan, dan bangun properti impian Anda bersama
                    kami.
                </p>
            </div>
            <div class="absolute bottom-0 left-0 w-full h-32 bg-gradient-to-t from-black/10 to-transparent"></div>
        </div>

        {{-- ==========================================================
             SISI KANAN: Area Form Dinamis
             ========================================================== --}}
        <div
            class="w-full lg:w-7/12 bg-white relative flex flex-col justify-center h-full overflow-hidden px-6 sm:px-16 md:px-24 py-6">

            {{-- Tombol Kembali --}}
            <a href="{{ url('/') }}"
                class="absolute top-6 left-6 sm:top-8 sm:left-10 flex items-center text-sm font-semibold text-gray-400 hover:text-[#0d9488] transition-colors no-underline group z-40">
                <div
                    class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center mr-3 group-hover:bg-teal-50 transition-colors">
                    <i class="fas fa-arrow-left"></i>
                </div>
                <span class="hidden sm:inline">Beranda</span>
            </a>

            <div class="w-full max-w-md mx-auto relative mt-10">

                {{-- 1. FORM LOGIN --}}
                <div id="form-login" class="form-section form-active">
                    <div class="text-left mb-6">
                        <h1 class="font-heading text-3xl font-extrabold text-gray-900 mb-2">Selamat Datang 👋</h1>
                        <p class="text-gray-500 text-sm leading-relaxed">Silakan masuk untuk mengakses akun Anda.</p>
                    </div>

                    {{-- MENANGKAP ERROR DARI LARAVEL (Menyesuaikan logika file lama) --}}
                    @if ($errors->any())
                        <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-600 text-sm">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- MENGGUNAKAN FORM POST ASLI KE BACKEND --}}
                    <form action="{{ route('login') }}" method="POST" onsubmit="handleLoginSubmit(event)">
                        @csrf

                        {{-- Hidden --}}
                        <input type="hidden" name="fcm_token" id="login_fcm_token" value="">

                        <div class="mb-5">
                            <label
                                class="block text-xs font-bold text-gray-600 uppercase tracking-wide mb-2">Username</label>
                            <input type="text" name="name" placeholder="Masukkan username" required
                                class="w-full px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium text-gray-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0d9488]/30 focus:border-[#0d9488] transition-all placeholder-gray-400">
                        </div>

                        <div class="mb-4">
                            <label
                                class="block text-xs font-bold text-gray-600 uppercase tracking-wide mb-2">Password</label>
                            <div class="relative">
                                <input type="password" id="login_password" name="password" placeholder="••••••••"
                                    required
                                    class="w-full px-4 py-3.5 pr-12 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium text-gray-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0d9488]/30 focus:border-[#0d9488] transition-all placeholder-gray-400">
                                <button type="button" onclick="togglePassword('login_password', 'icon_login_pw')"
                                    class="absolute inset-y-0 right-0 px-4 flex items-center text-gray-400 hover:text-[#0d9488] focus:outline-none">
                                    <i class="fas fa-eye" id="icon_login_pw"></i>
                                </button>
                            </div>
                        </div>

                        <button type="button" onclick="switchForm('form-forgot')"
                            class="text-sm font-bold text-[#0d9488] hover:text-[#0f766e] transition-colors mb-8">
                            Lupa sandi?
                        </button>

                        <button type="submit"
                            class="w-full py-3.5 bg-[#0d9488] text-white font-bold rounded-xl shadow-lg shadow-teal-700/20 hover:bg-[#0f766e] transition-all transform hover:-translate-y-0.5">
                            Masuk
                        </button>

                        <div class="mt-8 text-center">
                            <p class="text-sm text-gray-500 font-medium">
                                Belum punya akun?
                                <button type="button" onclick="switchForm('form-register')"
                                    class="text-[#0d9488] font-bold hover:text-[#0f766e] transition-colors bg-transparent border-none p-0">Daftar
                                    sekarang</button>
                            </p>
                        </div>
                    </form>
                </div>

                {{-- 2. FORM REGISTRASI (Tetap menggunakan UI Dummy OTP kita) --}}
                <div id="form-register" class="form-section form-hidden">
                    <div class="text-left mb-8">
                        <h1 class="font-heading text-3xl font-extrabold text-gray-900 mb-2">Buat Akun Baru 🚀</h1>
                        <p class="text-gray-500 text-sm leading-relaxed">Daftarkan diri Anda untuk mulai mengiklankan
                            properti.</p>
                    </div>

                    <form id="registerFormElement" onsubmit="handleRegisterSubmit(event)">
                        @csrf
                        <div class="mb-4">
                            <label
                                class="block text-xs font-bold text-gray-600 uppercase tracking-wide mb-2">Username</label>
                            <input type="text" name="name" placeholder="Misal: budisantoso" required
                                class="w-full px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium text-gray-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0d9488]/30 focus:border-[#0d9488] transition-all placeholder-gray-400">
                        </div>

                        {{-- Tab Pilihan Pendaftaran: Email vs WhatsApp --}}
                        <div class="flex bg-gray-100 p-1 rounded-xl mb-4">
                            <button type="button" id="tab_reg_email" onclick="toggleContactMethod('reg', 'email')"
                                class="flex-1 py-2.5 text-sm font-bold rounded-lg bg-white shadow-sm text-gray-900 transition-all focus:outline-none">
                                <i class="fas fa-envelope mr-2"></i>Email
                            </button>
                            <button type="button" id="tab_reg_wa" onclick="toggleContactMethod('reg', 'wa')"
                                class="flex-1 py-2.5 text-sm font-bold rounded-lg text-gray-500 hover:text-gray-700 transition-all focus:outline-none">
                                <i class="fab fa-whatsapp mr-2"></i>WhatsApp
                            </button>
                        </div>

                        {{-- Input Dinamis Email --}}
                        <div id="group_reg_email" class="mb-5 block">
                            <input type="email" id="reg_email" name="email" placeholder="contoh@email.com"
                                class="w-full px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium text-gray-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0d9488]/30 focus:border-[#0d9488] transition-all placeholder-gray-400">
                        </div>

                        {{-- Input Dinamis WhatsApp --}}
                        <div id="group_reg_wa" class="mb-5 hidden">
                            <div class="flex">
                                <span
                                    class="inline-flex items-center px-4 py-3.5 bg-gray-100 border border-r-0 border-gray-200 rounded-l-xl text-sm font-bold text-gray-600">+62</span>
                                <input type="number" id="reg_wa" name="whatsapp" placeholder="81234567890"
                                    class="w-full px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-r-xl text-sm font-medium text-gray-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0d9488]/30 focus:border-[#0d9488] transition-all placeholder-gray-400">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
                            <div>
                                <label
                                    class="block text-xs font-bold text-gray-600 uppercase tracking-wide mb-2">Password</label>
                                <div class="relative">
                                    <input type="password" id="reg_password" name="password"
                                        placeholder="Min. 8 karakter" required
                                        class="w-full px-4 py-3.5 pr-10 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium text-gray-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0d9488]/30 focus:border-[#0d9488] transition-all placeholder-gray-400">
                                    <button type="button" onclick="togglePassword('reg_password', 'icon_reg_pw')"
                                        class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-400 hover:text-[#0d9488] focus:outline-none">
                                        <i class="fas fa-eye" id="icon_reg_pw"></i>
                                    </button>
                                </div>
                            </div>
                            <div>
                                <label
                                    class="block text-xs font-bold text-gray-600 uppercase tracking-wide mb-2">Ulangi
                                    Password</label>
                                <div class="relative">
                                    <input type="password" id="reg_password_conf" name="password_confirmation"
                                        placeholder="••••••••" required
                                        class="w-full px-4 py-3.5 pr-10 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium text-gray-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0d9488]/30 focus:border-[#0d9488] transition-all placeholder-gray-400">
                                    <button type="button"
                                        onclick="togglePassword('reg_password_conf', 'icon_reg_pw_conf')"
                                        class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-400 hover:text-[#0d9488] focus:outline-none">
                                        <i class="fas fa-eye" id="icon_reg_pw_conf"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <button type="submit"
                            class="w-full py-3.5 bg-gray-900 text-white font-bold rounded-xl shadow-lg shadow-gray-900/20 hover:bg-black transition-all transform hover:-translate-y-0.5">
                            Lanjutkan Pendaftaran
                        </button>

                        <div class="mt-8 text-center">
                            <p class="text-sm text-gray-500 font-medium">
                                Sudah punya akun?
                                <button type="button" onclick="switchForm('form-login')"
                                    class="text-[#0d9488] font-bold hover:text-[#0f766e] transition-colors bg-transparent border-none p-0">Masuk
                                    di sini</button>
                            </p>
                        </div>
                    </form>
                </div>

                {{-- 3. FORM LUPA SANDI --}}
                <div id="form-forgot" class="form-section form-hidden">
                    <div class="text-left mb-8">
                        <h1 class="font-heading text-3xl font-extrabold text-gray-900 mb-2">Lupa Sandi 🔒</h1>
                        <p class="text-gray-500 text-sm leading-relaxed">Pilih metode untuk menerima kode pemulihan
                            (OTP).</p>
                    </div>

                    <form onsubmit="handleForgotSubmit(event)">

                        {{-- TAMBAHAN: Input Username Wajib --}}
                        <div class="mb-4">
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide mb-2">Username
                                Terdaftar</label>
                            <input type="text" id="forgot_name" placeholder="Misal: budisantoso" required
                                class="w-full px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium text-gray-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0d9488]/30 focus:border-[#0d9488] transition-all placeholder-gray-400">
                        </div>

                        <div class="flex bg-gray-100 p-1 rounded-xl mb-4">
                            {{-- ... Tombol Email & WA Tetap ... --}}
                            <button type="button" id="tab_forgot_email"
                                onclick="toggleContactMethod('forgot', 'email')"
                                class="flex-1 py-2.5 text-sm font-bold rounded-lg bg-white shadow-sm text-gray-900 transition-all focus:outline-none"><i
                                    class="fas fa-envelope mr-2"></i>Email</button>
                            <button type="button" id="tab_forgot_wa" onclick="toggleContactMethod('forgot', 'wa')"
                                class="flex-1 py-2.5 text-sm font-bold rounded-lg text-gray-500 hover:text-gray-700 transition-all focus:outline-none"><i
                                    class="fab fa-whatsapp mr-2"></i>WhatsApp</button>
                        </div>

                        <div id="group_forgot_email" class="mb-8 block">
                            <input type="email" id="forgot_email" placeholder="Masukkan email terdaftar"
                                class="w-full px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium text-gray-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0d9488]/30 transition-all placeholder-gray-400">
                        </div>

                        <div id="group_forgot_wa" class="mb-8 hidden">
                            <div class="flex">
                                <span
                                    class="inline-flex items-center px-4 py-3.5 bg-gray-100 border border-r-0 border-gray-200 rounded-l-xl text-sm font-bold text-gray-600">+62</span>
                                <input type="number" id="forgot_wa" placeholder="81234567890"
                                    class="w-full px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-r-xl text-sm font-medium text-gray-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0d9488]/30 transition-all placeholder-gray-400">
                            </div>
                        </div>

                        <button type="submit"
                            class="w-full py-3.5 bg-gray-900 text-white font-bold rounded-xl shadow-lg hover:bg-black transition-all transform hover:-translate-y-0.5">Kirim
                            Kode OTP</button>

                        <div class="mt-8 text-center">
                            <button type="button" onclick="switchForm('form-login')"
                                class="text-sm text-gray-500 font-bold hover:text-[#0d9488] transition-colors bg-transparent border-none p-0">Kembali
                                ke halaman Login</button>
                        </div>
                    </form>
                </div>

                {{-- 4. FORM OTP (6 KOTAK TERPISAH) --}}
                <div id="form-otp" class="form-section form-hidden">
                    <div class="text-left mb-8">
                        <h1 class="font-heading text-3xl font-extrabold text-gray-900 mb-2">Verifikasi Kode ✉️</h1>
                        <p class="text-gray-500 text-sm leading-relaxed" id="otp_desc_text">
                            Kami telah mengirimkan 6 digit kode OTP ke kontak Anda.
                        </p>
                    </div>

                    <form onsubmit="handleVerifyOTP(event)">

                        {{-- 6 Kotak Input OTP --}}
                        <div class="flex justify-between gap-2 sm:gap-3 mb-8" id="otp_container">
                            <input type="text" maxlength="1"
                                class="otp-box w-10 h-12 sm:w-14 sm:h-16 text-center text-xl sm:text-2xl font-bold bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0d9488]/30 focus:border-[#0d9488] transition-all text-gray-900"
                                autofocus>
                            <input type="text" maxlength="1"
                                class="otp-box w-10 h-12 sm:w-14 sm:h-16 text-center text-xl sm:text-2xl font-bold bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0d9488]/30 focus:border-[#0d9488] transition-all text-gray-900">
                            <input type="text" maxlength="1"
                                class="otp-box w-10 h-12 sm:w-14 sm:h-16 text-center text-xl sm:text-2xl font-bold bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0d9488]/30 focus:border-[#0d9488] transition-all text-gray-900">
                            <input type="text" maxlength="1"
                                class="otp-box w-10 h-12 sm:w-14 sm:h-16 text-center text-xl sm:text-2xl font-bold bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0d9488]/30 focus:border-[#0d9488] transition-all text-gray-900">
                            <input type="text" maxlength="1"
                                class="otp-box w-10 h-12 sm:w-14 sm:h-16 text-center text-xl sm:text-2xl font-bold bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0d9488]/30 focus:border-[#0d9488] transition-all text-gray-900">
                            <input type="text" maxlength="1"
                                class="otp-box w-10 h-12 sm:w-14 sm:h-16 text-center text-xl sm:text-2xl font-bold bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0d9488]/30 focus:border-[#0d9488] transition-all text-gray-900">
                        </div>

                        <button type="submit"
                            class="w-full py-3.5 bg-[#0d9488] text-white font-bold rounded-xl shadow-lg shadow-teal-700/20 hover:bg-[#0f766e] transition-all transform hover:-translate-y-0.5 mb-6">
                            Verifikasi OTP
                        </button>

                        <div class="text-center">
                            <p class="text-sm text-gray-500 mb-2" id="otp-timer-text">
                                Belum menerima kode? Tunggu <span id="countdown"
                                    class="font-bold text-gray-900">60</span> detik.
                            </p>
                            <button type="button" id="btn-resend-otp" onclick="resendOTP()" disabled
                                class="text-sm font-bold text-gray-400 cursor-not-allowed transition-colors bg-transparent border-none p-0 focus:outline-none">
                                Kirim Ulang OTP
                            </button>
                        </div>
                    </form>
                </div>

                {{-- 5. FORM ATUR ULANG SANDI --}}
                <div id="form-reset-password" class="form-section form-hidden">
                    <div class="text-left mb-8">
                        <h1 class="font-heading text-3xl font-extrabold text-gray-900 mb-2">Atur Sandi Baru 🔑</h1>
                        <p class="text-gray-500 text-sm leading-relaxed">Silakan buat kata sandi baru untuk akun Anda.
                        </p>
                    </div>

                    <form onsubmit="handleResetPasswordSubmit(event)">
                        <div class="mb-5">
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide mb-2">Kata
                                Sandi Baru</label>
                            <div class="relative">
                                <input type="password" id="reset_new_password" placeholder="Min. 8 karakter" required
                                    minlength="8"
                                    class="w-full px-4 py-3.5 pr-12 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium text-gray-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0d9488]/30 focus:border-[#0d9488] transition-all placeholder-gray-400">
                                <button type="button" onclick="togglePassword('reset_new_password', 'icon_reset_pw')"
                                    class="absolute inset-y-0 right-0 px-4 flex items-center text-gray-400 hover:text-[#0d9488] focus:outline-none">
                                    <i class="fas fa-eye" id="icon_reset_pw"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-8">
                            <label
                                class="block text-xs font-bold text-gray-600 uppercase tracking-wide mb-2">Konfirmasi
                                Sandi Baru</label>
                            <div class="relative">
                                <input type="password" id="reset_confirm_password" placeholder="••••••••" required
                                    minlength="8"
                                    class="w-full px-4 py-3.5 pr-12 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium text-gray-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0d9488]/30 focus:border-[#0d9488] transition-all placeholder-gray-400">
                                <button type="button"
                                    onclick="togglePassword('reset_confirm_password', 'icon_reset_pw_conf')"
                                    class="absolute inset-y-0 right-0 px-4 flex items-center text-gray-400 hover:text-[#0d9488] focus:outline-none">
                                    <i class="fas fa-eye" id="icon_reset_pw_conf"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit"
                            class="w-full py-3.5 bg-[#0d9488] text-white font-bold rounded-xl shadow-lg shadow-teal-700/20 hover:bg-[#0f766e] transition-all transform hover:-translate-y-0.5">
                            Simpan Sandi Baru
                        </button>
                    </form>
                </div>

            </div>
        </div>

        {{-- ==========================================================
             MODAL: SURAT AGREEMENT DUMMY (Untuk Registrasi)
             ========================================================== --}}
        <div id="agreementModal" class="fixed inset-0 z-50 flex items-center justify-center hidden">
            <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" onclick="closeAgreementModal()"></div>
            <div
                class="relative bg-white w-[90%] max-w-lg rounded-2xl shadow-2xl p-6 md:p-8 flex flex-col max-h-[85vh]">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-heading text-xl font-bold text-gray-900">Syarat & Ketentuan</h3>
                    <button onclick="closeAgreementModal()"
                        class="text-gray-400 hover:text-red-500 transition-colors focus:outline-none">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                <div
                    class="flex-1 overflow-y-auto pr-2 mb-6 text-sm text-gray-600 leading-relaxed border border-gray-100 rounded-xl p-4 bg-gray-50">
                    <p class="mb-3 font-bold text-gray-800">1. Pendahuluan</p>
                    <p class="mb-4">Dengan mendaftar di Dabelyuland, Anda menyetujui seluruh ketentuan layanan kami
                        terkait jual beli dan manajemen properti. Data yang Anda masukkan akan dilindungi sesuai
                        kebijakan privasi kami.</p>
                    <p class="mb-3 font-bold text-gray-800">2. Penggunaan Akun</p>
                    <p class="mb-4">Akun hanya boleh digunakan oleh individu yang berwenang. Segala aktivitas dari
                        akun Anda adalah tanggung jawab Anda sepenuhnya.</p>
                    <p class="mb-3 font-bold text-gray-800">3. OTP & Keamanan</p>
                    <p>Kode OTP (One Time Password) bersifat rahasia. Jangan pernah membagikan kode ini kepada pihak
                        manapun, termasuk staf Dabelyuland.</p>
                </div>

                <label class="flex items-start cursor-pointer group mb-6">
                    <input type="checkbox" id="agreeCheckbox"
                        class="mt-1 w-4 h-4 text-[#0d9488] border-gray-300 rounded focus:ring-[#0d9488] transition-colors">
                    <span class="ml-3 text-sm text-gray-700 font-medium group-hover:text-gray-900 transition-colors">
                        Saya telah membaca, memahami, dan menyetujui Syarat & Ketentuan yang berlaku.
                    </span>
                </label>

                <button type="button" onclick="proceedToRegisterOTP()"
                    class="w-full py-3.5 bg-gray-900 text-white font-bold rounded-xl shadow-lg hover:bg-black transition-all transform hover:-translate-y-0.5">
                    Setuju & Lanjutkan
                </button>
            </div>
        </div>
    </div>

    {{-- ==========================================================
         SCRIPT LOGIKA UI
         ========================================================== --}}
    <script>
        // === VARIABEL GLOBAL ===
        let activeRegMethod = 'email';
        let activeForgotMethod = 'email';
        let currentOtpContext = '';
        let tempRegisterData = null;
        let tempForgotData = {}; // Variabel penampung data Lupa Sandi

        // === FUNGSI: TOGGLE PASSWORD (MATA) ===
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);

            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = "password";
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // === FUNGSI: LOGIKA KOTAK OTP (AUTO FOCUS & PASTE) ===
        document.addEventListener("DOMContentLoaded", function() {
            const otpBoxes = document.querySelectorAll('.otp-box');

            otpBoxes.forEach((box, index) => {
                // Saat user mengetik (pindah ke kotak selanjutnya)
                box.addEventListener('input', function(e) {
                    // Pastikan input hanya angka
                    this.value = this.value.replace(/[^0-9]/g, '');

                    if (this.value.length === 1 && index < otpBoxes.length - 1) {
                        otpBoxes[index + 1].focus();
                    }
                });

                // Saat user menekan tombol (Backsapce untuk mundur)
                box.addEventListener('keydown', function(e) {
                    if (e.key === 'Backspace' && this.value === '' && index > 0) {
                        otpBoxes[index - 1].focus();
                        otpBoxes[index - 1].value = ''; // Hapus isi kotak sebelumnya
                    }
                });

                // Fitur Paste 6 digit sekaligus
                box.addEventListener('paste', function(e) {
                    e.preventDefault();
                    let pastedData = e.clipboardData.getData('text').replace(/[^0-9]/g, '').slice(0,
                        6);
                    if (pastedData.length > 0) {
                        for (let i = 0; i < pastedData.length; i++) {
                            if (index + i < otpBoxes.length) {
                                otpBoxes[index + i].value = pastedData[i];
                                if (index + i < otpBoxes.length - 1) {
                                    otpBoxes[index + i + 1].focus();
                                } else {
                                    otpBoxes[index + i].blur(); // Lepas fokus di kotak terakhir
                                }
                            }
                        }
                    }
                });
            });
        });

        async function handleLoginSubmit(e) {
            e.preventDefault();
            const form = e.target;
            const tokenInput = document.getElementById('login_fcm_token');

            // Cek apakah browser mendukung notifikasi dan belum meminta izin
            if ('Notification' in window && Notification.permission === 'default') {
                const result = await Swal.fire({
                    title: 'Tetap Terhubung! 🔔',
                    text: 'Aktifkan notifikasi sekarang agar Anda tidak ketinggalan pemberitahuan penting, bahkan saat Anda tidak sedang membuka aplikasi.',
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonColor: '#0d9488',
                    cancelButtonColor: '#424242',
                    confirmButtonText: 'Ya, Aktifkan',
                    cancelButtonText: 'Nanti Saja'
                });

                if (result.isConfirmed) {
                    // Minta izin ke browser jika user menekan "Ya, Aktifkan"
                    await Notification.requestPermission();
                }
            }

            // Tampilkan efek loading untuk proses Login
            Swal.fire({
                title: 'Sedang Masuk...',
                text: 'Mempersiapkan akses Anda',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Tarik Token FCM (hanya akan berhasil jika permission sudah 'granted')
            try {
                if ('Notification' in window && Notification.permission === 'granted') {
                    // Bungkus proses FCM ke dalam Promise utama
                    const fetchTokenPromise = (async () => {
                        const registration = await navigator.serviceWorker.register(
                            '/firebase-messaging-sw.js', {  
                                scope: '/'
                            });
                        await navigator.serviceWorker.ready;
                        return await getFirebaseToken(firebaseMessaging, {
                            serviceWorkerRegistration: registration,
                        });
                    })();

                    // Buat Promise khusus untuk batas waktu (timeout 5 detik)
                    const timeoutPromise = new Promise((_, reject) =>
                        setTimeout(() => reject(new Error('FCM_TIMEOUT')), 10000)
                    );

                    // Balapan antara proses ambil token vs batas waktu
                    const currentToken = await Promise.race([fetchTokenPromise, timeoutPromise]);

                    if (currentToken) {
                        tokenInput.value = currentToken;
                    }
                } else {
                    console.warn('Izin notifikasi tidak diberikan. Melanjutkan login tanpa FCM token.');
                }
            } catch (err) {
                console.error('Terjadi kesalahan saat mengambil token FCM:', err);
            }

            // Eksekusi pengiriman form ke backend Laravel
            form.submit();
        }

        // === FUNGSI: SWITCH FORM ===
        function switchForm(targetId) {
            const sections = document.querySelectorAll('.form-section');
            sections.forEach(section => {
                if (section.id === targetId) {
                    section.classList.remove('form-hidden');
                    section.classList.add('form-active');
                } else {
                    section.classList.remove('form-active');
                    section.classList.add('form-hidden');
                }
            });
        }

        // === FUNGSI: TOGGLE TAB CONTACT (EMAIL/WA) ===
        function toggleContactMethod(formContext, method) {
            const btnEmail = document.getElementById(`tab_${formContext}_email`);
            const btnWa = document.getElementById(`tab_${formContext}_wa`);
            const groupEmail = document.getElementById(`group_${formContext}_email`);
            const groupWa = document.getElementById(`group_${formContext}_wa`);

            if (method === 'email') {
                btnEmail.classList.add('bg-white', 'shadow-sm', 'text-gray-900');
                btnEmail.classList.remove('text-gray-500', 'hover:text-gray-700');
                btnWa.classList.remove('bg-white', 'shadow-sm', 'text-gray-900');
                btnWa.classList.add('text-gray-500', 'hover:text-gray-700');

                groupEmail.classList.replace('hidden', 'block');
                groupWa.classList.replace('block', 'hidden');

                if (formContext === 'reg') activeRegMethod = 'email';
                if (formContext === 'forgot') activeForgotMethod = 'email';
            } else {
                btnWa.classList.add('bg-white', 'shadow-sm', 'text-gray-900');
                btnWa.classList.remove('text-gray-500', 'hover:text-gray-700');
                btnEmail.classList.remove('bg-white', 'shadow-sm', 'text-gray-900');
                btnEmail.classList.add('text-gray-500', 'hover:text-gray-700');

                groupWa.classList.replace('hidden', 'block');
                groupEmail.classList.replace('block', 'hidden');

                if (formContext === 'reg') activeRegMethod = 'wa';
                if (formContext === 'forgot') activeForgotMethod = 'wa';
            }
        }

        // === FUNGSI: SUBMIT REGISTRASI (Buka Modal) ===
        function handleRegisterSubmit(e) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);

            // Simpan data pendaftaran ke variabel sementara (di memori, bukan localStorage agar aman)
            tempRegisterData = Object.fromEntries(formData.entries());

            // Buka Modal Agreement
            const modal = document.getElementById('agreementModal');
            modal.classList.remove('hidden');
            modal.children[1].classList.add('modal-enter');
        }

        // Tutup Modal Agreement
        function closeAgreementModal() {
            const modal = document.getElementById('agreementModal');
            modal.children[1].classList.remove('modal-enter');
            modal.children[1].classList.add('modal-exit');
            setTimeout(() => modal.classList.add('hidden'), 300);
        }

        // Lanjut ke OTP
        async function proceedToRegisterOTP() {
            const isAgreed = document.getElementById('agreeCheckbox').checked;

            if (!isAgreed) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian',
                    text: 'Anda harus menyetujui syarat & ketentuan untuk melanjutkan.'
                });
                return;
            }

            // Tampilkan Loading
            Swal.showLoading();

            try {
                const contact = activeRegMethod === 'email' ? tempRegisterData.email : tempRegisterData.whatsapp;
                const response = await fetch("{{ route('send-otp') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        contact: contact,
                        type: activeRegMethod === 'email' ? 'email' : 'wa'
                    })
                });

                const result = await response.json();

                if (response.ok) {
                    Swal.close();
                    closeAgreementModal();
                    currentOtpContext = 'register';
                    switchForm('form-otp');
                    startOtpTimer();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: result.message
                    });
                }
            } catch (error) {
                console.error(error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan sistem.'
                });
            }
        }

        // === FUNGSI: RESEND OTP CERDAS (Untuk Registrasi & Lupa Sandi) ===
        async function resendOTP() {
            let contact, type;

            if (currentOtpContext === 'register') {
                if (!tempRegisterData) return switchForm('form-register');
                contact = activeRegMethod === 'email' ? tempRegisterData.email : tempRegisterData.whatsapp;
                type = activeRegMethod === 'email' ? 'email' : 'wa';
            } else if (currentOtpContext === 'forgot') {
                if (!tempForgotData.contact) return switchForm('form-forgot');
                contact = tempForgotData.contact;
                type = tempForgotData.type;
            }

            Swal.showLoading();
            try {
                // Baik Registrasi maupun Lupa Sandi, resend-nya cukup menggunakan rute /send-otp dasar
                const response = await axios.post("{{ route('send-otp') }}", {
                    contact,
                    type
                }, {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                Swal.fire({
                    icon: 'success',
                    title: 'OTP Terkirim',
                    text: 'Kode verifikasi baru telah dikirim!',
                    timer: 2000,
                    showConfirmButton: false
                });
                startOtpTimer();
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Mengirim',
                    text: error.response?.data?.message || 'Kesalahan sistem.'
                });
            }
        }

        // === FUNGSI: SUBMIT LUPA SANDI (Verifikasi Data User Asli) ===
        async function handleForgotSubmit(e) {
            e.preventDefault();
            const nameValue = document.getElementById('forgot_name').value;
            const contactValue = activeForgotMethod === 'email' ? document.getElementById('forgot_email').value :
                document.getElementById('forgot_wa').value;

            if (!nameValue || !contactValue) {
                return Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Mohon lengkapi Username dan Kontak Anda.'
                });
            }

            tempForgotData = {
                name: nameValue,
                contact: contactValue,
                type: activeForgotMethod === 'email' ? 'email' : 'wa'
            };

            Swal.showLoading();

            try {
                // Hubungi backend untuk mencocokkan nama dengan email/WA
                const response = await axios.post("{{ route('forgot-password-req') }}", tempForgotData, {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                Swal.close();
                document.getElementById('otp_desc_text').innerText =
                    `Kami telah mengirimkan 6 digit kode OTP ke ${activeForgotMethod === 'email' ? 'email' : 'nomor WhatsApp'} Anda.`;
                currentOtpContext = 'forgot';
                switchForm('form-otp');
                startOtpTimer();
            } catch (error) {
                console.error(error);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: error.response?.data?.message || 'Data tidak ditemukan.'
                });
            }
        }

        // === FUNGSI: VERIFIKASI OTP ===
        async function handleVerifyOTP(e) {
            e.preventDefault();
            let otpValue = '';
            document.querySelectorAll('.otp-box').forEach(box => otpValue += box.value);

            if (otpValue.length !== 6) return Swal.fire({
                icon: 'warning',
                title: 'OTP tidak lengkap'
            });

            Swal.showLoading();

            try {
                if (currentOtpContext === 'register') {
                    // Eksekusi Pembuatan Akun Baru
                    const finalData = {
                        ...tempRegisterData,
                        otp: otpValue
                    };
                    const response = await axios.post("{{ route('register') }}", finalData, {
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });

                    Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Akun berhasil dibuat. Silahkan login ulang.',
                            timer: 3000
                        })
                        .then(() => window.location.href = "{{ route('login') }}");
                } else if (currentOtpContext === 'forgot') {
                    // Hanya verifikasi kecocokan OTP, jika valid -> Berikan Hak Masuk ke Form Reset Sandi
                    const response = await axios.post("{{ route('verify-forgot-otp') }}", {
                        contact: tempForgotData.contact,
                        otp: otpValue
                    }, {
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });

                    Swal.close();

                    // Simpan "Kunci Spesial" ke memori
                    tempForgotData.reset_token = response.data.reset_token;

                    // Buka Form Baru
                    switchForm('form-reset-password');
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Verifikasi Gagal',
                    text: error.response?.data?.message || 'Proses gagal.'
                });
            }
        }

        // === FUNGSI: SIMPAN SANDI BARU ===
        async function handleResetPasswordSubmit(e) {
            e.preventDefault();
            const password = document.getElementById('reset_new_password').value;
            const password_confirmation = document.getElementById('reset_confirm_password').value;

            if (password !== password_confirmation) {
                return Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Konfirmasi sandi tidak cocok.'
                });
            }

            Swal.showLoading();
            try {
                // Tembak perubahan Sandi
                const response = await axios.post("{{ route('reset-password') }}", {
                    name: tempForgotData.name,
                    contact: tempForgotData.contact,
                    reset_token: tempForgotData.reset_token,
                    password: password,
                    password_confirmation: password_confirmation
                }, {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                Swal.fire({
                        icon: 'success',
                        title: 'Sandi Diperbarui!',
                        text: 'Kata sandi berhasil diubah. Silakan login.',
                        showConfirmButton: true,
                        confirmButtonColor: '#0d9488'
                    })
                    .then(() => {
                        document.getElementById('reset_new_password').value = '';
                        document.getElementById('reset_confirm_password').value = '';
                        tempForgotData = {}; // Bersihkan memori
                        switchForm('form-login');
                    });
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: error.response?.data?.message || 'Gagal mengubah sandi.'
                });
            }
        }

        // === FUNGSI: TIMER OTP ===
        let otpInterval;

        function startOtpTimer() {
            let timeLeft = 60;
            const countdownEl = document.getElementById('countdown');
            const resendBtn = document.getElementById('btn-resend-otp');
            const timerText = document.getElementById('otp-timer-text');

            // Reset box OTP jika timer diulang
            document.querySelectorAll('.otp-box').forEach(box => box.value = '');
            document.querySelector('.otp-box').focus(); // Fokus ke kotak 1

            clearInterval(otpInterval);
            countdownEl.innerText = timeLeft;
            timerText.style.display = 'block';
            resendBtn.disabled = true;
            resendBtn.classList.add('text-gray-400', 'cursor-not-allowed');
            resendBtn.classList.remove('text-[#0d9488]', 'hover:text-[#0f766e]');

            otpInterval = setInterval(() => {
                timeLeft--;
                countdownEl.innerText = timeLeft;

                if (timeLeft <= 0) {
                    clearInterval(otpInterval);
                    timerText.style.display = 'none';
                    resendBtn.disabled = false;
                    resendBtn.classList.remove('text-gray-400', 'cursor-not-allowed');
                    resendBtn.classList.add('text-[#0d9488]', 'hover:text-[#0f766e]');
                }
            }, 1000);
        }
    </script>
</body>

</html>
