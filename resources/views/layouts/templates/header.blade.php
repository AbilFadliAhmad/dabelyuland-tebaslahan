@if (!$isUser)
    <header id="header-wrapper" class="sticky top-0 z-50 w-full transition-all duration-300 bg-white shadow-sm">

        <div id="top-bar"
            class="w-full border-b border-gray-200 py-2 transition-all duration-300 overflow-hidden px-4 sm:px-6 lg:px-12 origin-top">
            <div class="max-w-7xl mx-auto flex justify-between items-center flex-wrap">
                <span class="text-xs text-gray-500 hidden md:inline">Paket Pemasaran Gratis untuk konsultasi</span>

                <div class="flex items-center gap-4 ml-auto">
                    <span class="text-xs text-gray-500 font-medium">Ikuti Kami</span>
                    <a href="https://www.youtube.com/@hardiwidyanto" target="_blank"
                        class="text-gray-500 hover:opacity-100 opacity-80 hover:scale-110 transition-all duration-300">
                        <img src="{{ asset('frontside/img/media/youtube_icon.png') }}" alt="YouTube"
                            class="w-5 h-5 object-contain" />
                    </a>
                    <a href="https://www.facebook.com/profile.php?id=61562823042702" target="_blank"
                        class="text-gray-500 hover:opacity-100 opacity-80 hover:scale-110 transition-all duration-300">
                        <img src="{{ asset('frontside/img/media/facebook_icon.png') }}" alt="Facebook"
                            class="w-5 h-5 object-contain" />
                    </a>
                    <a href="https://www.tiktok.com/@dabelyuland.indonesia?_t=ZS-8w6S74mJLGh&_r=1" target="_blank"
                        class="text-gray-500 hover:opacity-100 opacity-80 hover:scale-110 transition-all duration-300">
                        <img src="{{ asset('frontside/img/media/tiktok_icon.png') }}" alt="TikTok"
                            class="w-5 h-5 object-contain" />
                    </a>
                    <a href="https://www.instagram.com/hardi.widyanto/" target="_blank"
                        class="hover:opacity-100 opacity-80 hover:scale-110 transition-all duration-300">
                        <img src="{{ asset('frontside/img/media/instagram_icon.png') }}" alt="Instagram"
                            class="w-5 h-5 object-contain" />
                    </a>
                </div>
            </div>
        </div>

        {{-- MAIN NAVBAR --}}
        <div id="main-nav" class="w-full transition-all duration-300 px-4 sm:px-6 lg:px-8 bg-white relative">
            <div class="max-w-7xl mx-auto">
                <nav class="flex items-center justify-between py-3 h-20 relative">

                    {{-- Logo & Brand --}}
                    <a href="{{ route('home') }}" class="flex items-center gap-2 shrink-0 no-underline group">
                        <div class="p-1">
                            <img id="nav-logo"
                                class="w-[50px] h-[50px] object-contain transition-transform duration-500 group-hover:scale-105"
                                src="{{ asset('frontside/img/icon/logo-green.svg') }}" alt="Dabelyuland Logo"
                                loading="eager" />
                        </div>
                        <div class="flex flex-col sm:flex-row sm:items-baseline text-gray-900 sm:flex">
                            <h4
                                class="m-0 font-extrabold text-xl lg:text-2xl font-['Plus_Jakarta_Sans'] transition-colors duration-300 group-hover:text-[#0d9488] tracking-tight">
                                #tebaslahan</h4>
                        </div>
                    </a>

                    {{-- Desktop Menu Links (Tengah) --}}
                    <div class="hidden lg:flex lg:items-center lg:gap-8 absolute left-1/2 -translate-x-1/2">
                        <a href="{{ route('home') }}"
                            class="relative text-[15px] font-semibold transition-colors duration-300 no-underline py-2 group {{ request()->routeIs('home') ? 'text-[#0d9488]' : 'text-gray-600 hover:text-[#0d9488]' }}">
                            Beranda
                            <span
                                class="absolute bottom-0 left-0 h-[2px] bg-[#0d9488] transition-all duration-300 ease-out {{ request()->routeIs('home') ? 'w-full' : 'w-0 group-hover:w-full' }}"></span>
                        </a>

                        <a href="{{ route('shop.index') }}"
                            class="relative text-[15px] font-semibold transition-colors duration-300 no-underline py-2 group {{ request()->routeIs('shop.index') ? 'text-[#0d9488]' : 'text-gray-600 hover:text-[#0d9488]' }}">
                            Properti
                            <span
                                class="absolute bottom-0 left-0 h-[2px] bg-[#0d9488] transition-all duration-300 ease-out {{ request()->routeIs('shop.index') ? 'w-full' : 'w-0 group-hover:w-full' }}"></span>
                        </a>

                        <a href="{{ route('portfolio.index') }}"
                            class="relative text-[15px] font-semibold transition-colors duration-300 no-underline py-2 group {{ request()->routeIs('portfolio.index') ? 'text-[#0d9488]' : 'text-gray-600 hover:text-[#0d9488]' }}">
                            Portofolio
                            <span
                                class="absolute bottom-0 left-0 h-[2px] bg-[#0d9488] transition-all duration-300 ease-out {{ request()->routeIs('portfolio.index') ? 'w-full' : 'w-0 group-hover:w-full' }}"></span>
                        </a>
                    </div>

                    {{-- Right Actions (Ikon Selalu Tampil di Mobile & Desktop) --}}
                    <div class="flex items-center gap-1 sm:gap-3 ml-auto lg:ml-0">

                        {{-- 1. DROPDOWN FAVORIT --}}
                        <div class="relative inline-block text-left" id="favDropdownWrapper">
                            <button type="button" id="favDropdownBtn"
                                class="relative p-2 text-gray-500 hover:bg-rose-50 hover:text-rose-500 rounded-full transition-all focus:outline-none group"
                                title="Properti Favorit">
                                <svg class="w-6 h-6 sm:w-[22px] sm:h-[22px] group-hover:animate-wiggle" fill="none"
                                    stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                                </svg>
                                <span id="nav-fav-count"
                                    class="absolute top-1 right-1 w-4 h-4 bg-rose-500 text-white text-[10px] font-bold rounded-full items-center justify-center border-[1.5px] border-white transition-transform hidden">0</span>
                            </button>

                            {{-- Panel Favorit (Diatur lebarnya agar aman di HP) --}}
                            <div id="favDropdownPanel"
                                class="hidden absolute right-[-40px] sm:right-0 mt-3 w-[290px] sm:w-[380px] bg-white rounded-2xl shadow-[0_10px_40px_rgba(0,0,0,0.15)] border border-gray-100 z-50 transform origin-top-right transition-all duration-200 opacity-0 scale-95">
                                <div
                                    class="flex items-center justify-between px-5 py-3.5 border-b border-gray-100 bg-white rounded-t-2xl sticky top-0 z-10">
                                    <h3
                                        class="font-['Plus_Jakarta_Sans'] font-bold text-gray-900 text-sm sm:text-base m-0 flex items-center">
                                        <i class="fas fa-heart text-rose-500 mr-2 text-sm"></i> Tersimpan
                                    </h3>
                                    <span id="favDropdownCount"
                                        class="text-xs font-bold text-gray-500 bg-gray-100 px-2 py-0.5 rounded-md">0
                                        item</span>
                                </div>

                                <div id="favorite-list-container"
                                    class="max-h-87.5 overflow-y-auto [&::-webkit-scrollbar]:w-1.5 [&::-webkit-scrollbar-thumb]:bg-gray-200 [&::-webkit-scrollbar-thumb]:rounded-full flex flex-col p-2 space-y-1">
                                </div>

                                <div class="border-t border-gray-100">
                                    <a href="#" onclick="clearAllFavorites(event)"
                                        class="block text-center px-4 py-3 text-sm font-bold text-red-500 hover:bg-red-50 rounded-b-2xl transition-colors no-underline">
                                        Hapus Semua
                                    </a>
                                </div>
                            </div>
                        </div>

                        {{-- 2. DROPDOWN NOTIFIKASI --}}
                        <div class="relative inline-block text-left" id="notifDropdownWrapper">
                            <button type="button" id="notifBellBtn"
                                class="relative p-2 text-gray-500 hover:bg-gray-100 hover:text-[#0d9488] rounded-full transition-all focus:outline-none group"
                                title="Notifikasi">
                                <svg class="w-6 h-6 sm:w-6 sm:h-6 group-hover:animate-wiggle" fill="none"
                                    stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                                </svg>
                                <span id='nav-notif-count'
                                    class="absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-rose-500 border-2 border-white rounded-full hidden"></span>
                            </button>

                            {{-- Panel Notifikasi --}}
                            <div id="notifDropdownPanel"
                                class="hidden absolute right-[-10px] sm:right-0 mt-3 w-[290px] sm:w-[380px] bg-white rounded-2xl shadow-[0_10px_40px_rgba(0,0,0,0.15)] border border-gray-100 z-50 transform origin-top-right transition-all duration-200 opacity-0 scale-95">
                                <div
                                    class="flex items-center justify-between px-5 py-3.5 border-b border-gray-100 bg-white rounded-t-2xl sticky top-0 z-10">
                                    <h3
                                        class="font-['Plus_Jakarta_Sans'] font-bold text-gray-900 text-sm sm:text-base m-0">
                                        Notifikasi</h3>
                                    <a href="{{ route('notifikasi') }}"
                                        class="text-gray-400 hover:text-[#0d9488] transition-colors p-1"
                                        title="Pengaturan Notifikasi">
                                        <i class="fas fa-cog text-base sm:text-lg"></i>
                                    </a>
                                </div>

                                <div id="notifPanel"
                                    class="max-h-87.5 overflow-y-auto [&::-webkit-scrollbar]:w-1.5 [&::-webkit-scrollbar-thumb]:bg-gray-200 [&::-webkit-scrollbar-thumb]:rounded-full">
                                </div>

                                <div class="border-t border-gray-100">
                                    <a href="#"
                                        class="block text-center px-4 py-3 text-xs sm:text-sm font-bold text-[#0d9488] hover:bg-gray-50 rounded-b-2xl transition-colors no-underline">
                                        Tandai semua dibaca
                                    </a>
                                </div>
                            </div>
                        </div>

                        {{-- Tombol Login / Jual Properti (Desktop Only) --}}
                        <div class="hidden lg:block ml-2 border-l border-gray-200 pl-4">
                            <a href="{{ route('login') }}"
                                class="inline-flex items-center justify-center px-6 py-2.5 border border-transparent text-sm font-bold rounded-full text-white bg-[#198754] hover:bg-[#146c43] shadow-[0_4px_12px_rgba(25,135,84,0.3)] hover:-translate-y-0.5 transition-all no-underline">
                                <i class="fas fa-building mr-2"></i> Jual Propertimu
                            </a>
                        </div>

                        {{-- Hamburger Menu Button (Mobile Only) --}}
                        <button type="button" id="mobile-menu-button"
                            class="lg:hidden inline-flex items-center justify-center p-2 ml-1 rounded-md text-gray-500 hover:text-[#0d9488] hover:bg-teal-50 transition-colors focus:outline-none">
                            <svg class="block h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>

                    </div>
                </nav>
            </div>

            {{-- Mobile Dropdown Menu --}}
            <div id="mobile-menu"
                class="lg:hidden absolute top-full left-0 w-full bg-white shadow-xl border-t border-gray-100 hidden">
                <div class="px-6 py-6 space-y-2 max-w-7xl mx-auto bg-white">
                    <a href="{{ route('home') }}"
                        class="block px-4 py-3 rounded-xl text-base font-semibold no-underline transition-colors {{ request()->routeIs('home') ? 'bg-teal-50 text-[#0d9488]' : 'text-gray-700 hover:bg-gray-50 hover:text-[#0d9488]' }}">
                        Beranda
                    </a>
                    <a href="{{ route('shop.index') }}"
                        class="block px-4 py-3 rounded-xl text-base font-semibold no-underline transition-colors {{ request()->routeIs('shop.index') ? 'bg-teal-50 text-[#0d9488]' : 'text-gray-700 hover:bg-gray-50 hover:text-[#0d9488]' }}">
                        Properti
                    </a>
                    <a href="{{ route('portfolio.index') }}"
                        class="block px-4 py-3 rounded-xl text-base font-semibold no-underline transition-colors {{ request()->routeIs('portfolio.index') ? 'bg-teal-50 text-[#0d9488]' : 'text-gray-700 hover:bg-gray-50 hover:text-[#0d9488]' }}">
                        Portofolio
                    </a>

                    <div class="mt-6 pt-4 border-t border-gray-100">
                        <a href="{{ route('login') }}"
                            class="w-full flex items-center justify-center px-4 py-3 border border-transparent text-base font-bold rounded-xl text-white bg-[#198754] shadow-md active:scale-95 transition-all no-underline">
                            <i class="fas fa-building mr-2"></i> Jual Propertim
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <script>
        document.addEventListener('DOMContentLoaded', async function() {
            console.log('Script dijalankan');

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


            /* --- 3. Logika Dropdown Ganda (Favorit & Notif) --- */
            const favBtn = document.getElementById('favDropdownBtn');
            const favPanel = document.getElementById('favDropdownPanel');
            const favWrapper = document.getElementById('favDropdownWrapper');

            const notifBtn = document.getElementById('notifBellBtn');
            const notifPanel = document.getElementById('notifDropdownPanel');
            const notifWrapper = document.getElementById('notifDropdownWrapper');

            // Ambil jumlah favorit dan notif
            const favCount = await idb_get('lengthFavorites') ?? 0;
            const notifCount = await idb_get('lengthNotifications') ?? 0;

            // Badge Notifikasi dan favorit
            const favBadge = document.getElementById('nav-fav-count');
            const notifBadge = document.getElementById('nav-notif-count');

            // Perbarui jumlah favorit
            if (favCount > 0) {
                document.getElementById('favDropdownCount').innerText = favCount + ' item';
                favBadge.innerText = favCount;
                favBadge.classList.remove('hidden');
            } else {
                favBadge.innerText = '';
                favBadge.classList.add('hidden');
            }

            // Tambahkan dot di badge notifikasi
            if (notifCount > 0) {
                notifBadge.classList.remove('hidden');
            } else {
                notifBadge.classList.add('hidden');
            }

            // Helper: Buka Dropdown
            function openDropdown(panel) {
                if (panel) {
                    panel.classList.remove('hidden');
                    setTimeout(() => {
                        panel.classList.remove('opacity-0', 'scale-95');
                        panel.classList.add('opacity-100', 'scale-100');
                    }, 10);
                }
            }

            // Helper: Tutup Dropdown
            function closeDropdown(panel) {
                if (panel && !panel.classList.contains('hidden')) {
                    panel.classList.remove('opacity-100', 'scale-100');
                    panel.classList.add('opacity-0', 'scale-95');
                    setTimeout(() => {
                        panel.classList.add('hidden');
                    }, 200);
                }
            }

            // Event Klik Hati Favorit
            if (favBtn && favPanel) {
                favBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    closeDropdown(notifPanel); // Tutup panel notif jika terbuka

                    if (favPanel.classList.contains('hidden')) {
                        openDropdown(favPanel);
                        getAllFavorites();
                    } else {
                        closeDropdown(favPanel);
                    }
                });
            }

            // Event Klik Lonceng Notifikasi
            if (notifBtn && notifPanel) {
                notifBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    closeDropdown(favPanel); // Tutup panel favorit jika terbuka

                    if (notifPanel.classList.contains('hidden')) {
                        openDropdown(notifPanel);
                        getAllNotifications();
                    } else {
                        closeDropdown(notifPanel);
                    }
                });
            }

            // Tutup dropdown jika area luar diklik
            document.addEventListener('click', function(e) {
                if (favWrapper && !favWrapper.contains(e.target)) closeDropdown(favPanel);
                if (notifWrapper && !notifWrapper.contains(e.target)) closeDropdown(notifPanel);
            });
        });
        // Akhir dari DOMContentLoaded

        // Fungsi untuk mengambil daftar favorit
        async function getAllFavorites() {
            const listContainer = document.getElementById('favorite-list-container');
            if (!listContainer) return;

            // 1. Tampilkan Animasi Loading (Spinner)
            listContainer.innerHTML = `
                    <div class="py-12 flex flex-col items-center justify-center text-center">
                        <div class="w-10 h-10 border-4 border-gray-100 border-t-[#0d9488] rounded-full animate-spin mb-3"></div>
                        <p class="text-xs font-bold text-gray-500">Memuat properti tersimpan...</p>
                    </div>
                `;

            // Pastikan library idb_ready sudah siap (Helper dari percakapan sebelumnya)
            try {
                // 2. Ambil daftar ID dari IndexedDB
                const favIds = await idb_get('listFavorites') || [];

                // 3. Jika tidak ada ID, tampilkan state kosong
                if (favIds.length === 0) {
                    listContainer.innerHTML = `
                                <div class="py-10 flex flex-col items-center justify-center text-center">
                                    <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center text-gray-300 mb-3">
                                        <i class="fas fa-heart text-xl"></i>
                                    </div>
                                    <p class="text-sm font-bold text-gray-700 m-0">Belum ada favorit</p>
                                    <p class="text-[11px] text-gray-400 mt-1 px-4">Properti yang Anda simpan akan muncul di sini.</p>
                                </div>
                            `;
                    return;
                }

                // 4. Fetch data detail dari API berdasarkan array ID
                const response = await fetch("{{ route('favorite-list') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                            ?.content
                    },
                    body: JSON.stringify({
                        ids: favIds
                    })
                });

                if (!response.ok) throw new Error('Gagal mengambil data dari server');

                const favoritesData = await response.json();

                // 5. Bersihkan kontainer dan Render data
                listContainer.innerHTML = '';

                favoritesData.forEach(item => {
                    const cardHTML = `
                                <div id="favorite-${item.id}" class="group flex items-start gap-2.5 sm:gap-3 p-2.5 sm:p-3 hover:bg-gray-50 rounded-xl transition-colors relative border border-transparent hover:border-gray-100">
                                    <div class="shrink-0 w-[60px] h-[60px] sm:w-[70px] sm:h-[70px] rounded-lg overflow-hidden bg-gray-100 border border-gray-200/60">
                                        <img src="${item.gambar}" class="w-full h-full object-cover" alt="Properti">
                                    </div>
                                    <div class="flex-grow min-w-0 pr-6">
                                        <p class="text-[9px] sm:text-[10px] text-gray-500 font-bold uppercase tracking-wider mb-0.5 truncate">
                                            <i class="fas fa-map-marker-alt text-[#0d9488] mr-1"></i> ${item.kota}
                                        </p>
                                        <h4 class="text-xs sm:text-sm font-bold text-gray-800 m-0 leading-tight truncate mb-1">
                                            <a href="${item.slug}" class="no-underline text-gray-800 hover:text-[#0d9488] before:absolute before:inset-0">
                                                ${item.judul}
                                            </a>
                                        </h4>
                                        <p class="text-xs sm:text-sm font-extrabold text-[#0d9488] m-0">${formatRupiah(item.harga)}</p>
                                    </div>
                                    <button onclick="removeFavorite(${item.id})" class="absolute top-2 sm:top-3 right-2 sm:right-3 w-6 h-6 sm:w-7 sm:h-7 bg-white border border-gray-100 rounded-full flex items-center justify-center text-gray-400 hover:text-rose-500 hover:bg-rose-50 shadow-sm transition-all focus:outline-none z-10 opacity-0 group-hover:opacity-100">
                                        <i class="fas fa-trash-alt text-[9px] sm:text-[10px]"></i>
                                    </button>
                                </div>
                            `;
                    listContainer.insertAdjacentHTML('beforeend', cardHTML);
                });

            } catch (error) {
                console.error(error);
                listContainer.innerHTML = `
                            <div class="py-8 text-center">
                                <p class="text-xs text-red-500 font-bold">Terjadi kesalahan saat memuat data.</p>
                            </div>
                        `;
            }
        };

        // Fungsi menghapus satu item favorit
        async function removeFavorite(id, event) {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }

            // Pastikan ID dalam bentuk integer agar cocok saat filtering
            const propertyId = parseInt(id);
            let isDeleted = false;
            document.getElementById(`favorite-${id}`).classList.add('hidden');

            try {
                // 1. Update List: Hapus ID dari array 'listFavorites'
                await idb_update('listFavorites', (list) => {
                    const currentList = list || [];
                    const index = currentList.indexOf(propertyId);

                    // Cek apakah ID ada di dalam list
                    if (index > -1) {
                        currentList.splice(index, 1); // Hapus 1 elemen pada posisi index tersebut
                        isDeleted = true; // Tandai bahwa proses hapus berhasil
                    }

                    return currentList;
                });

                // 2. Update Jumlah: Kurangi 1 dari 'lengthFavorites'
                if (isDeleted) {
                    let currentCount = 0;
                    await idb_update('lengthFavorites', (count) => {
                        currentCount = count || 0;
                        return Math.max(0, currentCount - 1); // Jangan biarkan angka jadi negatif
                    });

                    // Update UI Dot favorit
                    const navFavCount = document.getElementById('nav-fav-count');
                    navFavCount.innerText = currentCount - 1;
                    if (currentCount - 1 === 0) navFavCount.classList.add('hidden');

                    // Update UI panel favorit
                    document.getElementById('favDropdownCount').innerText = currentCount - 1 + ' item';

                    // Update UI icon favorit pada card properti
                    const svgIcons = document.querySelectorAll('.heart-' + propertyId);
                    svgIcons.forEach(svg => {
                        svg.classList.replace('text-rose-500', 'text-gray-500')
                        svg.setAttribute('fill', 'none');
                    });
                }


                // Feedback user
                if (typeof Toast !== 'undefined') {
                    Toast.fire({
                        icon: 'info',
                        title: 'Properti dihapus dari favorit'
                    });
                }

            } catch (error) {
                console.error("Gagal menghapus favorit dari IndexedDB:", error);
            }
        }

        // Fungsi membersihkan semua daftar favorit
        async function clearAllFavorites(event) {
            if (event) event.preventDefault();

            const favoritesCount = await idb_get('lengthFavorites') || 0;
            if (!favoritesCount) return;

            const result = await Swal.fire({
                title: 'Kosongkan Favorit?',
                text: 'Semua properti tersimpan akan dihapus dari lokal.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Kosongkan',
                cancelButtonText: 'Tidak, Simpan Saja',
                reverseButtons: true, // Menukar posisi tombol agar 'Ya' di kanan
                customClass: {
                    confirmButton: 'bg-teal-600 px-6 py-2 rounded-xl text-white font-bold mx-2',
                    cancelButton: 'bg-gray-100 px-6 py-2 rounded-xl text-gray-600 font-bold mx-2'
                },
                buttonsStyling: false // Matikan style default agar class Tailwind kamu jalan
            });

            if (result.isConfirmed) {
                // Logika Hapus yang kita bahas tadi
                await idb_set('listFavorites', []);
                await idb_set('lengthFavorites', 0);

                document.getElementById('favDropdownCount').innerText = '0 item';
                document.getElementById('nav-fav-count').innerText = '0';
                document.getElementById('nav-fav-count').classList.add('hidden');

                // Update UI icon favorit pada card properti
                const svgIcons = document.querySelectorAll('.heart-icon');
                svgIcons.forEach(svg => {
                    svg.classList.replace('text-rose-500', 'text-gray-500')
                    svg.setAttribute('fill', 'none');
                });

                // Feedback user
                Toast.fire({
                    icon: 'success',
                    title: 'Daftar favorit dikosongkan'
                });
            }
        }

        async function getAllNotifications() {
            const notifPanel = document.getElementById('notifPanel');
            if (!notifPanel) return;

            const iconMap = {
                'rumah': 'fas fa-home',
                'apartemen': 'far fa-building',
                'ruko': 'fas fa-store',
                'kantor': 'fas fa-briefcase',
                'gudang': 'fas fa-warehouse',
                'tanah': 'fas fa-map-marked-alt'
            };

            try {
                const notifications = await idb_get('listNotifications') ?? [];

                // 1. Tangani jika tidak ada notifikasi
                if (notifications.length === 0) {
                    notifPanel.innerHTML = `
                            <div class="py-10 text-center">
                                <p class="text-xs text-gray-600">Belum ada notifikasi baru.</p>
                            </div>
                        `;
                    return;
                }

                // 2. Gunakan map & join untuk performa render yang jauh lebih cepat
                const html = notifications.map(notif => {
                    return `
                            <a onclick="handleNotifClick('${notif.id}', '${notif.slug}', event)"
                                class="flex items-start gap-3 px-4 sm:px-5 py-3 hover:bg-gray-50 transition-colors border-b border-gray-100 no-underline group relative ${!notif.isRead ? 'bg-blue-50/30' : ''}">
                                
                                ${!notif.isRead ? `<div class="absolute left-1.5 top-1/2 -translate-y-1/2 w-2 h-2 bg-blue-500 rounded-full"></div>` : ''}
                                
                                <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-teal-100 text-[#0d9488] flex items-center justify-center shrink-0">
                                    <i class="${iconMap[notif.typeProperty] || 'fas fa-home'} text-xs sm:text-sm"></i>
                                </div>

                                <div class="flex-grow min-w-0 pr-2">
                                    <p class="text-xs sm:text-sm text-gray-800 leading-snug mb-1 font-['Inter']">
                                        <span class="font-bold">${notif.title}</span>: ${notif.body}
                                    </p>
                                    <span class="text-[10px] sm:text-xs text-gray-400 font-medium">${notif.timestamp}</span>
                                </div>
                            </a>
                        `;
                }).join('');

                notifPanel.innerHTML = html;

            } catch (error) {
                console.error("Gagal mengambil daftar notifikasi:", error);
            }
        }

        async function handleNotifClick(id, url) {
            try {
                let isUpdate = false
                await idb_update('listNotifications', (list) => {
                    const currentList = list || [];
                    const index = currentList.findIndex(notif => String(notif.id) === String(id));

                    if (index !== -1) {
                        currentList[index].isRead = true;
                        isUpdate = true;
                    }
                    return currentList;
                });

                if (isUpdate) {
                    const updatedLen = await idb_update('lengthNotifications', (len) => {
                        const newLen = parseInt(len) || 0;
                        return Math.max(0, newLen - 1);
                    });
                    if (updatedLen <= 0) document.getElementById.classList.add('hidden');
                }

                window.location.href = url; // Baru pindah halaman
            } catch (err) {
                window.location.href = url; // Tetap pindah jika database error
            }
        }
    </script>
