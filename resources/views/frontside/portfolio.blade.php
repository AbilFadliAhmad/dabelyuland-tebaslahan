@extends('layouts.index')

@section('styles')
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        .custom-shadow {
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.03);
        }

        .hover-lift {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .hover-lift:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(13, 148, 136, 0.1);
        }

        .font-heading {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .font-body {
            font-family: 'Inter', sans-serif;
        }

        /* Animasi smooth untuk filter grid */
        .portfolio-item {
            transition: opacity 0.4s ease-out, transform 0.4s ease-out;
        }

        .portfolio-hidden {
            opacity: 0 !important;
            transform: scale(0.95) !important;
            position: absolute !important;
            visibility: hidden !important;
            pointer-events: none !important;
        }
    </style>
@endsection

@section('content')
    <div class="bg-[#FAFAFA] font-body min-h-screen pb-20">

        {{-- ==========================================================
             1. HERO SECTION
             ========================================================== --}}
        <div
            class="relative w-full min-h-[500px] md:min-h-[00px] lg:min-h-[630px] bg-gray-900 flex items-center justify-center overflow-hidden">
            {{-- Background Image --}}
            <div class="absolute inset-0 z-0">
                <img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?ixlib=rb-4.0.3&auto=format&fit=crop&w=1470&q=80"
                    class="w-full h-full object-cover opacity-40" alt="Hero Background">
                <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/60 to-transparent"></div>
            </div>

            {{-- Content --}}
            <div class="relative z-10 text-center px-4 max-w-4xl mx-auto mt-10">
                <span
                    class="inline-block py-1.5 px-5 rounded-full bg-[#0d9488]/20 border border-[#0d9488]/50 text-white text-[10px] font-bold tracking-[0.2em] uppercase mb-6 backdrop-blur-md">
                    Our Masterpiece
                </span>
                <h1
                    class="font-heading text-4xl md:text-6xl lg:text-7xl font-extrabold text-white leading-tight tracking-tight mb-6 drop-shadow-lg">
                    Crafting Spaces, <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-teal-300 to-[#0d9488] pr-2">Defining
                        Legacies.</span>
                </h1>
                <p
                    class="text-gray-300 text-sm md:text-lg font-light mb-10 leading-relaxed max-w-2xl mx-auto hidden md:block">
                    Kami tidak hanya membangun properti, kami menciptakan warisan. Dari desain arsitektur hingga konstruksi
                    fisik di seluruh nusantara.
                </p>
                <div>
                    <a href="#portfolio-grid"
                        class="inline-block px-8 py-3.5 bg-[#0d9488] text-white font-bold text-sm rounded-full shadow-lg shadow-[#0d9488]/40 transition-all duration-300 transform hover:-translate-y-1 hover:bg-white hover:text-[#0d9488] hover:shadow-white/30 no-underline">
                        Lihat Karya Kami
                    </a>
                </div>
            </div>
        </div>

        {{-- ==========================================================
             2. STATS SECTION (Floating Style)
             ========================================================== --}}
        <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-20 -mt-12 md:-mt-16 mb-20">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-5xl mx-auto">

                <div
                    class="bg-white rounded-3xl p-6 md:p-8 custom-shadow border border-gray-50 transition-all duration-300 hover:-translate-y-2">
                    <div class="flex items-center gap-5">
                        <div
                            class="w-14 h-14 md:w-16 md:h-16 rounded-2xl bg-[#F0F7F7] flex items-center justify-center text-[#0d9488] text-2xl shrink-0">
                            <i class="fas fa-home"></i>
                        </div>
                        <div>
                            <h3 class="font-heading text-3xl md:text-4xl font-extrabold text-gray-900 tracking-tight m-0">
                                200+</h3>
                            <p
                                class="text-[10px] md:text-[11px] text-gray-500 font-bold uppercase tracking-widest mt-1 m-0">
                                Property Sold</p>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white rounded-3xl p-6 md:p-8 custom-shadow border border-gray-50 transition-all duration-300 hover:-translate-y-2">
                    <div class="flex items-center gap-5">
                        <div
                            class="w-14 h-14 md:w-16 md:h-16 rounded-2xl bg-[#F0F7F7] flex items-center justify-center text-[#0d9488] text-2xl shrink-0">
                            <i class="fas fa-drafting-compass"></i>
                        </div>
                        <div>
                            <h3 class="font-heading text-3xl md:text-4xl font-extrabold text-gray-900 tracking-tight m-0">
                                250+</h3>
                            <p
                                class="text-[10px] md:text-[11px] text-gray-500 font-bold uppercase tracking-widest mt-1 m-0">
                                Designs Created</p>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white rounded-3xl p-6 md:p-8 custom-shadow border border-gray-50 transition-all duration-300 hover:-translate-y-2">
                    <div class="flex items-center gap-5">
                        <div
                            class="w-14 h-14 md:w-16 md:h-16 rounded-2xl bg-[#F0F7F7] flex items-center justify-center text-[#0d9488] text-2xl shrink-0">
                            <i class="fas fa-hard-hat"></i>
                        </div>
                        <div>
                            <h3 class="font-heading text-3xl md:text-4xl font-extrabold text-gray-900 tracking-tight m-0">
                                60+</h3>
                            <p
                                class="text-[10px] md:text-[11px] text-gray-500 font-bold uppercase tracking-widest mt-1 m-0">
                                Projects Built</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- ==========================================================
             3. 4 PILAR & PORTFOLIO GRID
             ========================================================== --}}
        <div id="portfolio-grid" class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-32">

            {{-- Header Pilar --}}
            <div class="text-center mb-16 max-w-4xl mx-auto">
                <h2 class="font-heading text-3xl md:text-4xl font-extrabold text-gray-900 tracking-tight mb-6">
                    Karya & Proyek Unggulan Kami
                </h2>
                <p class="text-gray-500 leading-relaxed font-body text-sm md:text-base">
                    Kami menampilkan beragam proyek unggulan mulai dari desain hunian, pembangunan rumah, hingga renovasi
                    properti—sebagai wujud komitmen kami terhadap kualitas, estetika, dan kepuasan klien. Seluruh karya kami
                    berlandaskan pada 4 pilar utama.
                </p>
            </div>

            {{-- Grid 4 Pilar --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 text-center mb-20 max-w-6xl mx-auto">

                {{-- Pilar 1: Konten Kreator --}}
                <div
                    class="flex flex-col items-center p-6 rounded-3xl hover:bg-white hover:shadow-[0_8px_30px_rgb(0,0,0,0.04)] transition-all duration-300">
                    <svg class="w-14 h-14 mb-5 text-[#0d9488]" viewBox="0 0 512 512" fill="currentColor"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M448 32H64C28.7 32 0 60.7 0 96v320c0 35.3 28.7 64 64 64h384c35.3 0 64-28.7 64-64V96c0-35.3-28.7-64-64-64zm-16 64l-42.7 64H320l42.7-64H432zm-128 0l-42.7 64H192l42.7-64h69.3zm-128 0l-42.7 64H64V96h112zM64 416V224h384v192H64zm156.5-45.7l116.6-67.9c5.6-3.3 5.6-11.5 0-14.8l-116.6-67.9c-5.7-3.3-12.8 .8-12.8 7.4v135.8c0 6.6 7.1 10.7 12.8 7.4z" />
                    </svg>
                    <h3 class="font-heading text-[1.1rem] font-bold text-gray-800 mb-2">Konten Kreator</h3>
                    <p class="text-[0.9rem] text-gray-500 font-body leading-relaxed m-0">Adsense, endorse, affiliate,
                        pembuatan konten properti.</p>
                </div>

                {{-- Pilar 2: Design & Build --}}
                <div
                    class="flex flex-col items-center p-6 rounded-3xl hover:bg-white hover:shadow-[0_8px_30px_rgb(0,0,0,0.04)] transition-all duration-300">
                    <svg class="w-14 h-14 mb-5 text-[#0d9488]" viewBox="0 0 512 512" fill="currentColor"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M256 32c12.5 0 24.1 6.4 30.8 17l40.5 64H416c17.7 0 32 14.3 32 32v272h32c8.8 0 16 7.2 16 16s-7.2 16-16 16H32c-8.8 0-16-7.2-16-16s7.2-16 16-16h32V145c0-17.7 14.3-32 32-32h88.7l40.5-64C231.9 38.4 243.5 32 256 32zM128 192v32c0 8.8 7.2 16 16 16h32c8.8 0 16-7.2 16-16v-32c0-8.8-7.2-16-16-16h-32c-8.8 0-16 7.2-16 16zm0 96v32c0 8.8 7.2 16 16 16h32c8.8 0 16-7.2 16-16v-32c0-8.8-7.2-16-16-16h-32c-8.8 0-16 7.2-16 16zm64 80v48h128v-48c0-17.7-14.3-32-32-32h-64c-17.7 0-32 14.3-32 32zm128-96v32c0 8.8 7.2 16 16 16h32c8.8 0 16-7.2 16-16v-32c0-8.8-7.2-16-16-16h-32c-8.8 0-16 7.2-16 16zm0-96v32c0 8.8 7.2 16 16 16h32c8.8 0 16-7.2 16-16v-32c0-8.8-7.2-16-16-16h-32c-8.8 0-16 7.2-16 16zM286.6 66.4l-19.1-30.2L248.4 66.4 213.6 117H298.4l-11.8-50.6z" />
                    </svg>
                    <h3 class="font-heading text-[1.1rem] font-bold text-gray-800 mb-2">Design & Build</h3>
                    <p class="text-[0.9rem] text-gray-500 font-body leading-relaxed m-0">Desain 3D kustom, arsitektur, dan
                        eksekusi pembangunan.</p>
                </div>

                {{-- Pilar 3: Developer & Agency --}}
                <div
                    class="flex flex-col items-center p-6 rounded-3xl hover:bg-white hover:shadow-[0_8px_30px_rgb(0,0,0,0.04)] transition-all duration-300">
                    <svg class="w-14 h-14 mb-5 text-[#0d9488]" viewBox="0 0 512 512" fill="currentColor"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M213.3 27.7c10.4-11.4 27.7-14.2 41.5-6.7l160 85.3c10.4 5.5 17 16.5 17 28.5V266h32.2c16.3 0 31.4 8.5 39.9 22.6s9.4 31.9 2.5 46.8l-40 85.3c-14.8 31.5-46.7 51.3-81.5 51.3H102.6c-27.1 0-53-11.1-71.8-30.7l-15.6-16.3c-11.4-11.9-10.7-30.9 1.5-41.9s31.1-10.4 41.9 1.5l15.6 16.3c8.1 8.5 19.3 13.2 31.1 13.2h282.4c15 0 28.8-8.6 35.1-22l40-85.3c1.5-3.2 1.3-7-.5-10s-5.1-4.9-8.7-4.9H224c-17.7 0-32-14.3-32-32s14.3-32 32-32H400V152.1L256 75.3l-144 76.8V288c0 17.7-14.3 32-32 32s-32-14.3-32-32V134.8c0-12.1 6.6-23.1 17-28.5l148.3-78.6z" />
                    </svg>
                    <h3 class="font-heading text-[1.1rem] font-bold text-gray-800 mb-2">Developer & Agency</h3>
                    <p class="text-[0.9rem] text-gray-500 font-body leading-relaxed m-0">Jual beli properti, titip jual, dan
                        pengelolaan kawasan terpadu.</p>
                </div>

                {{-- Pilar 4: Investment --}}
                <div
                    class="flex flex-col items-center p-6 rounded-3xl hover:bg-white hover:shadow-[0_8px_30px_rgb(0,0,0,0.04)] transition-all duration-300">
                    <svg class="w-14 h-14 mb-5 text-[#0d9488]" viewBox="0 0 512 512" fill="currentColor"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M400 32H112C85.5 32 64 53.5 64 80v352c0 26.5 21.5 48 48 48h288c26.5 0 48-21.5 48-48V80c0-26.5-21.5-48-48-48zm-144 64c26.5 0 48 21.5 48 48s-21.5 48-48 48-48-21.5-48-48 21.5-48 48-48zm80 208v16c0 17.7-14.3 32-32 32H208c-17.7 0-32-14.3-32-32v-16c0-44.2 35.8-80 80-80h32c44.2 0 80 35.8 80 80zM224 144c0 17.7-14.3 32-32 32s-32-14.3-32-32 14.3-32 32-32 32 14.3 32 32zm0 256c0 17.7-14.3 32-32 32s-32-14.3-32-32 14.3-32 32-32 32 14.3 32 32z" />
                        <path
                            d="M256 128a32 32 0 1 0 0-64 32 32 0 1 0 0 64zm-96 248c0-35.3 28.7-64 64-64h64c35.3 0 64 28.7 64 64v16c0 8.8-7.2 16-16 16H176c-8.8 0-16-7.2-16-16v-16z"
                            opacity="0.4" />
                        <path
                            d="M501.6 352.5c-4-11.8-15.1-19.4-27.5-19.4H425c-8 0-15 4.3-19 11.2l-37.5 64.9-63.5-36.6c-4.4-2.5-9.6-3.9-14.9-3.9H224.2c-17.7 0-32 14.3-32 32s14.3 32 32 32h60.4l82 47.3c15.6 9 34.6 9 50.2 0l113.6-65.6c13.7-7.9 20.3-23.7 15.6-39.2l-14.4-42.7z" />
                    </svg>
                    <h3 class="font-heading text-[1.1rem] font-bold text-gray-800 mb-2">Investment</h3>
                    <p class="text-[0.9rem] text-gray-500 font-body leading-relaxed m-0">Kerjasama Investasi bisnis cerdas
                        bidang properti.</p>
                </div>

            </div>

            {{-- UPGRADED: Filter Pills (Modern Capsule Style) --}}
            <div class="flex justify-center mb-12">
                <div class="inline-flex bg-white p-1.5 rounded-full shadow-sm border border-gray-200 filter-group">
                    <button data-filter="all"
                        class="filter-btn active relative px-6 py-2.5 rounded-full text-sm font-bold text-white transition-colors duration-300 z-10 focus:outline-none">
                        <span class="relative z-10">Semua Proyek</span>
                        {{-- Background Highlight --}}
                        <div
                            class="filter-bg absolute inset-0 bg-[#0d9488] rounded-full shadow-md -z-10 transition-all duration-300 opacity-100 scale-100">
                        </div>
                    </button>

                    <button data-filter="design"
                        class="filter-btn relative px-6 py-2.5 rounded-full text-sm font-bold text-gray-500 hover:text-gray-800 transition-colors duration-300 z-10 focus:outline-none">
                        <span class="relative z-10">Desain (3D)</span>
                        <div
                            class="filter-bg absolute inset-0 bg-[#0d9488] rounded-full shadow-md -z-10 transition-all duration-300 opacity-0 scale-95">
                        </div>
                    </button>

                    <button data-filter="build"
                        class="filter-btn relative px-6 py-2.5 rounded-full text-sm font-bold text-gray-500 hover:text-gray-800 transition-colors duration-300 z-10 focus:outline-none">
                        <span class="relative z-10">Konstruksi (Build)</span>
                        <div
                            class="filter-bg absolute inset-0 bg-[#0d9488] rounded-full shadow-md -z-10 transition-all duration-300 opacity-0 scale-95">
                        </div>
                    </button>
                </div>
            </div>

            {{-- Grid Cards Portofolio --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6" id="portfolio-list">
                @forelse ($portofolios as $item)
                    @php
                        // Menangani Kategori
                        $kategori = strtolower($item->tipe);
                        $mainImage = !empty($item->gambar)
                            ? asset('storage/' . $item->gambar)
                            : asset('frontside/img/default-property.jpg');
                    @endphp

                    <div class="portfolio-item group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1 border border-gray-100 flex flex-col h-full opacity-100 scale-100"
                        data-category="{{ $kategori }}">

                        {{-- Image Wrapper (Rasio 4:3 agar seragam dengan halaman lain) --}}
                        <div class="relative w-full aspect-[4/3] overflow-hidden bg-gray-200 shrink-0">
                            {{-- Badge Kategori --}}
                            <div class="absolute top-3 left-3 z-10">
                                <span
                                    class="px-2.5 py-1 bg-white/90 backdrop-blur-sm text-[#0d9488] text-[10px] font-bold uppercase tracking-widest rounded shadow-sm border border-white/50">
                                    {{ ucfirst($kategori) }}
                                </span>
                            </div>

                            {{-- Image --}}
                            <img src="{{ $mainImage }}" alt="{{ $item->judul }}"
                                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                                loading="lazy">
                        </div>

                        {{-- Content --}}
                        <div class="p-5 bg-white flex flex-col flex-grow">
                            {{-- Judul --}}
                            <h3
                                class="font-heading text-lg font-bold text-gray-900 mb-1 leading-snug group-hover:text-[#0d9488] transition-colors line-clamp-2">
                                {{ $item->judul }}
                            </h3>

                            {{-- Nama Pemilik --}}
                            <p class="text-xs text-gray-500 mb-4 flex items-center">
                                <svg class="w-3.5 h-3.5 mr-1.5 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24"
                                    stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                </svg>
                                {{ Str::limit($item->pemilik ?? 'Klien Dabelyuland', 25) }}
                            </p>

                            {{-- Lokasi Bottom --}}
                            <div
                                class="mt-auto pt-4 border-t border-gray-50 flex items-start gap-2 text-gray-500 text-xs font-medium">
                                <i class="fas fa-map-marker-alt text-[#0d9488] mt-0.5 shrink-0"></i>
                                <span
                                    class="line-clamp-2 leading-relaxed">{{ $item->alamat ?? 'Lokasi tidak tersedia' }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    {{-- Empty State --}}
                    <div class="col-span-full py-20 text-center bg-white shadow-sm rounded-3xl border border-gray-100">
                        <div
                            class="mx-auto w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center text-gray-300 text-3xl mb-4">
                            <i class="fas fa-folder-open"></i>
                        </div>
                        <h3 class="font-heading text-xl font-bold text-gray-900">Belum ada portofolio</h3>
                        <p class="text-gray-500 mt-2 text-sm font-body">Data proyek akan segera ditambahkan.</p>
                    </div>
                @endforelse
            </div>

            {{-- Pesan Kosong Saat Filter --}}
            <div id="no-results-msg"
                class="hidden col-span-full py-16 text-center bg-white shadow-sm rounded-3xl border border-gray-100 mt-6">
                <div
                    class="mx-auto w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center text-gray-300 text-2xl mb-4">
                    <i class="fas fa-search"></i>
                </div>
                <h3 class="font-heading text-lg font-bold text-gray-900">Tidak ada proyek</h3>
                <p class="text-gray-500 mt-1 text-sm font-body">Kategori ini belum memiliki data untuk ditampilkan.</p>
            </div>

            {{-- Pagination (Menggunakan style bawaan Laravel/Tailwind) --}}
            @if (isset($portofolios) && $portofolios->hasPages())
                <div class="mt-16 flex justify-center">
                    {{ $portofolios->links() }}
                </div>
            @endif

        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // 1. Smooth Scroll Effect untuk tombol "Lihat Karya Kami" di Hero
            document.querySelectorAll('a[href^="#portfolio-grid"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        const headerOffset = 100; // Jarak aman dari header sticky
                        const elementPosition = target.getBoundingClientRect().top;
                        const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                        window.scrollTo({
                            top: offsetPosition,
                            behavior: "smooth"
                        });
                    }
                });
            });

            // 2. Logic Filter Portofolio (Dengan Animasi Pill Kapsul Modern)
            const filterBtns = document.querySelectorAll('.filter-btn');
            const portfolioItems = document.querySelectorAll('.portfolio-item');
            const noResultsMsg = document.getElementById('no-results-msg');

            filterBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    const filterValue = btn.getAttribute('data-filter');
                    let visibleCount = 0;

                    // Reset semua tombol (Matikan warna aktif)
                    filterBtns.forEach(b => {
                        b.classList.remove('text-white', 'active');
                        b.classList.add('text-gray-500');
                        // Sembunyikan background
                        b.querySelector('.filter-bg').classList.replace('opacity-100',
                            'opacity-0');
                        b.querySelector('.filter-bg').classList.replace('scale-100',
                            'scale-95');
                    });

                    // Aktifkan tombol yang sedang diklik
                    btn.classList.add('text-white', 'active');
                    btn.classList.remove('text-gray-500');
                    // Tampilkan background highlight
                    btn.querySelector('.filter-bg').classList.replace('opacity-0', 'opacity-100');
                    btn.querySelector('.filter-bg').classList.replace('scale-95', 'scale-100');

                    // Proses filter item
                    portfolioItems.forEach(item => {
                        const itemCategory = item.getAttribute('data-category');

                        if (filterValue === 'all' || filterValue === itemCategory) {
                            item.classList.remove('portfolio-hidden');
                            setTimeout(() => {
                                item.style.opacity = '1';
                                item.style.transform = 'scale(1)';
                            }, 50); // Jeda kecil agar transisi CSS terbaca
                            visibleCount++;
                        } else {
                            item.style.opacity = '0';
                            item.style.transform = 'scale(0.95)';
                            // Sembunyikan dari DOM setelah animasi selesai (400ms)
                            setTimeout(() => {
                                if (item.style.opacity === '0') {
                                    item.classList.add('portfolio-hidden');
                                }
                            }, 400);
                        }
                    });

                    // Tampilkan pesan kosong jika tidak ada yang cocok
                    setTimeout(() => {
                        if (visibleCount === 0) {
                            noResultsMsg.classList.remove('hidden');
                        } else {
                            noResultsMsg.classList.add('hidden');
                        }
                    }, 400);
                });
            });

        });
    </script>
@endsection
