@extends('layouts.index')

@section('styles')
    {{-- MENGGANTI FONT: Menghapus Playfair, Menggunakan Plus Jakarta Sans & Inter --}}
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        /* Efek Peek / Center Mode untuk Hero Banner */
        .hero-swiper {
            padding-bottom: 3rem !important;
            /* Ruang untuk titik pagination */
        }

        .hero-swiper .swiper-slide {
            width: 85%;
            /* Lebar gambar di HP */
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            opacity: 0.4;
            /* Redup saat di pinggir */
            transform: scale(0.85);
            /* Mengecil saat di pinggir */
        }

        @media (min-width: 768px) {
            .hero-swiper .swiper-slide {
                width: 75%;
            }
        }

        @media (min-width: 1024px) {
            .hero-swiper .swiper-slide {
                width: 65%;
            }
        }

        .hero-swiper .swiper-slide-active {
            opacity: 1;
            /* Terang penuh */
            transform: scale(1);
            /* Ukuran normal */
            z-index: 10;
        }

        .hide-scroll::-webkit-scrollbar {
            display: none;
        }

        .hide-scroll {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
@endsection

@section('content')

    <div class="bg-gray-50 font-['Inter'] overflow-x-hidden">

        {{-- ================================================================
             1. HERO BANNER SECTION (Infinite Seamless Peek Carousel)
             ================================================================ --}}
        @if (count($banners) > 0)
            <div class="w-full bg-gray-50 pt-8 pb-16 relative">

                <div class="max-w-full mx-auto relative overflow-hidden group" id="infinitePeekCarousel">

                    {{-- Wrapper diubah menggunakan flex untuk menengahkan elemen dummy penahan tinggi --}}
                    <div class="relative w-full flex justify-center items-start">

                        {{-- DUMMY ELEMENT (KUNCI UTAMA): 
                        Berfungsi sebagai "tiang" transparan untuk menjaga tinggi parent container.
                        Ukurannya disamakan persis dengan kartu (w-85, 70, 60) dan dikunci rasionya (3:1).
                        Dengan begini container tidak akan pernah kolaps dan ukurannya mutlak 3:1! --}}
                        <div class="w-[85%] md:w-[70%] lg:w-[60%] aspect-[3/1] invisible pointer-events-none"></div>

                        @foreach ($banners as $key => $banner)
                            {{-- KARTU BANNER: h-full diganti dengan aspect-[3/1] agar masing-masing card mengunci rasionya sendiri --}}
                            <div class="carousel-card absolute top-0 w-[85%] md:w-[70%] lg:w-[60%] aspect-[3/1] rounded-2xl overflow-hidden shadow-lg transition-all duration-[700ms] ease-in-out opacity-0"
                                data-index="{{ $key }}">
                                <a href="{{ route('shop.index') }}" class="carousel-link block w-full h-full">
                                    {{-- GAMBAR: object-fit: cover & object-center menjamin gambar proporsional anti-stretch --}}
                                    <img src="{{ asset('storage/' . $banner->image) }}"
                                        class="w-full h-full object-cover object-center" alt="Banner Image">
                                </a>
                            </div>
                        @endforeach
                    </div>

                    {{-- Navigasi Kiri & Kanan --}}
                    <button id="btnPeekPrev"
                        class="absolute left-3 lg:left-10 top-[45%] -translate-y-1/2 w-10 md:w-12 h-10 md:h-12 bg-white/90 hover:bg-white rounded-full shadow-md text-[#0f636d] flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300 z-40 focus:outline-none hover:scale-110">
                        <i class="fas fa-chevron-left text-lg"></i>
                    </button>
                    <button id="btnPeekNext"
                        class="absolute right-3 lg:right-10 top-[45%] -translate-y-1/2 w-10 md:w-12 h-10 md:h-12 bg-white/90 hover:bg-white rounded-full shadow-md text-[#0f636d] flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300 z-40 focus:outline-none hover:scale-110">
                        <i class="fas fa-chevron-right text-lg"></i>
                    </button>

                    <div class="flex justify-center items-center gap-2 mt-6 relative z-40" id="peekDots"></div>
                </div>
            </div>
        @endif

        <br><br>

        {{-- ================================================================
             2. FLOATING CATEGORIES
             ================================================================ --}}
        <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-40 -mt-24 mb-20">
            <div
                class="bg-white rounded-2xl shadow-[0_20px_40px_-15px_rgba(0,0,0,0.1)] p-4 sm:p-6 md:p-8 border border-gray-100">
                <div class="text-center mb-4 md:mb-6">
                    <h3 class="text-xs md:text-sm font-bold text-gray-400 uppercase tracking-widest font-['Inter']">Apa yang
                        Anda cari?</h3>
                </div>

                {{-- Grid di mobile 3 kolom (lebih rapat), di desktop 6 kolom --}}
                <div class="grid grid-cols-3 md:grid-cols-6 gap-3 sm:gap-4 md:gap-6 justify-items-center">
                    @php
                        $categories = [
                            ['slug' => 'rumah', 'icon' => 'fas fa-home', 'label' => 'Rumah'],
                            ['slug' => 'apartemen', 'icon' => 'far fa-building', 'label' => 'Apartemen'],
                            ['slug' => 'ruko', 'icon' => 'fas fa-store', 'label' => 'Ruko'],
                            ['slug' => 'kantor', 'icon' => 'fas fa-briefcase', 'label' => 'Kantor'],
                            ['slug' => 'gudang', 'icon' => 'fas fa-warehouse', 'label' => 'Gudang'],
                            ['slug' => 'tanah', 'icon' => 'fas fa-map-marked-alt', 'label' => 'Tanah'],
                        ];
                    @endphp

                    @foreach ($categories as $cat)
                        <a href="{{ route('shop.index', ['kategori_slug' => $cat['slug']]) }}"
                            class="group flex flex-col items-center gap-1.5 md:gap-3 text-decoration-none">
                            {{-- Kotak Icon: w-12 h-12 text-lg di mobile, w-16 h-16 text-2xl di desktop --}}
                            <div
                                class="w-12 h-12 sm:w-14 sm:h-14 md:w-16 md:h-16 rounded-xl md:rounded-2xl bg-gray-50 text-[#198754] flex items-center justify-center text-lg md:text-2xl transition-all duration-300 group-hover:bg-[#198754] group-hover:text-white group-hover:shadow-lg group-hover:shadow-[#198754]/30 group-hover:-translate-y-1 md:group-hover:-translate-y-2">
                                <i class="{{ $cat['icon'] }}"></i>
                            </div>
                            {{-- Teks: text-[10px] di mobile, text-sm di desktop --}}
                            <span
                                class="text-[10px] sm:text-xs md:text-sm font-bold text-gray-600 group-hover:text-[#198754] transition-colors font-['Inter'] text-center leading-tight">{{ $cat['label'] }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ================================================================
             3. HIGHLIGHT SECTION
             ================================================================ --}}
        @if (count($highlights) > 0)
            <div class="py-20 bg-white overflow-hidden">
                <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">

                    <div class="flex flex-col md:flex-row justify-between items-end mb-12">
                        <div>
                            <span class="text-[#0d9488] font-bold text-xs tracking-widest uppercase font-['Inter']">
                                Pilihan Terbaik
                            </span>
                            <h2 class="font-['Plus_Jakarta_Sans'] text-3xl md:text-4xl font-extrabold text-gray-900 mt-2">
                                Properti Highlight
                            </h2>
                        </div>
                        <div class="hidden md:block">
                            <div class="flex gap-3">
                                <button id="btnRecPrev"
                                    class="w-12 h-12 rounded-full border-2 border-gray-200 text-gray-500 flex items-center justify-center hover:border-[#0d9488] hover:bg-[#0d9488] hover:text-white transition-all focus:outline-none">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <button id="btnRecNext"
                                    class="w-12 h-12 rounded-full border-2 border-gray-200 text-gray-500 flex items-center justify-center hover:border-[#0d9488] hover:bg-[#0d9488] hover:text-white transition-all focus:outline-none">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div id="highlightTrack"
                        class="flex overflow-x-auto snap-x snap-mandatory scroll-smooth gap-6 pb-8 [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]">
                        @foreach ($highlights as $item)
                            @php
                                $val = $item->property;
                                $detailUrlRec = route('home.property-details', $val->slug);
                                $propertyId = $val->id;

                                $imagePath = $val->mainImage?->image_path ?? null;
                                $mainImage = $imagePath
                                    ? asset('storage/' . $imagePath . '-image_high.webp')
                                    : asset('frontside/img/default-property.jpg');
                            @endphp

                            {{-- Card Item --}}
                            <div class="w-[90%] md:w-[80%] lg:w-[100%] flex-none snap-center">

                                <div
                                    class="bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.06)] hover:shadow-[0_15px_40px_rgb(0,0,0,0.1)] transition-shadow duration-300 border border-gray-100 overflow-hidden h-auto lg:h-[420px] group">

                                    <div class="flex flex-col lg:flex-row h-full">

                                        {{-- =============================================== --}}
                                        {{-- Bagian Gambar (Kiri) & Tombol Favorit --}}
                                        {{-- =============================================== --}}
                                        <div class="lg:w-7/12 relative h-[250px] lg:h-full flex-shrink-0 overflow-hidden">
                                            <img onclick="window.location.href='{{ $detailUrlRec }}'"
                                                src="{{ $mainImage }}"
                                                class="absolute inset-0 cursor-pointer w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                                                alt="{{ $val->judul }}">

                                            {{-- Label Status --}}
                                            <div class="absolute top-6 left-6 z-10">
                                                <span
                                                    class="bg-[#0d9488] text-white px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wider shadow-lg">
                                                    Rekomendasi
                                                </span>
                                            </div>

                                            {{-- TOMBOL FAVORIT (Diletakkan di Kanan Atas Gambar) --}}
                                            <button onclick="toggleFavorite(this)" id="btn-favorite-{{ $val->id }}"
                                                class="btn-favorite absolute top-6 right-6 z-20 w-10 h-10 rounded-full bg-white/80 backdrop-blur border border-transparent flex items-center justify-center shadow-sm hover:shadow-md transition-all duration-200 focus:outline-none"
                                                data-id="{{ $val->id }}" data-is-favorite="false">

                                                {{-- Ikon Default Hati Outline --}}
                                                <svg class="heart-{{ $val->id }} heart-icon w-6 h-6 text-gray-500 hover:text-rose-500 transition-colors"
                                                    fill="none" stroke="currentColor" stroke-width="1.5"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                                                </svg>
                                            </button>
                                        </div>

                                        {{-- =============================================== --}}
                                        {{-- Bagian Konten (Kanan) --}}
                                        {{-- =============================================== --}}
                                        <div class="lg:w-5/12 p-8 lg:p-10 flex flex-col justify-between">
                                            <div>
                                                <a href="{{ $detailUrlRec }}" class="no-underline">
                                                    <h3 class="font-['Plus_Jakarta_Sans'] text-2xl lg:text-3xl font-bold text-gray-900 mb-3 hover:text-[#0d9488] transition-colors line-clamp-2"
                                                        title="{{ $val->judul }}">
                                                        {{ $val->judul }}
                                                    </h3>
                                                </a>
                                                <div class="flex items-center text-gray-500 mb-6 font-['Inter'] text-sm">
                                                    <i class="fas fa-map-marker-alt text-orange-500 mr-2"></i>
                                                    <span class="font-medium truncate">{{ $val->kota }}</span>
                                                </div>

                                                <h4
                                                    class="font-['Plus_Jakarta_Sans'] text-3xl font-extrabold text-[#132D48] mb-4">
                                                    Rp {{ number_format((int) $val->harga, 0, ',', '.') }}
                                                </h4>

                                                <p
                                                    class="text-gray-500 leading-relaxed mb-0 line-clamp-3 font-['Inter'] text-[0.95rem]">
                                                    {{ Str::limit(strip_tags($val->deskripsi ?? 'Hunian nyaman dengan fasilitas lengkap dan lokasi strategis yang cocok untuk keluarga Anda.'), 150) }}
                                                </p>
                                            </div>

                                            <div class="mt-8 pt-6 border-t border-gray-100">
                                                <a onclick="trackWhatsAppClick({{ $propertyId }})"
                                                    href="https://wa.me/{{ $val->phone ?? '62812345678' }}" target="_blank"
                                                    class="w-full py-3.5 bg-[#25D366] hover:bg-[#1EBE55] text-white font-bold rounded-xl shadow-[0_8px_20px_rgba(37,211,102,0.3)] hover:shadow-none hover:-translate-y-0.5 transition-all duration-300 flex items-center justify-center gap-2 font-['Inter'] no-underline">
                                                    <i class="fab fa-whatsapp text-xl"></i> Hubungi via WhatsApp
                                                </a>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        {{-- ================================================================
             4. EDUKASI: MENGAPA KAMI
             ================================================================ --}}
        <section class="py-20 bg-white border-y border-gray-100">
            <div class="w-full max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-14">
                    <span class="text-[#0d9488] font-bold text-sm tracking-widest uppercase font-['Inter']">Keunggulan
                        Layanan</span>
                    <h2
                        class="font-['Plus_Jakarta_Sans'] text-3xl md:text-[40px] font-bold text-gray-900 mt-3 tracking-tight">
                        Mengapa Memilih #tebaslahan?
                    </h2>
                    <p class="font-['Inter'] text-gray-500 mt-4 max-w-2xl mx-auto leading-relaxed">
                        Kami tidak hanya menjual properti, tetapi memberikan pendampingan penuh untuk memastikan investasi
                        dan hunian Anda aman, legal, dan terpercaya.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                    <div class="text-center px-4">
                        <div
                            class="w-16 h-16 mx-auto rounded-2xl bg-[#F0F7F7] text-[#0d9488] flex items-center justify-center text-3xl mb-6 transition-transform hover:-translate-y-2 duration-300">
                            <i class="fas fa-file-signature"></i>
                        </div>
                        <h4 class="font-['Plus_Jakarta_Sans'] text-xl font-bold text-gray-900 mb-3">Legalitas Terjamin</h4>
                        <p class="font-['Inter'] text-gray-600 text-sm leading-relaxed">
                            Seluruh properti dan lahan yang kami pasarkan telah melalui tahap verifikasi dokumen. Memastikan
                            keamanan investasi Anda terbebas dari sengketa.
                        </p>
                    </div>

                    <div class="text-center px-4">
                        <div
                            class="w-16 h-16 mx-auto rounded-2xl bg-[#F0F7F7] text-[#0d9488] flex items-center justify-center text-3xl mb-6 transition-transform hover:-translate-y-2 duration-300">
                            <i class="fas fa-handshake"></i>
                        </div>
                        <h4 class="font-['Plus_Jakarta_Sans'] text-xl font-bold text-gray-900 mb-3">Pendampingan Penuh</h4>
                        <p class="font-['Inter'] text-gray-600 text-sm leading-relaxed">
                            Tim ahli kami siap mendampingi proses Anda dari awal konsultasi, survei lokasi, hingga proses
                            serah terima kunci dan sertifikat.
                        </p>
                    </div>

                    <div class="text-center px-4">
                        <div
                            class="w-16 h-16 mx-auto rounded-2xl bg-[#F0F7F7] text-[#0d9488] flex items-center justify-center text-3xl mb-6 transition-transform hover:-translate-y-2 duration-300">
                            <i class="fas fa-drafting-compass"></i>
                        </div>
                        <h4 class="font-['Plus_Jakarta_Sans'] text-xl font-bold text-gray-900 mb-3">Solusi Desain & Bangun
                        </h4>
                        <p class="font-['Inter'] text-gray-600 text-sm leading-relaxed">
                            Sebagai bagian dari Dabelyuland, kami tidak hanya mencarikan lahan yang tepat, tetapi juga
                            memberikan solusi arsitektur dan konstruksi pembangunan.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        {{-- ================================================================
             5. LISTING PROPERTI REKOMENDASI
             ================================================================ --}}
        <div class="py-20 bg-gray-50">
            <div class="w-full max-w-360 mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-3xl mx-auto mb-12">
                    <h2 class="font-['Plus_Jakarta_Sans'] text-3xl md:text-4xl font-bold text-gray-900 mb-4">Selengkapnya
                        Tentang Properti</h2>
                    <p class="text-gray-500 font-['Inter']">Jelajahi berbagai pilihan properti terbaik yang telah kami
                        kurasi khusus untuk Anda.</p>
                </div>

                @if ($rekomendasi->count() > 0)
                    <div id="property-list"
                        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 md:gap-8">
                        @foreach ($rekomendasi as $item)
                            @include('partials.property.cardProperty', [
                                'item' => $item,
                                'isSearch' => false,
                            ])
                        @endforeach
                    </div>

                    <div class="text-center mt-12">
                        {{-- Tombol Lihat Lebih Banyak (Muncul jika ada cursor selanjutnya) --}}
                        <button onclick="loadMoreProperties()" id="load-more-btn"
                            class="{{ !$rekomendasi->hasMorePages() ? 'hidden' : 'inline-flex' }} items-center justify-center px-8 py-3 bg-white border-black border hover:text-white font-bold rounded-full hover:bg-gray-700 transition font-['Inter']">
                            <span>Lihat Lebih Banyak</span>
                            <div id="btn-loader"
                                class="hidden ml-2 animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full">
                            </div>
                        </button>

                        {{-- Link ke Shop (Muncul jika data sudah habis) --}}
                        <a id="all-property-link" href="{{ route('shop.index') }}"
                            class="{{ $rekomendasi->hasMorePages() ? 'hidden' : 'inline-flex' }} items-center justify-center px-8 py-3 border border-gray-800 text-gray-800 font-bold rounded-full hover:bg-gray-800 hover:text-white transition font-['Inter'] no-underline">
                            Lihat Properti Lainnya <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="bg-white shadow-sm rounded-xl p-8 inline-block">
                            <p class="text-gray-500 font-bold font-['Inter'] m-0">Belum ada properti yang direkomendasikan.
                            </p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- ================================================================
            6. TESTIMONIAL SECTION (3 Kolom dengan Swiper Carousel)
            ================================================================ --}}
        <div class="py-24 bg-gray-50">
            <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                {{-- Header Section --}}
                <div class="text-center max-w-2xl mx-auto mb-16">
                    <span
                        class="text-[#0d9488] font-bold text-xs tracking-widest uppercase font-['Inter'] bg-teal-50 px-3 py-1 rounded-full">
                        Testimonial
                    </span>
                    <h2
                        class="font-['Plus_Jakarta_Sans'] text-3xl md:text-4xl font-extrabold text-gray-900 mt-4 tracking-tight">
                        Apa Kata Klien Kami?
                    </h2>
                    <p class="text-gray-500 mt-4 font-['Inter'] text-sm md:text-base leading-relaxed">
                        Kepercayaan mereka adalah bukti dedikasi kami dalam memberikan layanan properti terbaik dan
                        terpercaya.
                    </p>
                </div>

                {{-- Swiper Carousel Container --}}
                <div class="swiper testimonialSwiper pb-14!">
                    <div class="swiper-wrapper">
                        @if (isset($testimonis) && $testimonis->count() > 0)
                            @foreach ($testimonis as $testimoni)
                                {{-- Class h-auto memastikan semua card dalam satu baris memiliki tinggi yang sama --}}
                                <div class="swiper-slide h-auto font-['Inter']">

                                    {{-- Card Testimonial --}}
                                    <div
                                        class="bg-white border border-gray-100 rounded-3xl p-8 shadow-[0_4px_20px_rgb(0,0,0,0.03)] hover:shadow-[0_8px_30px_rgb(13,148,136,0.1)] transition-all duration-300 h-full flex flex-col relative group">

                                        {{-- Ikon Kutip (Quote) Background --}}
                                        <div
                                            class="absolute top-6 right-6 text-gray-100 text-7xl font-serif leading-none select-none group-hover:text-teal-50 transition-colors z-0">
                                            "
                                        </div>

                                        {{-- Bintang Rating --}}
                                        <div class="flex gap-1 text-yellow-400 text-sm mb-6 relative z-10">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                        </div>

                                        {{-- Teks Testimonial (Dibatasi 5 baris agar rapi) --}}
                                        <div class="grow relative z-10 mb-6">
                                            <p class="text-gray-600 leading-relaxed text-[0.95rem] italic line-clamp-5"
                                                title="{{ $testimoni->testimoni }}">
                                                "{{ $testimoni->testimoni }}"
                                            </p>
                                        </div>

                                        {{-- Profil Pengguna (Selalu di bawah berkat mt-auto) --}}
                                        <div class="flex items-center mt-auto pt-5 border-t border-gray-100 relative z-10">
                                            <div
                                                class="w-12 h-12 rounded-full flex items-center justify-center overflow-hidden bg-gray-500 mr-4 shadow-sm border-2 border-white ring-1 ring-gray-100 shrink-0">
                                                @if ($testimoni->foto)
                                                    <img src="{{ asset('storage/' . $testimoni->foto) }}"
                                                        alt="{{ $testimoni->nama }}"
                                                        class="w-full h-full flex items-center justify-center text-[#0d9488]">
                                                @else
                                                    <div
                                                        class="w-full h-full flex items-center justify-center text-[#0d9488]">
                                                        <i class="fas fa-user text-lg"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="flex flex-col overflow-hidden">
                                                <h6
                                                    class="font-['Plus_Jakarta_Sans'] font-bold text-gray-900 mb-0.5 text-base truncate">
                                                    {{ $testimoni->nama }}</h6>
                                                <span
                                                    class="text-xs text-[#0d9488] font-bold uppercase tracking-wider truncate">{{ $testimoni->pekerjaan }}</span>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="w-full text-center text-gray-500 py-10 font-['Inter']">
                                Belum ada data testimoni.
                            </div>
                        @endif
                    </div>

                    {{-- Navigasi Titik (Dots) di bawah carousel --}}
                    <div class="swiper-pagination !bottom-0"></div>
                </div>

            </div>
        </div>

        {{-- ================================================================
             7. NEWS / BLOG SECTION
             ================================================================ --}}
        <section class="py-20 bg-gray-50">
            <div class="w-full max-w-[90rem] mx-auto px-4 sm:px-6 lg:px-8">

                {{-- Header Section --}}
                <div class="flex items-center justify-between mb-10">
                    <div class="flex items-center">
                        <img class="w-10 mr-3" src="{{ asset('frontside/img/icon/logo-green.svg') }}" alt="Icon" />
                        <h4 class="font-['Plus_Jakarta_Sans'] text-2xl font-bold text-gray-900 m-0">
                            Dabelyuland<span class="text-[#198754]">.NEWS</span>
                        </h4>
                    </div>
                    <div class="hidden md:block">
                        <a href="#"
                            class="text-[#198754] font-bold text-sm hover:text-green-800 transition-colors no-underline">
                            Lihat Semua Artikel &rarr;
                        </a>
                    </div>
                </div>

                {{-- Karena Property menggunakan Grid, News juga kita jadikan Grid Statis yang konsisten.
                     Aturan: Mobile (2), Tablet (3), Laptop (4), Desktop Lebar (5) --}}
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 md:gap-6">

                    <a href="{{ route('news.show', ['slug' => 'saham-vs-properti-2025']) }}"
                        class="block h-full no-underline group font-['Inter']">
                        <div
                            class="bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-[0_15px_30px_-10px_rgba(0,0,0,0.1)] transition-all duration-300 hover:-translate-y-1.5 h-full flex flex-col">

                            <div class="relative w-full aspect-[4/3] overflow-hidden bg-gray-100 shrink-0">
                                <img src="https://asset.kompas.com/crops/lei7ewm68MrMv7YmYR22gPH3fCc=/0x0:1595x1063/1200x800/data/photo/2025/06/04/683f9436cd505.jpg"
                                    class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                                    alt="News">
                                <span
                                    class="absolute top-4 right-4 bg-white/95 backdrop-blur-sm text-[#198754] px-3 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider shadow-sm">
                                    Investasi
                                </span>
                            </div>

                            <div class="p-4 md:p-6 flex flex-col flex-grow">
                                <div class="flex items-center text-[10px] md:text-xs text-gray-400 mb-2 md:mb-3">
                                    <i class="far fa-calendar-alt mr-1.5"></i> 04 Mar 2026
                                </div>
                                <h5
                                    class="font-['Plus_Jakarta_Sans'] text-[15px] md:text-lg font-bold text-gray-900 mb-2 md:mb-3 line-clamp-2 group-hover:text-[#198754] transition-colors leading-snug">
                                    Saham vs Properti: Pilihan Investasi Terbaik di 2025
                                </h5>
                                <p
                                    class="text-gray-500 text-xs md:text-sm mb-4 line-clamp-2 md:line-clamp-3 leading-relaxed flex-grow">
                                    Di tengah ketidakpastian ekonomi global, satu pertanyaan besar muncul di benak investor:
                                    Apakah saham masih lebih menguntungkan...
                                </p>
                                <span
                                    class="text-[#198754] font-bold text-[11px] md:text-sm flex items-center mt-auto w-fit">
                                    Selengkapnya <i
                                        class="fas fa-arrow-right ml-1.5 transition-transform group-hover:translate-x-1.5"></i>
                                </span>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="md:hidden text-center mt-8">
                    <a href="#"
                        class="inline-block border border-[#198754] text-[#198754] font-bold text-sm px-6 py-2.5 rounded-full hover:bg-[#198754] hover:text-white transition-colors no-underline">
                        Lihat Semua Artikel
                    </a>
                </div>
            </div>
        </section>

    </div>

    {{-- Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        let currentCursor = "{{ $rekomendasi->nextCursor() ? $rekomendasi->nextCursor()->encode() : '' }}";


        function loadMoreProperties() {
            const btn = this;
            const loader = document.getElementById('btn-loader');
            const listContainer = document.getElementById('property-list');
            const shopLink = document.getElementById('all-property-link');
            const btnText = btn.querySelector('span');

            // Proteksi klik ganda & Loading state
            btn.disabled = true;
            loader.classList.remove('hidden');
            btnText.innerText = 'Memuat...';

            // Fetch data menggunakan cursor
            fetch(`?cursor=${currentCursor}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(async data => {
                    // 1. Buat elemen pembungkus sementara (virtual container)
                    const tempContainer = document.createElement('div');
                    tempContainer.innerHTML = data.html;

                    // 2. SINKRONISASI HANYA PADA DATA BARU (Efisien!)
                    // Kita panggil syncHeartIcons hanya pada tempContainer sebelum dimasukkan ke DOM utama
                    await syncHeartIcons(tempContainer);

                    // 3. Masukkan HTML yang sudah disinkronkan ke daftar utama
                    listContainer.insertAdjacentHTML('beforeend', tempContainer.innerHTML);

                    // Update cursor untuk request berikutnya
                    currentCursor = data.next_cursor;

                    if (data.hasMore) {
                        // Reset tombol jika masih ada data
                        btn.disabled = false;
                        loader.classList.add('hidden');
                        btnText.innerText = 'Lihat Lebih Banyak';
                    } else {
                        // Jika sudah habis, ganti tombol dengan link "Lihat Properti Lainnya"
                        btn.remove();
                        shopLink.classList.remove('hidden');
                    }
                })
                .catch(error => {
                    console.error('Error loading properties:', error);
                    btn.disabled = false;
                    loader.classList.add('hidden');
                    btnText.innerText = 'Lihat Lebih Banyak';
                });
        };

        // Insialisasi Favorite
        document.addEventListener('DOMContentLoaded', () => {
            syncHeartIcons();
        });

        async function syncHeartIcons(container = document) {
            try {
                // 1. Ambil daftar ID dari IndexedDB
                const favIds = await idb_get('listFavorites') || [];

                // 2. Cari semua tombol favorit yang ada di halaman
                const buttons = container.querySelectorAll('.btn-favorite');

                buttons.forEach(btn => {
                    const propertyId = parseInt(btn.getAttribute('data-id'));

                    // 3. Jika ID cocok, ubah warna & fill
                    if (favIds.includes(propertyId)) {
                        const svgIcons = btn.querySelector('svg');
                        // Ganti warna teks/stroke dan beri fill
                        svgIcons.classList.remove('text-gray-500');
                        svgIcons.classList.add('text-rose-500');
                        svgIcons.setAttribute('fill', 'currentColor');
                    }
                });
            } catch (error) {
                console.error('Error syncing heart icon:', error);
                return;
            }
        }

        async function toggleFavorite(button) {
            // 1. Ambil ID dari data-attribute (pastikan dalam bentuk Number/Integer)
            const propertyId = parseInt(button.getAttribute('data-id'));
            const svgIcons = document.querySelectorAll('.heart-' + propertyId);

            try {
                let isAdding = false;

                // 2. Update LIST ID di IndexedDB secara atomic
                await idb_update('listFavorites', (list) => {
                    const currentList = list || [];
                    const index = currentList.indexOf(propertyId);

                    if (index > -1) {
                        // Jika sudah ada, kita hapus (Unfavorite)
                        currentList.splice(index, 1);
                        isAdding = false;
                    } else {
                        // Jika belum ada, kita tambah (Favorite)
                        currentList.push(propertyId);
                        isAdding = true;
                    }
                    return currentList;
                });

                // 3. Update JUMLAH (Length) di IndexedDB
                await idb_update('lengthFavorites', (count) => {
                    const currentCount = count || 0;
                    return isAdding ? currentCount + 1 : Math.max(0, currentCount - 1);
                });

                // 4. Update UI Ikon (Hati) secara instan
                if (isAdding) {
                    svgIcons.forEach(svg => {
                        svg.classList.replace('text-gray-500', 'text-rose-500')
                        svg.setAttribute('fill', 'currentColor');
                    });
                    // Opsional: Trigger Toast Sukses
                    if (typeof Toast !== 'undefined') Toast.fire({
                        icon: 'success',
                        title: 'Ditambah ke favorit'
                    });
                } else {
                    svgIcons.forEach(svg => {
                        svg.classList.replace('text-rose-500', 'text-gray-500')
                        svg.setAttribute('fill', 'none');
                    });
                }

                // 5. Update Badge di Navbar & Dropdown
                await refreshBadgeUI();

            } catch (error) {
                console.error("Gagal update favorit:", error);
            }
        }

        // Fungsi pembantu untuk sinkronisasi Badge Navbar dan Label Dropdown
        async function refreshBadgeUI() {
            const navBadge = document.getElementById('nav-fav-count');
            const dropCount = document.getElementById('favDropdownCount');

            const totalFav = await idb_get('lengthFavorites') || 0;

            // Update Label di Dropdown
            if (dropCount) dropCount.innerText = `${totalFav} item`;

            // Update Badge Merah di Navbar
            if (navBadge) {
                navBadge.innerText = totalFav;
                if (totalFav > 0) {
                    navBadge.classList.remove('hidden');
                    navBadge.classList.add('animate-pop');
                    setTimeout(() => navBadge.classList.remove('animate-pop'), 300);
                } else {
                    navBadge.classList.add('hidden');
                }
            }
        }
    </script>
    <script>
        var heroSwiper = new Swiper(".hero-swiper", {
            slidesPerView: "auto",
            centeredSlides: true,
            spaceBetween: 10,
            loop: true,
            speed: 800,
            autoplay: {
                delay: 4000,
                disableOnInteraction: false,
            },
            pagination: {
                el: ".hero-swiper .swiper-pagination",
                clickable: true,
                dynamicBullets: true,
            },
            navigation: {
                nextEl: ".hero-swiper .swiper-button-next",
                prevEl: ".hero-swiper .swiper-button-prev",
            },
            breakpoints: {
                768: {
                    spaceBetween: 20
                }
            }
        });

        document.addEventListener('DOMContentLoaded', () => {
            const carousel = document.getElementById('infinitePeekCarousel');
            if (!carousel) return;

            const cards = carousel.querySelectorAll('.carousel-card');
            const btnPrev = document.getElementById('btnPeekPrev');
            const btnNext = document.getElementById('btnPeekNext');
            const dotsContainer = document.getElementById('peekDots');
            const totalCards = cards.length;
            if (totalCards === 0) return;

            let currentIndex = 0;
            let autoPlayTimer;

            cards.forEach((_, index) => {
                const dot = document.createElement('div');
                dot.className =
                    `h-2 rounded-full transition-all duration-300 cursor-pointer ${index === 0 ? 'w-8 bg-[#0f636d]' : 'w-2 bg-gray-300 hover:bg-gray-400'}`;
                dot.addEventListener('click', () => {
                    currentIndex = index;
                    updateCarousel();
                    resetAutoPlay();
                });
                dotsContainer.appendChild(dot);
            });
            const dots = dotsContainer.querySelectorAll('div');

            const classes = {
                active: ['left-1/2', '-translate-x-1/2', 'scale-100', 'opacity-100', 'blur-none', 'z-30',
                    'pointer-events-auto'
                ],
                prev: ['left-0', '-translate-x-[95%]', 'md:-translate-x-[85%]', 'lg:-translate-x-[75%]',
                    'scale-[0.85]', 'opacity-50', 'blur-[2px]', 'z-20', 'cursor-pointer',
                    'pointer-events-auto'
                ],
                next: ['left-full', '-translate-x-[5%]', 'md:-translate-x-[15%]', 'lg:-translate-x-[25%]',
                    'scale-[0.85]', 'opacity-50', 'blur-[2px]', 'z-20', 'cursor-pointer',
                    'pointer-events-auto'
                ],
                hiddenLeft: ['left-0', '-translate-x-[150%]', 'scale-75', 'opacity-0', 'z-10',
                    'pointer-events-none'
                ],
                hiddenRight: ['left-full', 'translate-x-[50%]', 'scale-75', 'opacity-0', 'z-10',
                    'pointer-events-none'
                ]
            };

            const baseClasses =
                "carousel-card absolute top-0 h-full w-[85%] md:w-[70%] lg:w-[72%] rounded-2xl overflow-hidden shadow-lg transition-all duration-[700ms] ease-in-out"
                .split(" ");

            function updateCarousel() {
                cards.forEach((card, index) => {
                    card.className = "";
                    card.classList.add(...baseClasses);
                    const link = card.querySelector('.carousel-link');

                    if (index === currentIndex) {
                        card.classList.add(...classes.active);
                        link.style.pointerEvents = 'auto';
                        card.onclick = null;
                    } else if (index === (currentIndex - 1 + totalCards) % totalCards) {
                        card.classList.add(...classes.prev);
                        link.style.pointerEvents = 'none';
                        card.onclick = () => {
                            currentIndex = index;
                            updateCarousel();
                            resetAutoPlay();
                        };
                    } else if (index === (currentIndex + 1) % totalCards) {
                        card.classList.add(...classes.next);
                        link.style.pointerEvents = 'none';
                        card.onclick = () => {
                            currentIndex = index;
                            updateCarousel();
                            resetAutoPlay();
                        };
                    } else {
                        let diff = index - currentIndex;
                        if (diff < 0) diff += totalCards;
                        if (diff > totalCards / 2) {
                            card.classList.add(...classes.hiddenLeft);
                        } else {
                            card.classList.add(...classes.hiddenRight);
                        }
                        link.style.pointerEvents = 'none';
                        card.onclick = null;
                    }
                });

                dots.forEach((dot, index) => {
                    if (index === currentIndex) {
                        dot.className =
                            "h-2 rounded-full transition-all duration-300 cursor-pointer w-8 bg-[#0f636d]";
                    } else {
                        dot.className =
                            "h-2 rounded-full transition-all duration-300 cursor-pointer w-2 bg-gray-300 hover:bg-gray-400";
                    }
                });
            }

            function nextSlide() {
                currentIndex = (currentIndex + 1) % totalCards;
                updateCarousel();
            }

            function prevSlide() {
                currentIndex = (currentIndex - 1 + totalCards) % totalCards;
                updateCarousel();
            }

            if (btnNext) btnNext.addEventListener('click', () => {
                nextSlide();
                resetAutoPlay();
            });
            if (btnPrev) btnPrev.addEventListener('click', () => {
                prevSlide();
                resetAutoPlay();
            });

            let touchStartX = 0,
                touchEndX = 0;
            carousel.addEventListener('touchstart', e => {
                touchStartX = e.changedTouches[0].screenX;
            }, {
                passive: true
            });
            carousel.addEventListener('touchend', e => {
                touchEndX = e.changedTouches[0].screenX;
                if (touchStartX - touchEndX > 50) {
                    nextSlide();
                    resetAutoPlay();
                }
                if (touchEndX - touchStartX > 50) {
                    prevSlide();
                    resetAutoPlay();
                }
            }, {
                passive: true
            });

            function startAutoPlay() {
                if (totalCards > 1) autoPlayTimer = setInterval(nextSlide, 5000);
            }

            function resetAutoPlay() {
                clearInterval(autoPlayTimer);
                startAutoPlay();
            }

            updateCarousel();
            startAutoPlay();
        });

        document.addEventListener('DOMContentLoaded', () => {
            const track = document.getElementById('highlightTrack');
            const btnPrev = document.getElementById('btnRecPrev');
            const btnNext = document.getElementById('btnRecNext');
            if (!track) return;
            let autoPlayTimer;

            const slideNext = () => {
                const scrollAmount = track.clientWidth * 0.8;
                if (track.scrollLeft + track.clientWidth >= track.scrollWidth - 10) {
                    track.scrollTo({
                        left: 0,
                        behavior: 'smooth'
                    });
                } else {
                    track.scrollBy({
                        left: scrollAmount,
                        behavior: 'smooth'
                    });
                }
            };
            const slidePrev = () => {
                track.scrollBy({
                    left: -(track.clientWidth * 0.8),
                    behavior: 'smooth'
                });
            };

            if (btnNext) btnNext.addEventListener('click', () => {
                slideNext();
                resetAutoPlay();
            });
            if (btnPrev) btnPrev.addEventListener('click', () => {
                slidePrev();
                resetAutoPlay();
            });

            const startAutoPlay = () => {
                autoPlayTimer = setInterval(slideNext, 5000);
            };
            const resetAutoPlay = () => {
                clearInterval(autoPlayTimer);
                startAutoPlay();
            };

            track.addEventListener('mouseenter', () => clearInterval(autoPlayTimer));
            track.addEventListener('mouseleave', startAutoPlay);
            track.addEventListener('touchstart', () => clearInterval(autoPlayTimer), {
                passive: true
            });
            track.addEventListener('touchend', startAutoPlay, {
                passive: true
            });

            startAutoPlay();
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Inisialisasi Swiper untuk Testimonial
            var testimonialSwiper = new Swiper(".testimonialSwiper", {
                slidesPerView: 1, // Tampilan default (Mobile): 1 Kolom
                spaceBetween: 20, // Jarak antar kartu
                loop: true, // Berputar terus menerus
                autoplay: {
                    delay: 5000, // Geser otomatis setiap 5 detik
                    disableOnInteraction: false,
                },
                pagination: {
                    el: ".testimonialSwiper .swiper-pagination",
                    clickable: true,
                    dynamicBullets: true, // Titik navigasi terlihat dinamis
                },
                breakpoints: {
                    // Tampilan Tablet: 2 Kolom
                    768: {
                        slidesPerView: 2,
                        spaceBetween: 24,
                    },
                    // Tampilan Desktop: 3 Kolom
                    1024: {
                        slidesPerView: 3,
                        spaceBetween: 32,
                    },
                }
            });
        });
    </script>
@endsection
