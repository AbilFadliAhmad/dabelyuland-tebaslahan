@extends('layouts.index')

@section('styles')
    {{-- MENGGANTI FONT: Menghapus Playfair Display, Menggunakan Plus Jakarta Sans & Inter --}}
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        .custom-shadow {
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.03);
        }

        .text-teal-theme {
            color: #0d9488;
        }

        /* Teal 600 */
        .bg-teal-theme {
            background-color: #0d9488;
        }

        .bg-teal-light {
            background-color: #F0F7F7;
        }

        /* Font Utilities Baru */
        .font-heading {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .font-body {
            font-family: 'Inter', sans-serif;
        }

        /* Lightbox Styles */
        #lightbox {
            transition: opacity 0.3s ease;
        }

        #lightbox.show {
            opacity: 1;
            pointer-events: auto;
        }

        #lightbox img {
            transition: transform 0.3s ease;
        }
    </style>
@endsection

@section('content')

    @php
        // Memastikan format gambar adalah array yang valid
        $imagesRaw = $property->galleries;
        $existingImages = !empty($property->galleries) ? $imagesRaw : ['frontside/img/card/default.jpg'];
        $totalImages = count($existingImages);
    @endphp

    <div class="bg-[#FAFAFA] font-body pb-24">

        {{-- Container Utama --}}
        <div class="container max-w-7xl mx-auto px-4 pt-8">

            {{-- Tombol Kembali & Breadcrumb --}}
            <div class="mb-6">
                <a href="{{ route('shop.index') }}"
                    class="inline-flex items-center text-sm font-semibold text-gray-500 hover:text-teal-theme transition-colors text-decoration-none">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali ke Listing
                </a>
            </div>

            {{-- 1. GALERI FOTO (Grid Adaptif & Anti Offside) --}}
            <div
                class="flex flex-col md:flex-row gap-2 md:gap-4 mb-10 rounded-2xl md:rounded-[24px] overflow-hidden h-auto md:h-[500px]">

                {{-- Foto Utama (Kiri - Dinamis Lebarnya, ditambah overflow-hidden) --}}
                <div class="relative w-full {{ $totalImages > 1 ? 'md:w-1/2' : 'w-full' }} h-[400px] md:h-full cursor-pointer group overflow-hidden"
                    onclick="openLightbox(0)">
                    <img src="{{ Storage::url($existingImages[0]->image_path . '-image_high.webp') }}" alt="{{ $property->judul }}"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute inset-0 bg-black/10 group-hover:bg-transparent transition-colors">
                    </div>
                </div>

                {{-- Foto Pendukung (Kanan - Grid Adaptif) --}}
                @if ($totalImages > 1)
                    <div
                        class="hidden md:grid w-1/2 gap-4 
                    {{ $totalImages == 2 ? 'grid-cols-1 grid-rows-1' : '' }}
                    {{ $totalImages == 3 ? 'grid-cols-1 grid-rows-2' : '' }}
                    {{ $totalImages >= 4 ? 'grid-cols-2 grid-rows-2' : '' }}
                    h-full">

                        @for ($i = 1; $i < min(5, $totalImages); $i++)
                            <div class="relative w-full h-full cursor-pointer group overflow-hidden rounded-xl
                            {{ $totalImages == 4 && $i == 1 ? 'col-span-2' : '' }}"
                                onclick="openLightbox({{ $i }})">

                                <img src="{{ Storage::url($existingImages[$i]?->image_path . '-image_high.webp' ?? '') }}"
                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">

                                {{-- Overlay "Lihat Semua Foto" --}}
                                @if ($i == 4 && $totalImages > 5)
                                    <div
                                        class="absolute inset-0 bg-black/60 flex flex-col items-center justify-center text-white transition-colors hover:bg-black/70">
                                        <i class="fas fa-images text-2xl mb-2"></i>
                                        <span class="font-bold text-sm">+{{ $totalImages - 5 }} Foto</span>
                                    </div>
                                @else
                                    <div class="absolute inset-0 bg-black/10 group-hover:bg-transparent transition-colors">
                                    </div>
                                @endif
                            </div>
                        @endfor
                    </div>
                @endif

                {{-- Tombol Lihat Semua Foto (Mobile) --}}
                @if ($totalImages > 1)
                    <div class="md:hidden absolute bottom-4 right-4 z-10">
                        <button onclick="openLightbox(0)"
                            class="bg-white/90 backdrop-blur text-gray-800 px-4 py-2 rounded-lg text-xs font-bold shadow-sm flex items-center">
                            <i class="fas fa-camera mr-2"></i> 1 / {{ $totalImages }}
                        </button>
                    </div>
                @endif
            </div>

            {{-- 2. KONTEN UTAMA & SIDEBAR --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">

                {{-- BAGIAN KIRI --}}
                <div class="lg:col-span-8">

                    {{-- Info Utama --}}
                    <div class="mb-10">
                        <div class="flex items-center text-gray-500 text-sm mb-3 font-medium">
                            <i class="fas fa-map-marker-alt text-teal-theme mr-2 text-lg"></i>
                            {{ ucwords($property->kota . ', ' . $property->kecamatan) }}
                        </div>

                        <h1
                            class="font-heading text-3xl md:text-[42px] font-extrabold text-gray-900 leading-tight tracking-tight mb-6">
                            {{ $property->judul }}
                        </h1>

                        {{-- Row Spesifikasi --}}
                        <div class="flex flex-wrap gap-3 md:gap-4 border-y border-gray-200 py-5">
                            <div
                                class="flex items-center bg-white border border-gray-100 px-4 py-2 rounded-xl custom-shadow">
                                <i class="fas fa-bed text-gray-400 text-lg mr-3"></i>
                                <div class="flex flex-col">
                                    <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wide">K.
                                        Tidur</span>
                                    <span class="font-bold text-gray-800">{{ $property->jumlah_kamar_tidur }}</span>
                                </div>
                            </div>
                            <div
                                class="flex items-center bg-white border border-gray-100 px-4 py-2 rounded-xl custom-shadow">
                                <i class="fas fa-bath text-gray-400 text-lg mr-3"></i>
                                <div class="flex flex-col">
                                    <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wide">K.
                                        Mandi</span>
                                    <span class="font-bold text-gray-800">{{ $property->jumlah_kamar_mandi }}</span>
                                </div>
                            </div>
                            <div
                                class="flex items-center bg-white border border-gray-100 px-4 py-2 rounded-xl custom-shadow">
                                <i class="fas fa-ruler-combined text-gray-400 text-lg mr-3"></i>
                                <div class="flex flex-col">
                                    <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wide">Luas</span>
                                    <span class="font-bold text-gray-800">{{ $property->luas_bangunan }} m²</span>
                                </div>
                            </div>
                            <div
                                class="flex items-center bg-white border border-gray-100 px-4 py-2 rounded-xl custom-shadow">
                                <i class="fas fa-home text-gray-400 text-lg mr-3"></i>
                                <div class="flex flex-col">
                                    <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wide">Tipe</span>
                                    <span
                                        class="font-bold text-gray-800">{{ ucfirst($property->tipe) ?? 'Property' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Deskripsi --}}
                    <div class="mb-12">
                        <h3 class="font-heading text-2xl font-bold text-gray-900 mb-5">Tentang Properti Ini</h3>
                        <div class="prose prose-gray max-w-none text-gray-600 leading-relaxed text-[15px] text-justify">
                            {!! htmlspecialchars_decode($property->deskripsi) !!}
                        </div>
                    </div>

                </div>

                {{-- BAGIAN KANAN (Sidebar Sticky) --}}
                <div class="lg:col-span-4">
                    <div class="sticky top-28">
                        <div class="bg-white rounded-[24px] custom-shadow border border-gray-100 p-6 lg:p-8">
                            <span class="text-gray-500 text-sm font-medium block mb-1">Harga Penawaran</span>
                            <h2
                                class="font-heading text-3xl lg:text-[34px] font-extrabold text-gray-900 mb-6 tracking-tight">
                                Rp {{ number_format((int) $property->harga, 0, ',', '.') }}
                            </h2>

                            <div class="w-full h-px bg-gray-100 mb-6"></div>

                            <p class="text-xs text-gray-500 mb-5 leading-relaxed">
                                Tertarik dengan properti ini? Hubungi kami sekarang untuk mendapatkan penawaran terbaik dan
                                mengatur jadwal survei lokasi.
                            </p>

                            {{-- Tombol CTA Hijau Terang --}}
                            <a href="https://wa.me/{{ $property->phone ?? '6282127277747' }}?text=Halo%20Dabelyuland,%20saya%20tertarik%20dan%20ingin%20mengajukan%20pembelian%20untuk%20properti:%20*{{ urlencode($property->judul) }}*."
                                target="_blank"
                                class="w-full py-4 bg-[#25D366] text-white font-bold text-base rounded-xl flex items-center justify-center shadow-lg shadow-[#25D366]/30 hover:bg-[#1ebe5d] hover:shadow-xl hover:shadow-[#25D366]/40 transition-all transform hover:-translate-y-1 text-decoration-none">
                                <i class="fab fa-whatsapp text-xl mr-2"></i> Ajukan Pembelian
                            </a>
                        </div>
                    </div>
                </div>

            </div>

            {{-- 3. PETA LOKASI --}}
            <div class="mt-16 pt-12 border-t border-gray-200">
                <h3 class="font-heading text-2xl font-bold text-gray-900 mb-6">Lokasi Properti</h3>

                <div class="relative w-full h-[400px] bg-gray-200 rounded-[24px] overflow-hidden custom-shadow">
                    <iframe
                        src="https://maps.google.com/maps?q={{ urlencode($property->lokasi) }}&t=&z=13&ie=UTF8&iwloc=&output=embed"
                        class="w-full h-full border-0 filter blur-[4px] pointer-events-none" allowfullscreen=""
                        loading="lazy">
                    </iframe>

                    <div class="absolute inset-0 bg-black/10 flex items-center justify-center">
                        <a href="https://wa.me/{{ $property->phone ?? '6282127277747' }}?text=Halo,%20saya%20ingin%20menanyakan%20detail%20lokasi%20properti%20*{{ urlencode($property->judul) }}*."
                            target="_blank"
                            class="bg-white text-gray-900 px-8 py-3.5 rounded-full font-bold text-sm shadow-xl hover:bg-teal-50 hover:text-teal-theme transition-all transform hover:scale-105 text-decoration-none flex items-center">
                            <i class="fas fa-map-marker-alt text-teal-theme mr-2 text-lg"></i> Tanya Detail Lokasi
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- 4. LIGHTBOX GALLERY MODAL --}}
    <div id="lightbox"
        class="fixed inset-0 bg-black/95 z-[9999] opacity-0 pointer-events-none flex flex-col items-center justify-center backdrop-blur-sm">

        <div class="absolute top-0 left-0 w-full p-6 flex justify-between items-center z-10">
            <div class="text-white font-medium text-sm bg-black/50 px-4 py-1.5 rounded-full font-body">
                <span id="lightbox-counter">1</span> / {{ count($existingImages) }}
            </div>
            <button onclick="closeLightbox()" class="text-white hover:text-gray-300 transition-colors">
                <i class="fas fa-times text-3xl"></i>
            </button>
        </div>

        <div class="relative w-full max-w-5xl h-[70vh] flex items-center justify-center px-4">
            <img id="lightbox-img" src="" class="max-w-full max-h-full object-contain rounded-lg shadow-2xl">
        </div>

        <button onclick="changeImage(-1)"
            class="absolute left-4 md:left-10 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-white/10 text-white hover:bg-white/30 transition-colors flex items-center justify-center backdrop-blur">
            <i class="fas fa-chevron-left text-xl"></i>
        </button>

        <button onclick="changeImage(1)"
            class="absolute right-4 md:right-10 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-white/10 text-white hover:bg-white/30 transition-colors flex items-center justify-center backdrop-blur">
            <i class="fas fa-chevron-right text-xl"></i>
        </button>
    </div>

