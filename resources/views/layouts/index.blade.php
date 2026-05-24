    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="utf-8" />
        <title>@yield('title', '#tebaslahan - Jual Beli Properti Terpercaya di Indonesia')</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="description" content="@yield('meta_description', 'Jual beli properti terbaik - rumah, tanah, villa, dan bangunan di Indonesia.')" />
        <meta name="keywords"
            content="properti, rumah dijual, tanah dijual, villa, apartemen, properti Indonesia, jual beli properti, e-commerce properti, jual tanah di jombang" />
        <meta name="author" content="#tebaslahan" />
        <meta name="robots" content="index, follow" />
        <link rel="canonical" href="{{ url()->current() }}" />

        <!-- Open Graph dan Twitter Card -->
        <meta property="og:title" content="@yield('og_title', 'Jual Beli Properti Terpercaya - #tebaslahan')" />
        <meta property="og:description" content="@yield('og_description', 'Temukan rumah impian, apartemen, dan properti terbaik hanya di #tebaslahan.')" />
        <meta property="og:image" content="@yield('og_image', url('frontside/img/icon/dabelyuland.png'))" />
        <meta property="og:url" content="{{ url()->current() }}" />
        <meta property="og:type" content="website" />
        <meta property="og:site_name" content="#tebaslahan" />
        <meta property="og:locale" content="id_ID" />
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" content="@yield('twitter_title', 'Jual Beli Properti Terpercaya - tebaslahan')" />
        <meta name="twitter:description" content="@yield('twitter_description', 'Temukan rumah impian, apartemen, dan properti terbaik hanya di #tebaslahan.')" />
        <meta name="twitter:image" content="@yield('twitter_image', asset('frontside/img/icon/dabelyuland.png'))" />

        <link rel="icon" href="{{ asset('frontside/img/icon/dabelyuland.png') }}" type="image/png" />

        <!-- Favicon -->
        <link href="{{ asset('frontside/img/icon/dabelyuland.png') }}" rel="icon" />


        <!-- Google Web Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link
            href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap"
            rel="stylesheet" />


        <!-- Icon Font Stylesheet -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet" />

        <!-- Template Stylesheet -->
        <link href="{{ asset('frontside/css/style.css') }}" rel="stylesheet" />

        <!-- Swiper CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />

        {{-- TailwindCSS & FCM --}}
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            /* Animasi turun halus khusus untuk Mobile Menu saat dibuka */
            @keyframes mobileMenuFade {
                from {
                    opacity: 0;
                    transform: translateY(-10px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .animate-mobile-menu {
                animation: mobileMenuFade 0.3s ease-out forwards;
            }
        </style>

        @yield('styles')


        {{-- Tracking pengunjung unik untuk keperluan analisa --}}
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const visitorCookieName = 'daily_visitor_tracked';

                if (document.cookie.indexOf(visitorCookieName + '=') === -1) {
                    const maxAgeSeconds = getSecondsUntilMidnightWIB();
                    document.cookie = visitorCookieName + "=true; max-age=" + maxAgeSeconds + "; path=/";

                    const url = '{{ route('track-view-website') }}';

                    // Menggunakan FormData
                    let formData = new FormData();
                    formData.append('_token', '{{ csrf_token() }}'); // CSRF token langsung dari Blade

                    navigator.sendBeacon(url, formData);
                }
            });

            const trackWhatsAppClick = ($id) => {
                const cookieName = 'property_wa_clicked_' + $id;

                // 1. Cek apakah user sudah pernah klik tombol WA properti ini hari ini
                if (document.cookie.indexOf(cookieName + '=') === -1) {

                    // A. Set Cookie yang akan hangus otomatis jam 12 malam WIB
                    const maxAgeSeconds = getSecondsUntilMidnightWIB(); // Menggunakan fungsi hitung mundur Anda
                    document.cookie = cookieName + "=true; max-age=" + maxAgeSeconds + "; path=/";

                    // B. Siapkan URL dan Token CSRF Laravel
                    const url = "{{ route('track-click-whatsapp') }}";
                    const token = '{{ csrf_token() }}';

                    // C. Ambil parameter dari URL untuk melacak sumber (opsional, jika dibutuhkan)
                    const urlParams = new URLSearchParams(window.location.search);
                    const sourceParam = urlParams.get('source') || 'other';

                    // D. Bungkus data ke dalam FormData (Wajib karena sendBeacon mengirim via POST body)
                    const formData = new FormData();
                    formData.append('property_id', $id);
                    formData.append('source', sourceParam);
                    formData.append('_token', token); // Laravel otomatis membaca token dari body form ini

                    // E. Tembak data ke backend di latar belakang secara aman
                    navigator.sendBeacon(url, formData);
                }
            }
        </script>
    </head>


    <body class="flex flex-col min-h-screen bg-gray-50 font-['Inter']">

        {{-- Header --}}
        @include('layouts.templates.header', ['isUser' => false])

        {{-- Konten --}}
        @yield('content')

        <!-- Footer -->
        @include('layouts.templates.footer')
    </body>

    {{-- Script tambahan --}}
    @yield('scripts')

    {{-- Script untuk Animasi Mobile Menu --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: "{{ session('success') }}",
                    showConfirmButton: false,
                    timer: 2500, // Hilang otomatis dalam 2.5 detik
                    customClass: {
                        popup: 'rounded-2xl font-["Inter"]'
                    }
                });
            @endif

            // Cek juga jika ada session error (Opsional)
            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: "{{ session('error') }}",
                    confirmButtonColor: '#ef4444',
                    customClass: {
                        popup: 'rounded-2xl font-["Inter"]'
                    }
                });
            @endif

            /* --- 1. Logika Mobile Menu --- */
            const mobileBtn = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');

            if (mobileBtn && mobileMenu) {
                mobileBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const isExpanded = mobileBtn.getAttribute('aria-expanded') === 'true';

                    if (!isExpanded) {
                        mobileMenu.classList.remove('hidden');
                        mobileMenu.classList.add('animate-mobile-menu');
                        mobileBtn.setAttribute('aria-expanded', 'true');
                        mobileBtn.innerHTML =
                            `<span class="sr-only">Tutup menu</span><svg class="block h-7 w-7" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>`;
                    } else {
                        mobileMenu.classList.add('hidden');
                        mobileMenu.classList.remove('animate-mobile-menu');
                        mobileBtn.setAttribute('aria-expanded', 'false');
                        mobileBtn.innerHTML =
                            `<span class="sr-only">Buka menu navigasi</span><svg class="block h-7 w-7" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>`;
                    }
                });

                document.addEventListener('click', function(event) {
                    if (!mobileMenu.contains(event.target) && !mobileBtn.contains(event.target)) {
                        if (!mobileMenu.classList.contains('hidden')) {
                            mobileBtn.click();
                        }
                    }
                });
            }

            /* --- 2. Logika Sticky Header (Efek Scroll) --- */
            const headerWrapper = document.getElementById('header-wrapper');
            const topBar = document.getElementById('top-bar');
            const navLogo = document.getElementById('nav-logo');

            if (headerWrapper && topBar && navLogo) {
                window.addEventListener('scroll', function() {
                    if (window.scrollY > 40) {
                        topBar.classList.add('max-h-0', 'py-0', 'opacity-0');
                        topBar.classList.remove('py-2', 'opacity-100');

                        navLogo.classList.remove('w-[50px]', 'h-[50px]');
                        navLogo.classList.add('w-[40px]', 'h-[40px]');

                        headerWrapper.classList.add('shadow-md');
                        headerWrapper.classList.remove('shadow-sm');
                    } else {
                        topBar.classList.remove('max-h-0', 'py-0', 'opacity-0');
                        topBar.classList.add('py-2', 'opacity-100');

                        navLogo.classList.add('w-[50px]', 'h-[50px]');
                        navLogo.classList.remove('w-[40px]', 'h-[40px]');

                        headerWrapper.classList.add('shadow-sm');
                        headerWrapper.classList.remove('shadow-md');
                    }
                });
            }

        });
    </script>

    {{-- Script untuk Sticky Header --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // 1. Logika Sticky Header (Menyembunyikan Top Bar & Mengurangi Padding)
            const headerWrapper = document.getElementById('header-wrapper');
            const topBar = document.getElementById('top-bar');
            const mainNav = document.getElementById('main-nav');
            const navLogo = document.getElementById('nav-logo'); // Opsional: untuk mengecilkan logo

            window.addEventListener('scroll', function() {
                // Jika di-scroll lebih dari 40px ke bawah
                if (window.scrollY > 40) {
                    // Sembunyikan top bar (sosmed) dengan melipatnya (tinggi 0)
                    topBar.style.maxHeight = '0px';
                    topBar.style.paddingTop = '0px';
                    topBar.style.paddingBottom = '0px';
                    topBar.style.opacity = '0';

                    // Hapus padding-x (px-8 / px-12) pada Navbar utama agar penuh (Optional sesuai request Anda)
                    mainNav.classList.remove('lg:px-12', 'sm:px-6', 'px-4');
                    mainNav.classList.add(
                        'px-4'); // Sisakan sedikit padding agar tidak menempel ke tepi layar

                    // Opsional: Sedikit mengecilkan logo saat sticky agar lebih hemat ruang
                    navLogo.classList.remove('w-[50px]', 'h-[50px]');
                    navLogo.classList.add('w-[40px]', 'h-[40px]');

                } else {
                    // Kembalikan ke keadaan semula saat di puncak halaman
                    topBar.style.maxHeight = '100px'; // Angka cukup besar untuk menampung kontennya
                    topBar.style.paddingTop = ''; // Mengembalikan ke nilai class utility
                    topBar.style.paddingBottom = '';
                    topBar.style.opacity = '1';

                    // Kembalikan padding-x Navbar
                    mainNav.classList.add('lg:px-12', 'sm:px-6', 'px-4');

                    // Kembalikan ukuran logo
                    navLogo.classList.remove('w-[40px]', 'h-[40px]');
                    navLogo.classList.add('w-[50px]', 'h-[50px]');
                }
            });

            // 2. Logika Mobile Menu Toggle (Tetap sama seperti sebelumnya)
            const btn = document.getElementById('mobile-menu-button');
            const menu = document.getElementById('mobile-menu');

            btn.addEventListener('click', function() {
                const isExpanded = btn.getAttribute('aria-expanded') === 'true';

                if (!isExpanded) {
                    btn.setAttribute('aria-expanded', 'true');
                    menu.style.maxHeight = menu.scrollHeight + "px";
                } else {
                    btn.setAttribute('aria-expanded', 'false');
                    menu.style.maxHeight = "0px";
                }
            });

            window.addEventListener('resize', function() {
                if (window.innerWidth >= 1024) {
                    menu.style.maxHeight = null;
                    btn.setAttribute('aria-expanded', 'false');
                }
            });
        });
    </script>

    </html>
