@extends('layouts.index')

@section('styles')
    {{-- Menggunakan Font yang sama dengan Portofolio agar konsisten --}}
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;1,600&display=swap"
        rel="stylesheet">

    {{-- CSS Khusus untuk animasi smooth --}}
    <style>
        .hover-zoom-img:hover img {
            transform: scale(1.1);
        }

        .transition-all-300 {
            transition: all 0.3s ease-in-out;
        }

        .custom-shadow {
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.03);
        }

        /* Shadow halus ala referensi */
    </style>
@endsection

@section('content')
    <div class="bg-[#FAFAFA] min-h-screen font-['Inter']">

        {{-- ================================================================
             1. HEADER & SEARCH SECTION
             ================================================================ --}}
        <div class="relative bg-white pt-16 pb-24 z-30">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                {{-- Header Text Minimalis --}}
                <div class="text-center mb-12">
                    <h1
                        class="font-['Plus_Jakarta_Sans'] text-4xl md:text-5xl lg:text-6xl font-extrabold text-gray-900 leading-tight mb-4 tracking-tight">
                        Find Your <span class="text-[#0d9488] italic">Sanctuary.</span>
                    </h1>
                    <p class="text-gray-500 text-lg font-light">Telusuri ratusan properti eksklusif di lokasi terbaik.</p>
                </div>

                {{-- Search bar --}}
                <div class="max-w-5xl mx-auto relative -mb-36 z-50">
                    <form action="{{ route('shop.index') }}" method="GET" id="searchForm">
                        <div
                            class="bg-white rounded-4xl custom-shadow p-2 border-2 border-gray-300 flex flex-col md:flex-row items-center divide-y md:divide-y-0 md:divide-x divide-gray-100">

                            {{-- 1. Input Lokasi --}}
                            <div
                                class="w-full md:w-5/12 px-6 py-3 hover:bg-teal-50/50 rounded-[2rem] transition group relative">
                                <div class="flex items-center">
                                    <i class="fas fa-map-marker-alt text-[#0d9488] mr-3 text-lg"></i>
                                    <input type="text" name="lokasi" id="inputLokasi"
                                        class="w-full bg-transparent border-none p-0 text-gray-900 placeholder-gray-400 focus:ring-0 focus:outline-none text-sm font-semibold truncate"
                                        placeholder="Pilih kota?" value="{{ request('lokasi') }}" autocomplete="off">

                                    <input type="hidden" name="lat" id="latInput" value="{{ request('lat') }}">
                                    <input type="hidden" name="lng" id="lngInput" value="{{ request('lng') }}">
                                </div>

                                <div id="dropdownLokasi"
                                    class="hidden absolute top-full left-0 w-full md:w-80 mt-4 bg-white rounded-2xl custom-shadow border border-gray-500 overflow-hidden z-50 p-2">
                                    <div id="cityResults" class="flex flex-col">
                                    </div>
                                </div>
                            </div>

                            {{-- 2. CUSTOM DROPDOWN: BUDGET --}}
                            <div
                                class="w-full border md:w-3/12 px-6 py-3 hover:bg-teal-50/50 rounded-[2rem] transition group relative custom-dropdown">
                                <div class="flex items-center cursor-pointer dropdown-trigger">
                                    <i class="fas fa-wallet text-[#0d9488] mr-3 text-lg"></i>
                                    <span class="text-sm font-semibold text-gray-900 truncate selected-text">
                                        @if (request('harga') == '0-500000000')
                                        < Rp 500 Juta @elseif(request('harga') == '500000000-1000000000') 500jt - 1 Miliar
                                            @elseif(request('harga') == '1000000000-2000000000') 1 M - 2 Miliar
                                            @elseif(request('harga') == '2000000000-5000000000') 2 M - 5 Miliar
                                            @elseif(request('harga') == '5000000000-999999999999')> Rp 5 Miliar
                                            @else
                                                Semua Harga
                                        @endif
                                    </span>
                                    <input type="hidden" name="harga" value="{{ request('harga') }}"
                                        class="dropdown-input">
                                </div>

                                {{-- PANEL DROPDOWN --}}
                                <div
                                    class="dropdown-menu hidden absolute top-full left-0 w-64 mt-4 bg-white rounded-2xl custom-shadow border border-gray-500 overflow-hidden z-50 p-2">
                                    <div class="flex flex-col">
                                        <div class="dropdown-item px-4 py-3 hover:bg-[#F0F7F7] rounded-xl cursor-pointer text-sm font-medium text-gray-600 hover:text-[#0d9488] transition"
                                            data-value="">Semua Harga</div>
                                        <div class="dropdown-item px-4 py-3 hover:bg-[#F0F7F7] rounded-xl cursor-pointer text-sm font-medium text-gray-600 hover:text-[#0d9488] transition"
                                            data-value="0-500000000">&lt; Rp 500 Juta</div>
                                        <div class="dropdown-item px-4 py-3 hover:bg-[#F0F7F7] rounded-xl cursor-pointer text-sm font-medium text-gray-600 hover:text-[#0d9488] transition"
                                            data-value="500000000-1000000000">Rp 500 Juta - 1 Miliar</div>
                                        <div class="dropdown-item px-4 py-3 hover:bg-[#F0F7F7] rounded-xl cursor-pointer text-sm font-medium text-gray-600 hover:text-[#0d9488] transition"
                                            data-value="1000000000-2000000000">Rp 1 M - 2 Miliar</div>
                                        <div class="dropdown-item px-4 py-3 hover:bg-[#F0F7F7] rounded-xl cursor-pointer text-sm font-medium text-gray-600 hover:text-[#0d9488] transition"
                                            data-value="2000000000-5000000000">Rp 2 M - 5 Miliar</div>
                                        <div class="dropdown-item px-4 py-3 hover:bg-[#F0F7F7] rounded-xl cursor-pointer text-sm font-medium text-gray-600 hover:text-[#0d9488] transition"
                                            data-value="5000000000-999999999999">&gt; Rp 5 Miliar</div>
                                    </div>
                                </div>
                            </div>

                            {{-- 3. CUSTOM DROPDOWN: TIPE --}}
                            <div
                                class="w-full md:w-3/12 px-6 py-3 hover:bg-teal-50/50 rounded-[2rem] transition group relative custom-dropdown">
                                <div class="flex items-center cursor-pointer dropdown-trigger">
                                    <i class="fas fa-home text-[#0d9488] mr-3 text-lg"></i>
                                    <span class="text-sm font-semibold text-gray-900 truncate selected-text">
                                        {{ request('kategori_slug') ? ucfirst(request('kategori_slug')) : 'Semua Tipe' }}
                                    </span>
                                    <input type="hidden" name="kategori_slug" value="{{ request('kategori_slug') }}"
                                        class="dropdown-input">
                                </div>

                                <div
                                    class="dropdown-menu hidden absolute top-full left-0 w-56 mt-4 bg-white rounded-2xl custom-shadow border border-gray-500 overflow-hidden z-50 p-2">
                                    <div class="flex flex-col">
                                        <div class="dropdown-item px-4 py-3 hover:bg-[#F0F7F7] rounded-xl cursor-pointer text-sm font-medium text-gray-600 hover:text-[#0d9488] transition"
                                            data-value="">Semua Tipe</div>
                                        @foreach ($propertyTypes as $type)
                                            <div class="dropdown-item px-4 py-3 hover:bg-[#F0F7F7] rounded-xl cursor-pointer text-sm font-medium text-gray-600 hover:text-[#0d9488] transition"
                                                data-value="{{ strtolower($type) }}">
                                                {{ ucfirst($type) }}
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            {{-- 4. Tombol Cari --}}
                            <div class="w-full md:w-auto p-2">
                                <button type="submit"
                                    class="w-full md:w-14 md:h-14 rounded-full bg-[#0d9488] text-white shadow-md shadow-teal-700/30 hover:bg-teal-700 transition flex items-center justify-center transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#0d9488]">
                                    <i class="fas fa-search text-lg"></i>
                                    <span class="md:hidden ml-2 font-bold py-3">Cari Sekarang</span>
                                </button>
                            </div>

                        </div>
                    </form>
                </div>

            </div>
        </div>

        {{-- ================================================================
             2. MAIN CONTENT (GRID PROPERTI)
             ================================================================ --}}
        <div class="w-full max-w-[90rem] mx-auto px-4 sm:px-6 lg:px-8 pt-40 pb-20 z-10">

            {{-- Category Pills --}}
            <div class="flex flex-wrap justify-center gap-3 mb-14">
                <a href="{{ route('shop.index') }}"
                    class="px-6 py-2.5 rounded-full text-sm font-bold transition {{ !request('kategori_slug') ? 'bg-[#0d9488] text-white custom-shadow' : 'bg-white text-gray-500 border border-gray-200 hover:border-[#0d9488] hover:text-[#0d9488] hover:bg-teal-50/30' }}">
                    <i class="fas fa-th-large mr-1.5"></i> Semua
                </a>

                @foreach ($propertyTypes as $type)
                    @php
                        $isActive = request('kategori_slug') == strtolower($type);
                        $icon = match (strtolower($type)) {
                            'rumah' => 'fa-home',
                            'apartemen' => 'fa-building',
                            'ruko' => 'fa-store',
                            'kantor' => 'fa-briefcase',
                            'gudang' => 'fa-warehouse',
                            default => 'fa-city',
                        };
                    @endphp
                    <a href="{{ route('shop.index', array_merge(request()->all(), ['kategori_slug' => strtolower($type)])) }}"
                        class="px-6 py-2.5 rounded-full text-sm font-bold transition {{ $isActive ? 'bg-[#0d9488] text-white custom-shadow' : 'bg-white text-gray-500 border border-gray-200 hover:border-[#0d9488] hover:text-[#0d9488] hover:bg-teal-50/30' }}">
                        <i class="fas {{ $icon }} mr-1.5"></i> {{ ucfirst($type) }}
                    </a>
                @endforeach
            </div>

            {{-- 3. GRID PROPERTI --}}
            @if ($allProperties->count() > 0)
                <div id="propertyGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 md:gap-8">
                    @foreach ($allProperties as $property)
                        @include('partials/property/cardProperty', [
                            'item' => $property,
                            'isSearch' => true,
                        ])
                    @endforeach
                </div>

                @if ($allProperties->hasMorePages())
                    <div class="text-center mt-14" id="loadMoreContainer">
                        <button onclick="loadMoreProperties()" id="loadMoreBtn"
                            data-cursor="{{ $allProperties->nextCursor() ? $allProperties->nextCursor()->encode() : '' }}"
                            class="inline-flex items-center justify-center px-8 py-3 bg-white border-black border hover:text-white font-bold rounded-full hover:bg-gray-700 transition font-['Inter']">
                            <span id="btnText">Muat Lebih Banyak</span>
                            <div id="btn-loader"
                                class="hidden ml-2 animate-spin h-4 w-4 border-2 border-[#0d9488] border-t-transparent rounded-full">
                            </div>
                        </button>
                    </div>
                @endif
            @else
                <div class="text-center py-12">
                    <div class="bg-white shadow-sm rounded-xl p-8 inline-block">
                        <p class="text-gray-500 font-bold m-0">Belum ada properti yang tersedia.</p>
                    </div>
                </div>
            @endif

        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // --- 1. LOGIKA UNTUK CUSTOM DROPDOWN ---
            const dropdowns = document.querySelectorAll('.custom-dropdown');

            dropdowns.forEach(dropdown => {
                const trigger = dropdown.querySelector('.dropdown-trigger');
                const menu = dropdown.querySelector('.dropdown-menu');
                const input = dropdown.querySelector('.dropdown-input');
                const selectedText = dropdown.querySelector('.selected-text');
                const items = dropdown.querySelectorAll('.dropdown-item');

                trigger.addEventListener('click', (e) => {
                    e.stopPropagation();
                    dropdowns.forEach(otherDropdown => {
                        if (otherDropdown !== dropdown) {
                            otherDropdown.querySelector('.dropdown-menu').classList.add(
                                'hidden');
                        }
                    });
                    menu.classList.toggle('hidden');
                });

                items.forEach(item => {
                    item.addEventListener('click', (e) => {
                        e.stopPropagation();
                        const value = item.getAttribute('data-value');
                        const text = item.innerText;

                        input.value = value;
                        selectedText.innerText = text;

                        // Highlight item aktif menggunakan warna teal
                        items.forEach(i => i.classList.remove('bg-[#F0F7F7]',
                            'text-dabelyu-accent'));
                        item.classList.add('bg-[#F0F7F7]', 'text-dabelyu-accent');

                        menu.classList.add('hidden');
                    });
                });
            });

            document.addEventListener('click', (e) => {
                dropdowns.forEach(dropdown => {
                    const menu = dropdown.querySelector('.dropdown-menu');
                    if (!menu.classList.contains('hidden')) {
                        menu.classList.add('hidden');
                    }
                });
            });
        });
    </script>
    <script>
        async function loadMoreProperties() {
            const btn = document.getElementById('loadMoreBtn');
            const btnText = document.getElementById('btnText');
            const loader = document.getElementById('btn-loader');
            const container = document.getElementById('loadMoreContainer');
            const grid = document.getElementById('propertyGrid');

            let currentCursor = btn.getAttribute('data-cursor');
            if (!currentCursor) return;

            // UI Feedback
            btn.disabled = true;
            loader.classList.remove('hidden');
            btnText.innerText = 'Memuat...';

            try {
                // Ambil parameter filter yang ada di URL (Kota, Kategori, Harga)[cite: 3]
                const urlParams = new URLSearchParams(window.location.search);
                urlParams.set('cursor', currentCursor);

                // Fetch ke rute index yang sama[cite: 3]
                const response = await fetch(`{{ route('shop.index') }}?${urlParams.toString()}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await response.json();

                if (data.html.trim() !== "") {
                    const tempContainer = document.createElement('div');
                    tempContainer.innerHTML = data.html;

                    // Sinkronisasi status favorit untuk item baru (AWAIT agar warna SVG berubah)[cite: 4]
                    await syncHeartIcons(tempContainer);

                    // Pindahkan elemen asli satu per satu agar status objek (Class/Fill) terjaga[cite: 4]
                    while (tempContainer.firstChild) {
                        grid.appendChild(tempContainer.firstChild);
                    }

                    // Update cursor untuk klik berikutnya[cite: 3]
                    btn.setAttribute('data-cursor', data.nextCursor);
                }

                // Sembunyikan tombol jika sudah tidak ada data lagi[cite: 3]
                if (!data.hasMore || !data.nextCursor) {
                    container.classList.add('hidden');
                }

            } catch (error) {
                console.error("Gagal memuat properti:", error);
            } finally {
                btn.disabled = false;
                loader.classList.add('hidden');
                btnText.innerText = 'Muat Lebih Banyak';
            }
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
        document.addEventListener('DOMContentLoaded', function() {
            const inputLokasi = document.getElementById('inputLokasi');
            const dropdownLokasi = document.getElementById('dropdownLokasi');
            const cityResults = document.getElementById('cityResults');

            let searchTimeout = null;

            // --- FUNGSI UTAMA: PENCARIAN KE API ---
            const handleCitySearch = async (keyword) => {
                // Bersihkan timeout sebelumnya (Debouncing)
                clearTimeout(searchTimeout);

                if (keyword.length <= 2) {
                    cityResults.innerHTML = '';
                    return;
                }

                // Tampilkan Loading State (Gaya Notifikasi)
                cityResults.innerHTML = `
                <div class="p-4 text-center">
                    <div class="inline-block animate-spin h-4 w-4 border-2 border-[#0d9488] border-t-transparent rounded-full mb-2"></div>
                    <p class="text-xs text-gray-500">Mencari kota...</p>
                </div>
            `;
                dropdownLokasi.classList.remove('hidden');

                // Jalankan pencarian setelah user berhenti mengetik selama 600ms
                searchTimeout = setTimeout(async () => {
                    try {
                        const response = await fetch("{{ route('search-cities') }}?q=" +
                            encodeURIComponent(keyword));
                        const cities = await response.json();

                        renderResults(cities, keyword);
                    } catch (error) {
                        console.error("Gagal memuat kota:", error);
                        cityResults.innerHTML =
                            `<p class="px-4 py-3 text-xs text-red-500">Gagal mengambil data.</p>`;
                    }
                }, 600);
            };

            // --- FUNGSI: RENDER HASIL ---
            const renderResults = (cities, keyword) => {
                cityResults.innerHTML = '';

                if (cities.length > 0) {
                    cities.forEach(city => {
                        const btn = document.createElement('button');
                        btn.type = 'button';
                        // Menggunakan gaya hover dan icon yang sama dengan halaman notifikasi
                        btn.className =
                            "flex items-center w-full px-4 py-3 hover:bg-teal-50 rounded-xl cursor-pointer text-sm font-medium text-gray-600 hover:text-[#0d9488] transition text-left focus:outline-none";
                        btn.innerHTML =
                            `<i class="fas fa-map-marker-alt text-gray-300 mr-3 text-xs"></i> ${city.name}`;

                        btn.onclick = function() {
                            inputLokasi.value = city.name;
                            dropdownLokasi.classList.add('hidden');
                            // Opsional: Submit form otomatis setelah pilih kota
                            // document.getElementById('searchForm').submit(); 
                        };

                        cityResults.appendChild(btn);
                    });
                } else {
                    // Tampilan jika tidak ditemukan (Gaya Notifikasi)
                    cityResults.innerHTML = `
                    <div class="px-4 py-6 text-center">
                        <i class="fas fa-search-location text-gray-200 text-2xl mb-2 block"></i>
                        <p class="text-xs text-gray-400 italic">Kota "${keyword}" tidak ditemukan.</p>
                    </div>
                `;
                }
            };

            // --- EVENT LISTENERS ---

            // Saat mengetik
            inputLokasi.addEventListener('input', (e) => {
                handleCitySearch(e.target.value.trim());
            });

            // Saat diklik/fokus (langsung munculkan jika sudah ada teks)
            inputLokasi.addEventListener('focus', () => {
                if (inputLokasi.value.length > 2) {
                    dropdownLokasi.classList.remove('hidden');
                }
            });

            // Tutup jika klik di luar
            document.addEventListener('click', (e) => {
                if (!inputLokasi.contains(e.target) && !dropdownLokasi.contains(e.target)) {
                    dropdownLokasi.classList.add('hidden');
                }
            });
        });
    </script>
@endsection