@endsection

@section('scripts')
    <script>
        const galleryImages = @json($existingImages);
        let currentImageIndex = 0;

        const lightbox = document.getElementById('lightbox');
        const lightboxImg = document.getElementById('lightbox-img');
        const lightboxCounter = document.getElementById('lightbox-counter');

        function openLightbox(index) {
            currentImageIndex = index;
            updateLightboxImage();
            lightbox.classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closeLightbox() {
            lightbox.classList.remove('show');
            document.body.style.overflow = 'auto';
        }

        function changeImage(direction) {
            currentImageIndex += direction;

            if (currentImageIndex >= galleryImages.length) {
                currentImageIndex = 0;
            } else if (currentImageIndex < 0) {
                currentImageIndex = galleryImages.length - 1;
            }

            updateLightboxImage();
        }

        function updateLightboxImage() {
            lightboxImg.src = galleryImages[currentImageIndex];
            if (!lightboxImg.src.startsWith('http')) {
                lightboxImg.src = "{{ url('/') }}/" + galleryImages[currentImageIndex];
            }
            lightboxCounter.innerText = currentImageIndex + 1;
        }

        lightbox.addEventListener('click', function(e) {
            if (e.target === lightbox) {
                closeLightbox();
            }
        });

        document.addEventListener('keydown', function(e) {
            if (!lightbox.classList.contains('show')) return;
            if (e.key === 'Escape') closeLightbox();
            if (e.key === 'ArrowLeft') changeImage(-1);
            if (e.key === 'ArrowRight') changeImage(1);
        });
    </script>
@endsection
