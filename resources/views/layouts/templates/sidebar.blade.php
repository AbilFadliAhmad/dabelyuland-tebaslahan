<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<div id="overlay"
    class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-20 hidden transition-opacity opacity-0 xl:hidden"></div>

<aside id="sidebar"
    class="fixed inset-y-0 left-0 z-50 w-72 bg-white border-r border-gray-100 transform -translate-x-full xl:translate-x-0 transition-transform duration-300 ease-out flex flex-col shadow-[4px_0_24px_rgba(0,0,0,0.02)] font-['Inter']">

    {{-- Sidebar Brand --}}
    <div class="flex items-center justify-between h-[72px] px-6 border-b border-gray-100 flex-shrink-0">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 no-underline group">
            <div
                class="w-10 h-10 bg-teal-50 rounded-xl flex items-center justify-center transition-transform duration-300 group-hover:scale-105">
                <img src="{{ asset('frontside/img/icon/logo-green.svg') }}" alt="Logo" class="w-6 h-6" />
            </div>
            <h4 class="text-[1.35rem] font-extrabold text-gray-900 m-0 tracking-tight">Dabelyu<span
                    class="text-[#0d9488]">land</span></h4>
        </a>
        <button id="closeSidebarBtn"
            class="xl:hidden text-gray-400 hover:text-red-500 bg-gray-50 hover:bg-red-50 p-2 rounded-lg transition-colors focus:outline-none">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    {{-- Sidebar Menu Area --}}
    <div id="sidebar-scrollable"
        class="flex-1 overflow-y-auto [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none] flex flex-col justify-between">

        <div class="py-6 px-4">
            <ul class="list-none p-0 m-0 flex flex-col gap-1.5">

                {{-- ==========================================
                     1. DASHBOARD
                     ========================================== --}}
                <li class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1 px-3">Main Menu</li>
                <li>
                    <a href="{{ route('dashboard') }}"
                        class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-[0.95rem] font-semibold transition-all duration-200 no-underline group
                        @if (Request::is('admin') || Request::is('admin/dashboard') || Request::is('user')) bg-[#0d9488] text-white shadow-[0_4px_12px_rgba(13,148,136,0.3)] @else text-gray-600 hover:bg-gray-50 hover:text-[#0d9488] @endif">
                        <svg class="w-5 h-5 @if (Request::is('admin') || Request::is('admin/dashboard') || Request::is('user')) text-white @else text-gray-400 group-hover:text-[#0d9488] @endif transition-colors"
                            viewBox="0 0 24 24" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M3 6a3 3 0 0 1 3-3h2.25a3 3 0 0 1 3 3v2.25a3 3 0 0 1-3 3H6a3 3 0 0 1-3-3V6zm9.75 0a3 3 0 0 1 3-3H18a3 3 0 0 1 3 3v2.25a3 3 0 0 1-3 3h-2.25a3 3 0 0 1-3-3V6zM3 15.75a3 3 0 0 1 3-3h2.25a3 3 0 0 1 3 3V18a3 3 0 0 1-3 3H6a3 3 0 0 1-3-3v-2.25zm9.75 0a3 3 0 0 1 3-3H18a3 3 0 0 1 3 3V18a3 3 0 0 1-3 3h-2.25a3 3 0 0 1-3-3v-2.25z"
                                clip-rule="evenodd" />
                        </svg>
                        <span>Dashboard</span>
                    </a>
                </li>

                {{-- ==========================================
                     2. MANAJEMEN PROPERTI 
                     ========================================== --}}
                <li class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1 mt-6 px-3">Database
                    Properti</li>
                <li>
                    <a href="{{ Auth::user()->role == 'admin' ? route('admin.buildings.index') : route('user.buildings.index') }}"
                        class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-[0.95rem] font-semibold transition-all duration-200 no-underline group
                        @if (Route::is('buildings.*') || Route::is('admin.buildings.*') || Route::is('user.buildings.*')) bg-[#0d9488] text-white shadow-[0_4px_12px_rgba(13,148,136,0.3)] @else text-gray-600 hover:bg-gray-50 hover:text-[#0d9488] @endif">
                        <svg class="w-5 h-5 @if (Route::is('buildings.*') || Route::is('admin.buildings.*') || Route::is('user.buildings.*')) text-white @else text-gray-400 group-hover:text-[#0d9488] @endif transition-colors"
                            viewBox="0 0 24 24" fill="currentColor">
                            <path
                                d="M11.47 3.841a.75.75 0 0 1 1.06 0l8.99 8.99a.75.75 0 1 1-1.06 1.06l-1.46-1.46V20.25a1.5 1.5 0 0 1-1.5 1.5h-4.5v-6a1.5 1.5 0 0 0-1.5-1.5h-3a1.5 1.5 0 0 0-1.5 1.5v6H5.5a1.5 1.5 0 0 1-1.5-1.5v-7.818L2.53 13.89a.75.75 0 0 1-1.06-1.06l8.99-8.99Z" />
                        </svg>
                        <span>Bangunan & Rumah</span>
                    </a>
                </li>
                <li>
                    <a href="{{ Auth::user()->role == 'admin' ? route('admin.lands.index') : route('user.lands.index') }}"
                        class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-[0.95rem] font-semibold transition-all duration-200 no-underline group
                        @if (Route::is('lands.*') || Route::is('admin.lands.*') || Route::is('user.lands.*')) bg-[#0d9488] text-white shadow-[0_4px_12px_rgba(13,148,136,0.3)] @else text-gray-600 hover:bg-gray-50 hover:text-[#0d9488] @endif">
                        <svg class="w-5 h-5 @if (Route::is('lands.*') || Route::is('admin.lands.*') || Route::is('user.lands.*')) text-white @else text-gray-400 group-hover:text-[#0d9488] @endif transition-colors"
                            viewBox="0 0 24 24" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M8.161 2.58a1.5 1.5 0 0 1 1.678 0l4.993 3.328a3 3 0 0 0 3.336 0l3.182-2.12a1.5 1.5 0 0 1 2.333 1.25v12.214a1.5 1.5 0 0 1-.661 1.25l-4.181 2.788a3 3 0 0 1-3.337 0l-4.993-3.328a3 3 0 0 0-3.336 0l-3.182 2.12A1.5 1.5 0 0 1 1.5 18.835V6.621a1.5 1.5 0 0 1 .661-1.25l4.181-2.788a3 3 0 0 1 1.819-.002ZM15 7.5a.75.75 0 0 1 .75.75v10.5a.75.75 0 0 1-1.5 0V8.25A.75.75 0 0 1 15 7.5Zm-6 0a.75.75 0 0 1 .75.75v10.5a.75.75 0 0 1-1.5 0V8.25A.75.75 0 0 1 9 7.5Z"
                                clip-rule="evenodd" />
                        </svg>
                        <span>Lahan & Tanah</span>
                    </a>
                </li>
                <li>
                    <a href="{{ Auth::user()->role == 'admin' ? route('admin.properties.trash') : route('user.properties.trash') }}"
                        class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-[0.95rem] font-semibold transition-all duration-200 no-underline group
                        @if (Auth::user()->role == 'admin' ? Route::is('admin.properties.trash') : Route::is('user.properties.trash')) bg-rose-500 text-white shadow-[0_4px_12px_rgba(225,29,72,0.3)] @else text-gray-600 hover:bg-rose-50 hover:text-rose-500 @endif">
                        <svg class="w-5 h-5 @if (Auth::user()->role == 'admin' ? Route::is('admin.properties.trash') : Route::is('user.properties.trash')) text-white @else text-gray-400 group-hover:text-rose-500 @endif transition-colors"
                            fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                        </svg>
                        <span>Tempat Sampah</span>
                    </a>
                </li>

                {{-- ==========================================
                     3. MANAJEMEN WEBSITE (HANYA ADMIN)
                     ========================================== --}}
                <li class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1 mt-6 px-3">Manajemen
                    Website</li>
                <li>
                    <a href="{{ route('banner.index') }}"
                        class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-[0.95rem] font-semibold transition-all duration-200 no-underline group
                        @if (Route::is('banner.*')) bg-[#0d9488] text-white shadow-[0_4px_12px_rgba(13,148,136,0.3)] @else text-gray-600 hover:bg-gray-50 hover:text-[#0d9488] @endif">
                        <svg class="w-5 h-5 @if (Route::is('banner.*')) text-white @else text-gray-400 group-hover:text-[#0d9488] @endif transition-colors"
                            viewBox="0 0 24 24" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M1.5 6a2.25 2.25 0 0 1 2.25-2.25h16.5A2.25 2.25 0 0 1 22.5 6v12a2.25 2.25 0 0 1-2.25 2.25H3.75A2.25 2.25 0 0 1 1.5 18V6ZM3 16.06V18c0 .414.336.75.75.75h16.5A.75.75 0 0 0 21 18v-1.94l-2.69-2.689a1.5 1.5 0 0 0-2.12 0l-.88.879.97.97a.75.75 0 1 1-1.06 1.06l-5.16-5.159a1.5 1.5 0 0 0-2.12 0L3 16.061Zm10.125-7.81a1.125 1.125 0 1 1 2.25 0 1.125 1.125 0 0 1-2.25 0Z"
                                clip-rule="evenodd" />
                        </svg>
                        <span>Hero Banner</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.highlight.index') }}"
                        class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-[0.95rem] font-semibold transition-all duration-200 no-underline group
                        @if (Route::is('admin.highlight.*')) bg-[#0d9488] text-white shadow-[0_4px_12px_rgba(13,148,136,0.3)] @else text-gray-600 hover:bg-gray-50 hover:text-[#0d9488] @endif">
                        <svg class="w-5 h-5 @if (Route::is('admin.highlight.*')) text-white @else text-gray-400 group-hover:text-[#0d9488] @endif transition-colors"
                            viewBox="0 0 24 24" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zM12.75 6a.75.75 0 00-1.5 0v6c0 .414.336.75.75.75h4.5a.75.75 0 000-1.5h-3.75V6z"
                                clip-rule="evenodd" />
                        </svg>
                        <span>Highlight & Rekomendasi</span>
                    </a>
                </li>
                @if (Auth::check() && Auth::user()->role == 'admin')
                    <li>
                        <a href="{{ route('portofolios.index') }}"
                            class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-[0.95rem] font-semibold transition-all duration-200 no-underline group
                        @if (Route::is('portofolios.*')) bg-[#0d9488] text-white shadow-[0_4px_12px_rgba(13,148,136,0.3)] @else text-gray-600 hover:bg-gray-50 hover:text-[#0d9488] @endif">
                            <svg class="w-5 h-5 @if (Route::is('portofolios.*')) text-white @else text-gray-400 group-hover:text-[#0d9488] @endif transition-colors"
                                viewBox="0 0 24 24" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M7.5 5.25a3 3 0 0 1 3-3h3a3 3 0 0 1 3 3v.252a5.25 5.25 0 0 1 5.25 5.25v7.5a5.25 5.25 0 0 1-5.25 5.25h-15a5.25 5.25 0 0 1-5.25-5.25v-7.5a5.25 5.25 0 0 1 5.25-5.25V5.25Zm1.5 0v.252A5.215 5.215 0 0 1 12 5.25c1.026 0 1.984.293 2.802.801V5.25a1.5 1.5 0 0 0-1.5-1.5h-3a1.5 1.5 0 0 0-1.5 1.5ZM1.5 10.5v7.5a3.75 3.75 0 0 0 3.75 3.75h15a3.75 3.75 0 0 0 3.75-3.75v-7.5a3.75 3.75 0 0 0-3.75-3.75h-15a3.75 3.75 0 0 0-3.75 3.75Zm15 0a.75.75 0 0 1 .75.75v3a.75.75 0 0 1-1.5 0v-3a.75.75 0 0 1 .75-.75Zm-10.5 0a.75.75 0 0 1 .75.75v3a.75.75 0 0 1-1.5 0v-3a.75.75 0 0 1 .75-.75Z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span>Portofolio Pekerjaan</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('testimonis.index') }}"
                            class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-[0.95rem] font-semibold transition-all duration-200 no-underline group
                        @if (Route::is('testimonis.*')) bg-[#0d9488] text-white shadow-[0_4px_12px_rgba(13,148,136,0.3)] @else text-gray-600 hover:bg-gray-50 hover:text-[#0d9488] @endif">
                            <svg class="w-5 h-5 @if (Route::is('testimonis.*')) text-white @else text-gray-400 group-hover:text-[#0d9488] @endif transition-colors"
                                viewBox="0 0 24 24" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M4.804 21.644A6.707 6.707 0 0 0 6 21.75a6.721 6.721 0 0 0 3.583-1.029c.774.182 1.584.279 2.417.279 5.322 0 9.75-3.97 9.75-9 0-5.03-4.428-9-9.75-9s-9.75 3.97-9.75 9c0 2.409 1.025 4.587 2.674 6.192.232.226.277.428.254.543a3.73 3.73 0 0 1-.814 1.686.75.75 0 0 0 .44 1.223ZM8.25 10.875a1.125 1.125 0 1 0 0 2.25 1.125 1.125 0 0 0 0-2.25ZM10.875 12a1.125 1.125 0 1 1 2.25 0 1.125 1.125 0 0 1-2.25 0Zm4.875-1.125a1.125 1.125 0 1 0 0 2.25 1.125 1.125 0 0 0 0-2.25Z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span>Testimoni Klien</span>
                        </a>
                    </li>
                @endif

                {{-- ==========================================
                     4. PENGGUNA & LAYANAN (HANYA ADMIN)
                     ========================================== --}}
                @if (Auth::check() && Auth::user()->role == 'admin')
                    <li class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1 mt-6 px-3">Pengguna &
                        Layanan</li>
                    <li>
                        <a href="{{ route('admin.list.users') }}"
                            class="flex items-center justify-between px-4 py-2.5 rounded-xl text-[0.95rem] font-semibold transition-all duration-200 no-underline group
                        @if (Route::is('admin.list.users')) bg-[#0d9488] text-white shadow-[0_4px_12px_rgba(13,148,136,0.3)] @else text-gray-600 hover:bg-gray-50 hover:text-[#0d9488] @endif">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 @if (Route::is('admin.list.users')) text-white @else text-gray-400 group-hover:text-[#0d9488] @endif transition-colors"
                                    viewBox="0 0 24 24" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M8.25 6.75a3.75 3.75 0 1 1 7.5 0 3.75 3.75 0 0 1-7.5 0ZM15.75 9.75a3 3 0 1 1 6 0 3 3 0 0 1-6 0ZM2.25 9.75a3 3 0 1 1 6 0 3 3 0 0 1-6 0ZM6.31 15.117A6.745 6.745 0 0 1 12 12a6.745 6.745 0 0 1 5.69 3.117.75.75 0 0 0 .532.365c1.026.155 2.012.43 2.946.81a.75.75 0 0 1 .45 1.054A5.98 5.98 0 0 1 16.5 21h-9a5.98 5.98 0 0 1-5.118-3.654.75.75 0 0 1 .45-1.054c.934-.38 1.92-.655 2.946-.81a.75.75 0 0 0 .532-.365ZM1.95 14.887a.75.75 0 0 1 .986.37 5.975 5.975 0 0 0 1.95 2.115.75.75 0 0 1-.806 1.233A7.472 7.472 0 0 1 1.07 15.8a.75.75 0 0 1 .88-.913ZM22.05 14.887a.75.75 0 0 0-.986.37 5.975 5.975 0 0 1-1.95 2.115.75.75 0 0 0 .806 1.233 7.472 7.472 0 0 0 3.01-2.81.75.75 0 0 0-.88-.913Z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span>Agen Pelanggan</span>
                            </div>
                            @if (isset($pendingUsers) && $pendingUsers->count() > 0)
                                <span
                                    class="bg-red-100 text-red-600 px-2.5 py-0.5 rounded-md text-[11px] font-bold">{{ $pendingUsers->count() }}</span>
                            @endif
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.membership.index') }}"
                            class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-[0.95rem] font-semibold transition-all duration-200 no-underline group
                        @if (Route::is('admin.membership.index')) bg-[#0d9488] text-white shadow-[0_4px_12px_rgba(13,148,136,0.3)] @else text-gray-600 hover:bg-gray-50 hover:text-[#0d9488] @endif">
                            <svg class="w-5 h-5 @if (Route::is('admin.membership.index')) text-white @else text-gray-400 group-hover:text-[#0d9488] @endif transition-colors"
                                viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 7.5a2.25 2.25 0 1 0 0 4.5 2.25 2.25 0 0 0 0-4.5Z" />
                                <path fill-rule="evenodd"
                                    d="M1.5 4.875C1.5 3.839 2.34 3 3.375 3h17.25c1.035 0 1.875.84 1.875 1.875v9.75c0 1.036-.84 1.875-1.875 1.875H3.375A1.875 1.875 0 0 1 1.5 14.625v-9.75ZM8.25 9.75a3.75 3.75 0 1 1 7.5 0 3.75 3.75 0 0 1-7.5 0ZM18.75 9a.75.75 0 0 0-.75.75v.008c0 .414.336.75.75.75h.008a.75.75 0 0 0 .75-.75V9.75a.75.75 0 0 0-.75-.75h-.008ZM4.5 9.75A.75.75 0 0 1 5.25 9h.008a.75.75 0 0 1 .75.75v.008a.75.75 0 0 1-.75.75H5.25a.75.75 0 0 1-.75-.75V9.75Z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span>Paket Membership</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.koin.index') }}"
                            class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-[0.95rem] font-semibold transition-all duration-200 no-underline group
                                @if (Route::is('admin.koin.*')) bg-[#0d9488] text-white shadow-[0_4px_12px_rgba(13,148,136,0.3)] @else text-gray-600 hover:bg-gray-50 hover:text-[#0d9488] @endif">
                            <i
                                class="fas fa-coins w-5 text-center text-lg @if (Route::is('admin.koin.*')) text-white @else text-gray-400 group-hover:text-[#0d9488] @endif transition-colors"></i>
                            <span>Manajemen Paket Koin</span>
                        </a>
                    </li>
                @endif
                {{-- MENU KOIN KHUSUS AGEN --}}
                @if (Auth::check() && Auth::user()->role !== 'admin')
                    <li class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1 mt-6 px-3">Layanan
                        Koin</li>
                    <li>
                        <a href="{{ route('user.topup.index') }}"
                            class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-[0.95rem] font-semibold transition-all duration-200 no-underline group
                                @if (Request::is('user.topup*') || Request::is('user/koin*')) bg-amber-500 text-white shadow-[0_4px_12px_rgba(245,158,11,0.3)] @else text-gray-600 hover:bg-amber-50 hover:text-amber-600 @endif">
                            <i
                                class="fas fa-coins w-5 text-center text-lg text-amber-400 group-hover:text-amber-500  transition-colors"></i>
                            <span>Dabelyu Koin</span>
                        </a>
                    </li>
                @endif

                {{-- ==========================================
                     5. DATA & AUDIT (HANYA ADMIN)
                     ========================================== --}}
                @if (Auth::check() && Auth::user()->role == 'admin')
                    <li class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1 mt-6 px-3">Data &
                        Laporan</li>
                    <li>
                        <a href="{{ route('report') }}"
                            class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-[0.95rem] font-semibold transition-all duration-200 no-underline group
                        @if (Route::is('report')) bg-[#0d9488] text-white shadow-[0_4px_12px_rgba(13,148,136,0.3)] @else text-gray-600 hover:bg-gray-50 hover:text-[#0d9488] @endif">
                            <svg class="w-5 h-5 @if (Route::is('report')) text-white @else text-gray-400 group-hover:text-[#0d9488] @endif transition-colors"
                                viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M5.625 1.5c-1.036 0-1.875.84-1.875 1.875v17.25c0 1.035.84 1.875 1.875 1.875h12.75c1.035 0 1.875-.84 1.875-1.875V12.75A3.75 3.75 0 0 0 16.5 9h-1.875a1.875 1.875 0 0 1-1.875-1.875V5.25A3.75 3.75 0 0 0 9 1.5H5.625ZM7.5 15a.75.75 0 0 1 .75-.75h7.5a.75.75 0 0 1 0 1.5h-7.5A.75.75 0 0 1 7.5 15Zm.75 2.25a.75.75 0 0 0 0 1.5H12a.75.75 0 0 0 0-1.5H8.25Z" />
                                <path
                                    d="M12.971 1.816A5.23 5.23 0 0 1 14.25 5.25v1.875c0 .207.168.375.375.375H16.5a5.23 5.23 0 0 1 3.434 1.279 9.768 9.768 0 0 0-6.963-6.963Z" />
                            </svg>
                            <span>Laporan Analytics</span>
                        </a>
                    </li>
                    {{-- <li>
                        <a href="{{ route('admin.audit.index') }}"
                            class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-[0.95rem] font-semibold transition-all duration-200 no-underline group
                        @if (Route::is('admin.audit.*')) bg-[#0d9488] text-white shadow-[0_4px_12px_rgba(13,148,136,0.3)] @else text-gray-600 hover:bg-gray-50 hover:text-[#0d9488] @endif">
                            <svg class="w-5 h-5 @if (Route::is('admin.audit.*')) text-white @else text-gray-400 group-hover:text-[#0d9488] @endif transition-colors"
                                fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                            <span>Audit & Log Aktivitas</span>
                        </a>
                    </li> --}}
                    <li>
                        <a href="{{ route('contacts.index') }}"
                            class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-[0.95rem] font-semibold transition-all duration-200 no-underline group
                        @if (Route::is('contacts.*')) bg-[#0d9488] text-white shadow-[0_4px_12px_rgba(13,148,136,0.3)] @else text-gray-600 hover:bg-gray-50 hover:text-[#0d9488] @endif">
                            <svg class="w-5 h-5 @if (Route::is('contacts.*')) text-white @else text-gray-400 group-hover:text-[#0d9488] @endif transition-colors"
                                viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M1.5 8.67v8.58a3 3 0 0 0 3 3h15a3 3 0 0 0 3-3V8.67l-8.928 5.493a3 3 0 0 1-3.144 0L1.5 8.67Z" />
                                <path
                                    d="M22.5 6.908V6.75a3 3 0 0 0-3-3h-15a3 3 0 0 0-3-3v.158l9.714 5.978a1.5 1.5 0 0 0 1.572 0L22.5 6.908Z" />
                            </svg>
                            <span>Kontak Karyawan </span>
                        </a>
                    </li>
                @endif

                {{-- ==========================================
                     6. PENGATURAN AKUN (ADMIN)
                     ========================================== --}}
                {{-- @if (Auth::check() && Auth::user()->role == 'admin')
                    <li class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1 mt-6 px-3">Pengaturan
                        Akses</li>
                    <li>
                        <a href="{{ route('user') }}"
                            class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-[0.95rem] font-semibold transition-all duration-200 no-underline group
                        @if (Route::is('user.*') || Route::is('user')) bg-[#0d9488] text-white shadow-[0_4px_12px_rgba(13,148,136,0.3)] @else text-gray-600 hover:bg-gray-50 hover:text-[#0d9488] @endif">
                            <svg class="w-5 h-5 @if (Route::is('user.*') || Route::is('user')) text-white @else text-gray-400 group-hover:text-[#0d9488] @endif transition-colors"
                                viewBox="0 0 24 24" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M8.25 6.75a3.75 3.75 0 1 1 7.5 0 3.75 3.75 0 0 1-7.5 0ZM15.75 9.75a3 3 0 1 1 6 0 3 3 0 0 1-6 0ZM2.25 9.75a3 3 0 1 1 6 0 3 3 0 0 1-6 0ZM6.31 15.117A6.745 6.745 0 0 1 12 12a6.745 6.745 0 0 1 5.69 3.117.75.75 0 0 0 .532.365c1.026.155 2.012.43 2.946.81a.75.75 0 0 1 .45 1.054A5.98 5.98 0 0 1 16.5 21h-9a5.98 5.98 0 0 1-5.118-3.654.75.75 0 0 1 .45-1.054c.934-.38 1.92-.655 2.946-.81a.75.75 0 0 0 .532-.365ZM1.95 14.887a.75.75 0 0 1 .986.37 5.975 5.975 0 0 0 1.95 2.115.75.75 0 0 1-.806 1.233A7.472 7.472 0 0 1 1.07 15.8a.75.75 0 0 1 .88-.913ZM22.05 14.887a.75.75 0 0 0-.986.37 5.975 5.975 0 0 1-1.95 2.115.75.75 0 0 0 .806 1.233 7.472 7.472 0 0 0 3.01-2.81.75.75 0 0 0-.88-.913Z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span>Daftar Hak Akses Admin</span>
                        </a>
                    </li>
                @endif --}}
            </ul>
        </div>

        {{-- Card Upgrade Premium (Hanya untuk User Biasa) --}}
        @if (Auth::check() && Auth::user()->role !== 'admin')
            <div class="px-4 mb-6 mt-8">
                <div
                    class="bg-gradient-to-br from-amber-100 to-amber-50 rounded-2xl p-4 border border-amber-200 relative overflow-hidden group hover:shadow-md transition-all">
                    <div class="absolute -right-4 -top-4 text-amber-200/50 group-hover:scale-110 transition-transform">
                        <i class="fas fa-gem text-6xl"></i>
                    </div>
                    <h6 class="text-sm font-extrabold text-amber-900 mb-1 relative z-10">Tingkatkan Bisnismu!</h6>
                    <p class="text-[11px] text-amber-700 mb-3 leading-tight relative z-10">Dapatkan fitur upload tanpa
                        batas & highlight properti.</p>
                    <a href="{{ route('user.membership.index') ?? '#' }}"
                        class="block w-full py-2 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold text-center rounded-lg transition-colors shadow-sm no-underline relative z-10">
                        Lihat Paket Premium
                    </a>
                </div>
            </div>
        @endif

    </div>

    {{-- Sidebar Footer (Lihat Website & Logout) --}}
    <div class="p-4 border-t border-gray-50 flex-shrink-0 bg-white">
        <a href="{{ route('home') }}" target="_blank"
            class="flex items-center justify-center gap-2 w-full px-4 py-3 mb-3 rounded-xl text-sm font-bold text-[#0d9488] bg-teal-50 hover:bg-[#0d9488] hover:text-white transition-all duration-200 no-underline group">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                <path fill-rule="evenodd"
                    d="M15.75 2.25H21a.75.75 0 0 1 .75.75v5.25a.75.75 0 0 1-1.5 0V4.81L8.03 17.03a.75.75 0 0 1-1.06-1.06L19.19 3.75h-3.44a.75.75 0 0 1 0-1.5Zm-10.5 4.5a1.5 1.5 0 0 0-1.5 1.5v10.5a1.5 1.5 0 0 0 1.5 1.5h10.5a1.5 1.5 0 0 0 1.5-1.5V10.5a.75.75 0 0 1 1.5 0v8.25a3 3 0 0 1-3 3H5.25a3 3 0 0 1-3-3V8.25a3 3 0 0 1 3-3h8.25a.75.75 0 0 1 0 1.5H5.25Z"
                    clip-rule="evenodd" />
            </svg>
            <span>Lihat Website Utama</span>
        </a>
        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form-sidebar').submit();"
            class="flex items-center justify-center gap-2 w-full px-4 py-2 rounded-xl text-sm font-bold text-gray-500 hover:bg-red-50 hover:text-red-600 transition-all duration-200 no-underline group">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                <path fill-rule="evenodd"
                    d="M12 2.25a.75.75 0 0 1 .75.75v9a.75.75 0 0 1-1.5 0V3a.75.75 0 0 1 .75-.75ZM6.166 5.106a.75.75 0 0 1 0 1.06 8.25 8.25 0 1 0 11.668 0 .75.75 0 1 1 1.06-1.06c3.808 3.807 3.808 9.98 0 13.788-3.807 3.808-9.98 3.808-13.788 0-3.808-3.807-3.808-9.98 0-13.788a.75.75 0 0 1 1.06 0Z"
                    clip-rule="evenodd" />
            </svg>
            <span>Keluar Sistem</span>
        </a>
    </div>
    <form id="logout-form-sidebar" action="{{ route('logout') }}" method="POST" class="hidden">
        @csrf
    </form>

</aside>

{{-- =========================================================
     3. SCRIPT INTERAKSI (Pure JS)
     ========================================================= --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {

        // --- LOGIKA SIDEBAR MOBILE TOGGLE ---
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        const openBtn = document.getElementById('mobileMenuBtn');
        const closeBtn = document.getElementById('closeSidebarBtn');

        function openSidebar() {
            sidebar.classList.remove('-translate-x-full');
            sidebar.classList.add('translate-x-0');
            overlay.classList.remove('hidden');
            setTimeout(() => overlay.classList.remove('opacity-0'), 10);
        }

        function closeSidebar() {
            sidebar.classList.remove('translate-x-0');
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('opacity-0');
            setTimeout(() => overlay.classList.add('hidden'), 300); // Tunggu animasi fade selesai
        }

        if (openBtn) openBtn.addEventListener('click', openSidebar);
        if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
        if (overlay) overlay.addEventListener('click', closeSidebar);
    });
</script>
<script>
    (function() {
        const sidebar = document.getElementById('sidebar-scrollable');
        if (sidebar) {
            const savedPos = sessionStorage.getItem('sidebarScrollPosition');
            if (savedPos) sidebar.scrollTop = parseInt(savedPos, 10);

            sidebar.addEventListener('scroll', function() {
                sessionStorage.setItem('sidebarScrollPosition', sidebar.scrollTop);
            }, {
                passive: true
            });
        }
    })();
</script>
