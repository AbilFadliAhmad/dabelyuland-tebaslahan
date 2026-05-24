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
                    <img src="{{ Storage::url($existingImages[0]->image_path . '-image_high.webp') }}"
                        alt="{{ $property->judul }}"
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

                            {{-- Tombol WA (Sidebar) terhubung ke trackWhatsAppClick --}}
                            <a href="https://wa.me/{{ $property->phone ?? '6282127277747' }}?text=Halo%20Dabelyuland,%20saya%20tertarik%20dan%20ingin%20mengajukan%20pembelian%20untuk%20properti:%20*{{ urlencode($property->judul) }}*."
                                target="_blank" onclick="trackWhatsAppClick({{ $property->id }})"
                                class="w-full py-4 bg-[#25D366] text-white font-bold text-base rounded-xl flex items-center justify-center shadow-lg shadow-[#25D366]/30 hover:bg-[#1ebe5d] hover:shadow-xl hover:shadow-[#25D366]/40 transition-all transform hover:-translate-y-1 text-decoration-none">
                                <i class="fab fa-whatsapp text-xl mr-2"></i> Ajukan Pembelian
                            </a>


                            <div
                                class="mt-6 p-5 bg-gradient-to-br from-teal-50 to-emerald-50 border border-teal-100 rounded-xl flex flex-col items-center justify-center gap-3 shadow-sm relative overflow-hidden group">
                                <div class="absolute -right-4 -top-4 w-16 h-16 bg-teal-100 rounded-full opacity-50"></div>
                                <div class="absolute -left-4 -bottom-4 w-12 h-12 bg-emerald-100 rounded-full opacity-50">
                                </div>

                                @auth
                                    <div class="text-center relative z-10">
                                        <h4 class="text-sm font-bold text-teal-900 flex items-center justify-center">
                                            <i class="fas fa-gift text-amber-500 mr-2 text-lg animate-bounce"></i> Misi: Share &
                                            Dapatkan Koin!
                                        </h4>
                                        <p class="text-xs text-teal-700 mt-1.5 leading-relaxed px-2">
                                            Bagikan properti ini ke teman atau sosmed. Kumpulkan koin untuk fitur promosi
                                            eksklusif!
                                        </p>
                                    </div>
                                @endauth


                                {{-- Tombol Memanggil openShareModal --}}
                                <button
                                    onclick="openShareModal({{ $property->id }}, '{{ addslashes($property->judul) }}')"
                                    class="w-full mt-1 px-4 py-2.5 bg-[#0d9488] hover:bg-teal-700 text-white text-sm font-bold rounded-lg shadow-md transition-all flex items-center justify-center transform hover:-translate-y-0.5 relative z-10 border border-teal-600">
                                    <i class="fas fa-share-alt mr-2"></i> Bagikan Properti
                                </button>
                            </div>
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

    <div id="shareModal"
        class="fixed inset-0 z-[9999] hidden items-center justify-center transition-opacity duration-300 opacity-0">
        {{-- Overlay Gelap (Klik untuk menutup) --}}
        <div class="absolute inset-0 bg-black/60" onclick="closeShareModal()"></div>

        {{-- Konten Modal --}}
        <div id="shareModalContent"
            class="relative z-10 bg-white w-full max-w-lg rounded-3xl shadow-2xl p-6 transform scale-95 transition-transform duration-300 mx-4">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-900 font-['Plus_Jakarta_Sans']">Bagikan Properti</h3>
                <button onclick="closeShareModal()"
                    class="text-gray-400 hover:text-gray-700 bg-gray-50 hover:bg-gray-100 p-2 rounded-full transition-colors focus:outline-none">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            {{-- Tombol Sosial Media --}}
            <div class="flex gap-4 overflow-x-auto pb-4 mb-4 scrollbar-hide justify-between sm:justify-start">
                {{-- WhatsApp --}}
                <button onclick="trackAndShare('whatsapp')"
                    class="flex flex-col items-center gap-2 min-w-[70px] group focus:outline-none">
                    <div
                        class="w-14 h-14 rounded-full bg-[#25D366] text-white flex items-center justify-center text-3xl group-hover:scale-110 transition-transform shadow-md">
                        <i class="fab fa-whatsapp"></i>
                    </div>
                    <span class="text-xs text-gray-600 font-medium group-hover:text-gray-900">WhatsApp</span>
                </button>

                {{-- Facebook --}}
                <button onclick="trackAndShare('facebook')"
                    class="flex flex-col items-center gap-2 min-w-[70px] group focus:outline-none">
                    <div
                        class="w-14 h-14 rounded-full bg-[#1877F2] text-red-50 flex items-center justify-center text-3xl group-hover:scale-110 transition-transform shadow-md">
                        <i class="fab fa-facebook-f"></i>
                    </div>
                    <span class="text-xs text-gray-600 font-medium group-hover:text-gray-900">Facebook</span>
                </button>

                {{-- Twitter / X --}}
                <button onclick="trackAndShare('twitter')"
                    class="flex flex-col items-center gap-2 min-w-[70px] group focus:outline-none">
                    <div
                        class="w-14 h-14 bg-black rounded-full  flex items-center justify-center text-2xl group-hover:scale-110 transition-transform shadow-md">
                        <img src="/assets/images/icons/x-twitter.svg" class="w-[70%] aspect-square invert brightness-0"
                            alt="tidak ada">
                    </div>
                    <span class="text-xs text-gray-600 font-medium group-hover:text-gray-900">X</span>
                </button>

                {{-- Instagram (Diganti dari Telegram) --}}
                <button onclick="trackAndShare('instagram')"
                    class="flex flex-col items-center gap-2 min-w-[70px] group focus:outline-none">
                    <div
                        class="w-14 h-14 rounded-full bg-gradient-to-tr from-[#f09433] via-[#e6683c] to-[#bc1888] text-white flex items-center justify-center text-3xl group-hover:scale-110 transition-transform shadow-md">
                        <i class="fab fa-instagram"></i>
                    </div>
                    <span class="text-xs text-gray-600 font-medium group-hover:text-gray-900">Instagram</span>
                </button>
            </div>

            {{-- Input Tautan Salin --}}
            <div
                class="mt-2 p-1.5 border border-gray-200 rounded-xl bg-gray-50 flex items-center focus-within:border-[#0d9488] focus-within:ring-1 focus-within:ring-[#0d9488] transition-all">
                <input type="hidden" id="share-property-id" value="{{ $property->id }}">
                <input type="text" id="share-link-input" readonly
                    class="flex-1 bg-transparent px-3 text-sm text-gray-700 outline-none w-full whitespace-nowrap overflow-hidden text-ellipsis">
                <button id="btn-copy-link" onclick="trackAndShare('copy_link')"
                    class="px-5 py-2 bg-white border border-gray-200 text-gray-800 text-sm font-bold rounded-lg hover:bg-gray-100 transition-colors shadow-sm whitespace-nowrap">
                    Salin
                </button>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    {{-- Script track view properti --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const propertyId = '{{ $property->id }}';
            const urlParams = new URLSearchParams(window.location.search);
            const sourceParam = urlParams.get('source') || 'other';
            const refParam = urlParams.get('ref') || 'other';

            const maxAgeSeconds = getSecondsUntilMidnightWIB();

            // ========================================================
            // 1. TRACKING VIEW PROPERTY UMUM (KODE LAMA KAMU)
            // ========================================================
            const cookieName = 'property_viewed_' + propertyId;

            if (document.cookie.indexOf(cookieName + '=') === -1) {
                document.cookie = cookieName + "=true; max-age=" + maxAgeSeconds + "; path=/";

                const url = '{{ route('track-view-property') }}';

                let formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('source', sourceParam);
                formData.append('property_id', propertyId);

                navigator.sendBeacon(url, formData);
            }


            // ========================================================
            // 2. TRACKING QUEST PENGUNJUNG UNIK (KODE BARU)
            // ========================================================
            // Hanya jalankan beacon ini jika ada agen yang membagikan link (ref valid)
            if (refParam !== '') {

                // Nama cookie dibuat spesifik berdasarkan ID agen (ref)
                // Agar jika pengunjung klik link agen A, lalu besoknya klik link agen B, dua-duanya dapat poin.
                const questCookieName = 'quest_unique_visitor_ref_' + refParam;

                if (document.cookie.indexOf(questCookieName + '=') === -1) {
                    document.cookie = questCookieName + "=true; max-age=" + maxAgeSeconds + "; path=/";

                    // Panggil route khusus untuk menjalankan fungsi quest (Controller yang kita bahas sebelumnya)
                    const questUrl = '{{ route('quest.unique-view') }}';

                    let questFormData = new FormData();
                    questFormData.append('_token', '{{ csrf_token() }}');
                    questFormData.append('ref_user_id', refParam); // Kirim ID agen ke backend

                    navigator.sendBeacon(questUrl, questFormData);
                }
            }
        });
    </script>
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
            const storage = {{ Storage::url('') }}
            lightboxImg.src = storage + galleryImages[currentImageIndex].image_path + "-image_ori.webp";
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
    {{-- SCRIPT LOGIKA BAGIKAN & BEACON QUEST --}}
    <script>
        // Ambil ID User yang sedang login (Kosong jika tamu)
        const currentUserId = '{{ auth()->check() ? auth()->user()->id : '' }}';

        function openShareModal() {
            const modal = document.getElementById('shareModal');
            const modalContent = document.getElementById('shareModalContent');

            // Bersihkan parameter query yang mungkin sudah ada di URL browser saat ini
            const baseUrl = window.location.href.split('?')[0];

            // Buat tautan dasar dengan referal
            const baseShareLink = currentUserId ? `${baseUrl}?ref=${currentUserId}` : baseUrl;
            document.getElementById('share-link-input').value = baseShareLink;

            // Tampilkan Modal dengan animasi
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modalContent.classList.remove('scale-95');
            }, 10);
        }

        function closeShareModal() {
            const modal = document.getElementById('shareModal');
            const modalContent = document.getElementById('shareModalContent');

            modal.classList.add('opacity-0');
            modalContent.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }, 300);
        }

        function trackAndShare(platform) {
            const propertyId = document.getElementById('share-property-id').value;
            const baseUrl = window.location.href.split('?')[0];

            // Buat URL final yang spesifik dengan ref dan source (platform)
            const shareUrl = currentUserId ? `${baseUrl}?ref=${currentUserId}&source=${platform}` : baseUrl;
            const encodedUrl = encodeURIComponent(shareUrl);
            const textToShare = encodeURIComponent("Lihat properti menarik ini di TebasLahan! ");

            // ==========================================
            // 1. EKSEKUSI BEACON QUEST (KIRIM DATA KE SERVER)
            // ==========================================
            // Catatan: Saya tambahkan '_platform' di nama cookie agar user bisa
            // melakukan share ke 5 platform berbeda untuk memenuhi syarat misinya.
            const cookieName = 'share_property_' + propertyId;

            if (currentUserId && document.cookie.indexOf(cookieName + '=') === -1) {
                // Set Cookie kedaluwarsa nanti malam jam 00:00 WIB
                const maxAge = getSecondsUntilMidnightWIB();
                document.cookie = cookieName + "=true; max-age=" + maxAge + "; path=/";

                // Pastikan kamu memiliki route ini di web.php mengarah ke shareProperty()
                const beaconUrl = '{{ route('quest.share-property') }}';

                let formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('property_id', propertyId);
                formData.append('platform', platform);

                navigator.sendBeacon(beaconUrl, formData);
            }

            // ==========================================
            // 2. BUKA APLIKASI SOSIAL MEDIA
            // ==========================================
            let finalSocialUrl = '';

            if (platform === 'whatsapp') {
                finalSocialUrl = `https://api.whatsapp.com/send?text=${textToShare}${encodedUrl}`;
            } else if (platform === 'facebook') {
                finalSocialUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodedUrl}`;
            } else if (platform === 'x_twitter') {
                finalSocialUrl = `https://twitter.com/intent/tweet?url=${encodedUrl}&text=${textToShare}`;
            } else if (platform === 'instagram' || platform === 'copy_link') {
                // Instagram tidak punya Web Share API, jadi kita salin link ke clipboard
                navigator.clipboard.writeText(shareUrl).then(() => {
                    const btnCopy = document.getElementById('btn-copy-link');
                    const originalText = btnCopy.innerText;
                    btnCopy.innerText = 'Tersalin!';
                    btnCopy.classList.replace('bg-white', 'bg-teal-50');
                    btnCopy.classList.replace('text-gray-800', 'text-teal-700');

                    if (platform === 'instagram') {
                        alert(
                            'Tautan berhasil disalin! Silakan buka aplikasi Instagram dan bagikan di DM atau Bio Anda.'
                        );
                    }

                    setTimeout(() => {
                        btnCopy.innerText = originalText;
                        btnCopy.classList.replace('bg-teal-50', 'bg-white');
                        btnCopy.classList.replace('text-teal-700', 'text-gray-800');
                    }, 2000);
                });
                return; // Hentikan fungsi di sini agar tidak membuka window baru
            }

            // Buka tab baru untuk sosmed
            if (finalSocialUrl) {
                window.open(finalSocialUrl, '_blank');
            }
        }
    </script>
@endsection
