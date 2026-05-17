@php
    $property = $isSearch ? $item : $item->property;
    // DETEKSI TIPE PROPERTI
    $tipeRaw = $property->tipe ?? ($property->tipe_bangunan ?? ($property->tipe_properti ?? 'Tanah'));
    $tipe = strtolower(trim($tipeRaw));
    $isBuilding = in_array($tipe, ['rumah', 'apartemen', 'ruko', 'kantor', 'gudang', 'villa']);

    $detailUrl = route('home.property-details', $property->slug ?? 1);

    // DETEKSI GAMBAR
    $mainImage = asset('storage/' . $property->mainImage->image_path . '-image_low.webp');

    // STATUS TRANSAKSI
    $transaksi = $property->status ?? ($property->transaksi ?? 'Dijual');
    $badgeColor = strtolower(trim($transaksi)) == 'disewa' ? 'bg-blue-500' : 'bg-rose-500';
@endphp

<div
    class="group bg-white rounded-2xl overflow-hidden shadow-[0_2px_12px_rgba(0,0,0,0.04)] hover:shadow-[0_20px_40px_rgba(0,0,0,0.08)] transition-all duration-300 hover:-translate-y-1.5 border border-gray-100 font-['Inter'] flex flex-col h-full relative">

    {{-- AREA GAMBAR --}}
    <div class="relative w-full aspect-4/3 bg-gray-200 overflow-hidden shrink-0 flex items-center justify-center">
        <i class="fas fa-image text-gray-300 text-4xl absolute z-0"></i>


        {{-- Badge Transaksi --}}
        <div class="absolute top-3 left-3 z-20">
            <span
                class="px-3 py-1.5 {{ $badgeColor }} text-white text-[10px] font-extrabold uppercase tracking-wider rounded-lg shadow-md">
                {{ strtoupper($transaksi) }}
            </span>
        </div>

        {{-- Tombol Favorit --}}
        <button type="button"
            class="btn-favorite absolute top-3 right-3 z-20 w-9 h-9 flex items-center justify-center rounded-full bg-white/90 backdrop-blur-sm shadow-sm transition-all duration-300 focus:outline-none hover:bg-rose-50 hover:scale-110"
            onclick="toggleFavorite(this); event.preventDefault();" data-id="{{ $property->id }}"
            title="Tambahkan ke Favorit">
            <svg class="heart-{{ $property->id }} heart-icon w-5 h-5 text-gray-500 group-hover:text-rose-500 transition-colors"
                fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
            </svg>
        </button>

        {{-- Gambar Utama --}}
        <a href="{{ $detailUrl }}" class="absolute inset-0 block z-10 bg-gray-200/50">
            <img src="{{ $mainImage }}" alt="Property Image" onerror="this.style.display='none'"
                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
        </a>

        {{-- Gradient Overlay Bawah --}}
        <div
            class="absolute bottom-0 left-0 w-full p-3 bg-gradient-to-t from-gray-900/80 to-transparent flex justify-between items-end z-20 pointer-events-none">
            <span
                class="flex items-center gap-1.5 px-2.5 py-1 bg-white/95 backdrop-blur-sm text-[#0d9488] text-[10px] font-extrabold uppercase tracking-wider rounded-md shadow-sm">
                @if ($isBuilding)
                    @if (str_contains($tipe, 'rumah'))
                        <i class="fas fa-home"></i>
                    @elseif(str_contains($tipe, 'apartemen'))
                        <i class="far fa-building"></i>
                    @else
                        <i class="fas fa-building"></i>
                    @endif
                @else
                    <i class="fas fa-map-marked-alt"></i>
                @endif
                {{ ucfirst(trim($tipeRaw)) }}
            </span>
        </div>
    </div>

    {{-- AREA KONTEN --}}
    <div class="p-5 flex flex-col grow bg-white">
        <h4 class="font-['Plus_Jakarta_Sans'] text-xl font-extrabold text-[#0d9488] mb-2 tracking-tight">
            Rp
            {{ is_numeric(str_replace(['Rp', '.', ',', ' '], '', $property->harga)) ? number_format((float) str_replace(['Rp', '.', ',', ' '], '', $property->harga), 0, ',', '.') : $property->harga }}
        </h4>

        <a href="{{ $detailUrl }}"
            class="block text-sm font-bold text-gray-800 line-clamp-2 mb-3 hover:text-[#0d9488] transition-colors no-underline min-h-[40px]">
            {{ $property->judul }}
        </a>

        <div class="flex items-start text-xs text-gray-500 mb-5 mt-auto">
            <i class="fas fa-map-marker-alt text-gray-400 mt-0.5 mr-2 shrink-0"></i>
            <span class="line-clamp-2 leading-relaxed">{{ $property->kota ?? 'Lokasi tidak diketahui' }}</span>
        </div>

        {{-- SPESIFIKASI PROPERTI --}}
        <div class="pt-4 border-t border-gray-100 mt-auto">
            @if ($isBuilding)
                <div class="flex items-center justify-between text-xs text-gray-600 font-medium">
                    <div class="flex items-center gap-2 bg-gray-50 px-2.5 py-1.5 rounded-lg border border-gray-100">
                        <i class="fas fa-bed text-gray-400"></i>
                        <span
                            class="font-bold text-gray-800">{{ $property->jumlah_kamar_tidur ?? ($property->kamar_tidur ?? 0) }}</span>
                    </div>
                    <div class="flex items-center gap-2 bg-gray-50 px-2.5 py-1.5 rounded-lg border border-gray-100">
                        <i class="fas fa-bath text-gray-400"></i>
                        <span
                            class="font-bold text-gray-800">{{ $property->jumlah_kamar_mandi ?? ($property->kamar_mandi ?? 0) }}</span>
                    </div>
                    <div class="flex items-center gap-2 bg-gray-50 px-2.5 py-1.5 rounded-lg border border-gray-100">
                        <i class="fas fa-ruler-combined text-gray-400"></i>
                        <span
                            class="font-bold text-gray-800">{{ $property->luas_bangunan ?? ($property->luas_tanah ?? 0) }}
                            <span class="text-[10px] font-normal">m²</span></span>
                    </div>
                </div>
            @else
                <div class="flex items-center gap-2 bg-gray-50 px-3 py-2 rounded-lg border border-gray-100 w-fit">
                    <i class="fas fa-ruler-combined text-gray-400"></i>
                    <span class="text-xs text-gray-500">Luas Lahan: <span
                            class="font-bold text-gray-800">{{ $property->luas_tanah ?? 0 }}
                            m²</span></span>
                </div>
            @endif
        </div>
    </div>
</div>