@else
    <header
        class="bg-white border-b border-gray-100 sticky top-0 z-20 shadow-[0_2px_10px_rgba(0,0,0,0.02)] font-['Inter']">
        <div class="w-full px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-[72px]">

                {{-- Tombol Mobile Sidebar Toggle --}}
                <div class="flex items-center xl:hidden">
                    <button id="mobileMenuBtn"
                        class="p-2 text-gray-500 hover:text-[#0d9488] bg-gray-50 hover:bg-teal-50 rounded-xl transition-all focus:outline-none">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>
                </div>

                <div class="flex items-center gap-3 sm:gap-5 ml-auto">

                    {{-- Tombol Upgrade Cepat (Hanya User) --}}
                    @if (Auth::check() && Auth::user()->role !== 'admin')
                        <a href="#"
                            class="hidden sm:inline-flex items-center px-4 py-2 bg-gradient-to-r from-amber-200 to-yellow-400 hover:from-amber-300 hover:to-yellow-500 text-yellow-900 text-sm font-bold rounded-xl transition-all shadow-sm hover:shadow-md hover:-translate-y-0.5 no-underline">
                            <i class="fas fa-crown mr-2"></i> Upgrade Pro
                        </a>
                    @endif

                    {{-- Dropdown Notifikasi --}}
                    <div class="relative" id="notificationDropdown">
                        <button id="notifBtn"
                            class="relative p-2.5 text-gray-500 hover:text-[#0d9488] transition-colors rounded-full hover:bg-gray-50 focus:outline-none">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                            </svg>

                            @if (isset($pendingUsers) && $pendingUsers->count() > 0)
                                <span class="absolute top-1.5 right-1.5 flex h-4 w-4">
                                    <span
                                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                    <span
                                        class="relative inline-flex rounded-full h-4 w-4 bg-red-500 text-[10px] text-white items-center justify-center font-bold border-2 border-white">
                                        {{ $pendingUsers->count() }}
                                    </span>
                                </span>
                            @endif
                        </button>

                        <div id="notifMenu"
                            class="absolute right-0 mt-3 w-80 sm:w-96 bg-white rounded-2xl shadow-xl border border-gray-100 opacity-0 invisible transform scale-95 transition-all duration-200 origin-top-right z-50 overflow-hidden">
                            <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50">
                                <h6 class="text-sm font-bold text-gray-800 m-0">Notifikasi Masuk</h6>
                            </div>
                            <div
                                class="max-h-[320px] overflow-y-auto [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]">
                                @if (isset($pendingUsers) && $pendingUsers->count() > 0)
                                    @foreach ($pendingUsers as $user)
                                        <a href="{{ route('admin.list.users') }}"
                                            class="flex items-start px-5 py-4 hover:bg-teal-50/50 transition-colors border-b border-gray-50 no-underline last:border-0">
                                            <div
                                                class="flex-shrink-0 w-10 h-10 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center mr-3">
                                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                                    <path
                                                        d="M6.25 6.375a4.125 4.125 0 1 1 8.25 0 4.125 4.125 0 0 1-8.25 0zM3.25 19.125a7.125 7.125 0 0 1 14.25 0v.003l-.001.119a.75.75 0 0 1-.363.63 13.067 13.067 0 0 1-6.761 1.873c-2.472 0-4.786-.684-6.76-1.873a.75.75 0 0 1-.364-.63l-.001-.122zM19.75 7.5a.75.75 0 0 0-1.5 0v2.25H16a.75.75 0 0 0 0 1.5h2.25v2.25a.75.75 0 0 0 1.5 0v-2.25H22a.75.75 0 0 0 0-1.5h-2.25V7.5z" />
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-sm text-gray-800 m-0 font-medium">User Baru: <span
                                                        class="font-bold">{{ $user->name }}</span></p>
                                                <p class="text-xs text-gray-500 m-0 mt-1">Perlu verifikasi akses
                                                    segera.</p>
                                            </div>
                                        </a>
                                    @endforeach
                                @else
                                    <div class="px-5 py-8 text-center flex flex-col items-center">
                                        <div
                                            class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center text-gray-400 mb-3">
                                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M9.143 17.082a24.248 24.248 0 0 0 3.857.38 24.243 24.243 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c.31.115.626.223.946.326z" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M12 21a3.001 3.001 0 0 0 2.83-2M3 3l18 18" />
                                            </svg>
                                        </div>
                                        <p class="text-sm text-gray-500 m-0">Tidak ada notifikasi baru</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="w-px h-8 bg-gray-200 hidden sm:block"></div>

                    {{-- Dropdown Profile --}}
                    <div class="relative" id="profileDropdown">
                        <button id="profileBtn"
                            class="flex items-center gap-3 p-1.5 pr-4 bg-white hover:bg-gray-50 border border-gray-100 hover:border-gray-200 rounded-full transition-all focus:outline-none group">
                            {{-- <img src="{{ asset('assets/images/faces/1.jpg') }}" alt="Profile"
                                class="w-9 h-9 rounded-full object-cover shadow-sm border border-white"> --}}
                            <div class="hidden md:block text-left">
                                <p class="text-sm font-bold text-gray-800 m-0 leading-tight">
                                    {{ Auth::user()->name ?? 'User' }}</p>
                                <p class="text-[11px] text-[#0d9488] m-0 font-bold uppercase tracking-wider">
                                    {{ Auth::user()->role == 'admin' ? 'Administrator' : 'Agen Properti' }}</p>
                            </div>
                            <svg class="w-3 h-3 text-gray-400 ml-1 group-hover:text-gray-600 transition-colors"
                                viewBox="0 0 24 24" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M12.53 16.28a.75.75 0 0 1-1.06 0l-7.5-7.5a.75.75 0 0 1 1.06-1.06L12 14.69l6.97-6.97a.75.75 0 1 1 1.06 1.06l-7.5 7.5z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>

                        <div id="profileMenu"
                            class="absolute right-0 mt-3 w-56 bg-white rounded-2xl shadow-xl border border-gray-100 opacity-0 invisible transform scale-95 transition-all duration-200 origin-top-right z-50 overflow-hidden">
                            <div class="px-5 py-4 border-b border-gray-50 bg-gray-50/50 md:hidden">
                                <p class="text-sm font-bold text-gray-800 m-0">{{ Auth::user()->name ?? 'User' }}</p>
                                <p class="text-xs text-[#0d9488] m-0 mt-0.5 font-bold">
                                    {{ Auth::user()->role == 'admin' ? 'Administrator' : 'Agen Properti' }}</p>
                            </div>

                            <div class="p-2">
                                <a href="#"
                                    onclick="event.preventDefault(); document.getElementById('logout-form-header').submit();"
                                    class="flex items-center px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 rounded-xl font-bold transition-colors no-underline">
                                    <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                                    </svg>
                                    Keluar Sistem
                                </a>
                            </div>
                        </div>
                    </div>
                    <form id="logout-form-header" action="{{ route('logout') }}" method="POST" class="hidden">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </header>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- LOGIKA DROPDOWN HEADER (Notifikasi & Profil) ---
            const notifBtn = document.getElementById('notifBtn');
            const notifMenu = document.getElementById('notifMenu');
            const profileBtn = document.getElementById('profileBtn');
            const profileMenu = document.getElementById('profileMenu');

            function toggleMenu(menuToShow, otherMenu) {
                const isHidden = menuToShow.classList.contains('opacity-0');

                // Tutup menu lain jika terbuka
                if (otherMenu) {
                    otherMenu.classList.remove('opacity-100', 'visible', 'scale-100');
                    otherMenu.classList.add('opacity-0', 'invisible', 'scale-95');
                }

                // Toggle menu yang diklik
                if (isHidden) {
                    menuToShow.classList.remove('opacity-0', 'invisible', 'scale-95');
                    menuToShow.classList.add('opacity-100', 'visible', 'scale-100');
                } else {
                    menuToShow.classList.remove('opacity-100', 'visible', 'scale-100');
                    menuToShow.classList.add('opacity-0', 'invisible', 'scale-95');
                }
            }

            if (notifBtn) notifBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                toggleMenu(notifMenu, profileMenu);
            });
            if (profileBtn) profileBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                toggleMenu(profileMenu, notifMenu);
            });

            // Klik di mana saja untuk menutup dropdown
            document.addEventListener('click', (e) => {
                if (notifMenu && !notifMenu.contains(e.target)) {
                    notifMenu.classList.remove('opacity-100', 'visible', 'scale-100');
                    notifMenu.classList.add('opacity-0', 'invisible', 'scale-95');
                }
                if (profileMenu && !profileMenu.contains(e.target)) {
                    profileMenu.classList.remove('opacity-100', 'visible', 'scale-100');
                    profileMenu.classList.add('opacity-0', 'invisible', 'scale-95');
                }
            });

        })
    </script>
@endif
