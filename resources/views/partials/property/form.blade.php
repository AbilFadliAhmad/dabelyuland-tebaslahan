@extends($user->role == 'admin' ? 'layouts.admin' : 'layouts.user')

@section('style')
    <style>
        /* MODE FOKUS (SEMBUNYIKAN SIDEBAR BAWAAN) */
        #sidebar {
            display: none !important;
        }

        #main-wrapper {
            padding-left: 0 !important;
        }

        header {
            display: none !important;
        }

        body {
            background-color: #f8fafc;
        }

        /* WIZARD & STEPPER STYLING */
        .stepper-wrapper {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2.5rem;
            position: relative;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }

        .stepper-wrapper::before {
            content: "";
            position: absolute;
            top: 35%;
            left: 0;
            right: 0;
            height: 2px;
            background-color: #e2e8f0;
            z-index: 1;
        }

        .step-indicator {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 25%;
        }

        .step-circle {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background-color: #fff;
            border: 2px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: #94a3b8;
            transition: all 0.3s ease;
        }

        .step-title {
            margin-top: 0.75rem;
            font-size: 0.85rem;
            font-weight: 600;
            color: #64748b;
            transition: all 0.3s ease;
        }

        .step-indicator.active .step-circle {
            background-color: #0d9488;
            border-color: #0d9488;
            color: #fff;
            box-shadow: 0 0 0 4px rgba(13, 148, 136, 0.2);
        }

        .step-indicator.active .step-title {
            color: #0d9488;
        }

        .step-indicator.completed .step-circle {
            background-color: #10b981;
            border-color: #10b981;
            color: #fff;
        }

        .step-indicator.completed .step-title {
            color: #10b981;
        }

        .wizard-step {
            display: none;
            animation: fadeIn 0.4s ease;
        }

        .wizard-step.active {
            display: block;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-input:focus,
        .form-select:focus {
            outline: none;
            border-color: #0d9488;
            box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.15);
        }
    </style>
@endsection

@section('content')
    @php
        // LOGIKA CERDAS: Menentukan tombol Batal akan kembali ke tabel mana
        $isAdmin = Auth::user()->role == 'admin';
        if ($typeProperty == 'land') {
            $backRoute = $isAdmin ? route('admin.lands.index') : route('user.lands.index');
        } else {
            $backRoute = $isAdmin ? route('admin.buildings.index') : route('user.buildings.index');
        }
    @endphp

    <div class="w-full min-h-screen px-4 sm:px-6 lg:px-8 py-8 lg:py-12 font-['Inter']">

        {{-- Header Batal / Keluar --}}
        <div class="flex justify-between items-center mb-8 max-w-[900px] mx-auto">
            <div>
                <h3 class="text-2xl font-extrabold text-gray-900 m-0 tracking-tight font-['Plus_Jakarta_Sans']">
                    {{ $isEdit ? 'Edit' : 'Tambah' }} {{ $typeProperty == 'land' ? 'Tanah' : 'Bangunan' }}
                </h3>
                <p class="text-gray-500 text-sm m-0 mt-1">Lengkapi form secara bertahap untuk hasil maksimal.</p>
            </div>
            <a href="{{ $backRoute }}"
                class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-600 text-sm font-bold rounded-xl transition-colors no-underline shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
                Batal
            </a>
        </div>

        {{-- STEPPER INDICATOR (4 Langkah) --}}
        <div class="stepper-wrapper">
            <div class="step-indicator active" id="indicator-1">
                <div class="step-circle">1</div>
                <div class="step-title">Data Dasar</div>
            </div>
            <div class="step-indicator" id="indicator-2">
                <div class="step-circle">2</div>
                <div class="step-title">Spesifikasi</div>
            </div>
            <div class="step-indicator" id="indicator-3">
                <div class="step-circle">3</div>
                <div class="step-title">Media</div>
            </div>
            <div class="step-indicator" id="indicator-4">
                <div class="step-circle"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg></div>
                <div class="step-title">Preview</div>
            </div>
        </div>

        {{-- KOTAK FORM WIZARD UTAMA --}}
        <div
            class="max-w-225 mx-auto bg-white rounded-3xl shadow-[0_4px_25px_rgba(0,0,0,0.04)] border border-gray-100 overflow-hidden">
            <div class="p-6 md:p-10">
                <form action="{{ route($isEdit ? 'user.property.update' : 'user.property.store') }}" method="POST"
                    id="wizardForm">
                    @csrf
                    <input name="id" type="hidden" id="propertyId" value="{{ $propertyId ?? '' }}">
                    <input name="typeProperty" type="hidden" value="{{ $typeProperty }}">

                    {{-- ========================================== --}}
                    {{-- STEP 1: DATA DASAR PROPERTI                --}}
                    {{-- ========================================== --}}
                    <div class="wizard-step" id="step-1">
                        <div class="mb-6 pb-4 border-b border-gray-100">
                            <h5 class="text-lg font-bold text-gray-800 m-0">Informasi Utama</h5>
                            <p class="text-xs text-gray-500 m-0 mt-1">Masukkan judul yang menarik dan harga penawaran yang
                                sesuai.</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Judul Iklan <span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="judul" id="input-judul"
                                    value="{{ old('judul', $property->judul ?? '') }}"
                                    class="form-input-step w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm transition-all focus:ring-2 focus:ring-teal-500"
                                    placeholder="Contoh: Rumah Minimalis Strategis Siap Huni" required>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Harga (Rp) <span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="harga" id="input-harga"
                                    value="{{ old('harga', $property->harga ?? '') }}"
                                    class="form-input-step w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm transition-all focus:ring-2 focus:ring-teal-500"
                                    placeholder="Misal: 450.000.000" required>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Jenis Transaksi <span
                                        class="text-red-500">*</span></label>
                                <select name="transaksi" id="input-transaksi"
                                    class="form-input-step w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm transition-all focus:ring-2 focus:ring-teal-500"
                                    required>
                                    <option value="" disabled selected hidden>Pilih Transaksi</option>
                                    <option value="Dijual"
                                        {{ old('transaksi', $property->transaksi ?? '') == 'Dijual' ? 'selected' : '' }}>
                                        Dijual</option>
                                    <option value="Disewa"
                                        {{ old('transaksi', $property->transaksi ?? '') == 'Disewa' ? 'selected' : '' }}>
                                        Disewa</option>
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Tipe Properti <span
                                        class="text-red-500">*</span></label>
                                <select name="tipe" id="input-tipe"
                                    class="form-input-step w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm transition-all focus:ring-2 focus:ring-teal-500"
                                    required>
                                    <option value="" disabled selected hidden>Pilih Tipe</option>
                                    @if ($typeProperty == 'land')
                                        <option value="tanah"
                                            {{ old('tipe', $property->tipe ?? '') == 'tanah' ? 'selected' : '' }}>
                                            Tanah
                                        </option>
                                    @else
                                        @foreach (['apartemen', 'rumah', 'ruko', 'kantor', 'gudang'] as $opt)
                                            <option value="{{ $opt }}"
                                                {{ old('tipe', $property->tipe ?? '') == $opt ? 'selected' : '' }}>
                                                {{ ucfirst($opt) }}</option>
                                        @endforeach
                                    @endif

                                </select>
                            </div>
                        </div>

                        <div class="flex justify-end mt-8 pt-5 border-t border-gray-100">
                            <button type="button" id="btn-next-1" onclick="nextStep(1)" disabled
                                class="inline-flex items-center px-6 py-3 bg-[#0d9488] hover:bg-teal-700 disabled:bg-gray-300 disabled:opacity-60 disabled:cursor-not-allowed text-white text-sm font-bold rounded-xl transition-all shadow-md hover:-translate-y-0.5">
                                Lanjut ke Spesifikasi
                                <svg class="w-4 h-4 ml-2" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- ========================================== --}}
                    {{-- STEP 2: SPESIFIKASI & LOKASI               --}}
                    {{-- ========================================== --}}
                    <div class="wizard-step active" id="step-2">
                        <div class="mb-6 pb-4 border-b border-gray-100">
                            <h5 class="text-lg font-bold text-gray-800 m-0">Spesifikasi Detail & Lokasi</h5>
                            <p class="text-xs text-gray-500 m-0 mt-1">Lengkapi informasi ukuran, ruangan, dan titik lokasi
                                properti.</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-8">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Kategori/Lantai <span
                                        class="text-red-500">*</span></label>
                                <select name="kategori" id="input-kategori"
                                    class="validate-step-2 form-select w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm transition-all"
                                    required>
                                    <option value="" disabled selected hidden>Pilih Kategori</option>
                                    @foreach (['3 Lantai', '2 Lantai', '1 Lantai', 'Tanah Kosong', 'Sawah', 'Kebun', 'Lainnya'] as $opt)
                                        <option value="{{ $opt }}"
                                            {{ old('kategori', $property->kategori ?? '') == $opt ? 'selected' : '' }}>
                                            {{ $opt }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Legalitas <span
                                        class="text-red-500">*</span></label>
                                <select name="legalitas" id="input-legalitas"
                                    class="validate-step-2 form-select w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm transition-all"
                                    required>
                                    <option value="" disabled selected hidden>Pilih Legalitas</option>
                                    @foreach (['SHM', 'HGB', 'HP', 'SHMSRS', 'HGU', 'HPL', 'Girik', 'Petok_D', 'Letter_C', 'Eigendom', 'Sultan_Ground', 'AJB', 'PPJB', 'Lainnya'] as $opt)
                                        <option value="{{ $opt }}"
                                            {{ old('legalitas', $property->legalitas ?? '') == $opt ? 'selected' : '' }}>
                                            {{ str_replace('_', ' ', $opt) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Spesifikasi dasar luas --}}
                        <div
                            class="grid grid-cols-1 md:grid-cols-4 gap-5 mb-8 bg-blue-50/50 p-5 rounded-2xl border border-blue-100/50">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Luas Tanah (m²) <span
                                        class="text-red-500">*</span></label>
                                <input type="number" step="0.01" name="luas_tanah" id="input-luas"
                                    value="{{ old('luas_tanah', $property->luas_tanah ?? '') }}"
                                    class="validate-step-2 form-input w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm transition-all"
                                    required>
                            </div>

                            @php
                                $isBuilding = $typeProperty == 'building';
                            @endphp

                            {{-- Luas Bangunan --}}
                            <div class="spec-field {{ !$isBuilding ? 'opacity-40' : '' }}">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Luas Bangunan (m²)</label>
                                <input type="number" step="0.01" name="luas_bangunan" id="input-luas-bangunan"
                                    value="{{ $isBuilding ? old('luas_bangunan', $property->luas_bangunan ?? '') : '0' }}"
                                    class="validate-step-2 form-input disabled:cursor-not-allowed w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm transition-all"
                                    {{ !$isBuilding ? 'disabled' : 'required' }}>
                            </div>

                            {{-- Kamar Tidur --}}
                            <div class="spec-field {{ !$isBuilding ? 'opacity-40' : '' }}">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Kamar Tidur</label>
                                <input type="number" name="jumlah_kamar_tidur" id="input-kt"
                                    value="{{ $isBuilding ? old('jumlah_kamar_tidur', $property->jumlah_kamar_tidur ?? '') : '0' }}"
                                    class="validate-step-2 form-input disabled:cursor-not-allowed w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm transition-all"
                                    {{ !$isBuilding ? 'disabled' : '' }}>
                            </div>

                            {{-- Kamar Mandi --}}
                            <div class="spec-field {{ !$isBuilding ? 'opacity-40' : '' }}">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Kamar Mandi</label>
                                <input type="number" name="jumlah_kamar_mandi" id="input-km"
                                    value="{{ $isBuilding ? old('jumlah_kamar_mandi', $property->jumlah_kamar_mandi ?? '') : '0' }}"
                                    class="validate-step-2 form-input disabled:cursor-not-allowed w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm transition-all"
                                    {{ !$isBuilding ? 'disabled' : '' }}>
                            </div>
                        </div>

                        {{-- Kotak Lokasi --}}
                        <div class="p-5 bg-gray-50 border border-gray-200 rounded-xl mb-6">
                            <h5 class="text-gray-800 border-b border-gray-200 pb-3 mb-4 font-bold text-lg">
                                <i class="bi bi-geo-alt-fill mr-2 text-red-500"></i>Lokasi
                            </h5>

                            {{-- Input hidden tetap ada sesuai struktur asli --}}
                            <input type="hidden" value="{{ old('kota', $property->kota ?? '') }}" id="kota"
                                name="kota">

                            <div class="mb-4 relative">
                                <div class="flex justify-between items-end mb-2">
                                    <label class="block font-bold text-gray-600 text-sm">Titik Lokasi Peta</label>
                                    <button type="button" id="btnFindMe" onclick="findMyLocation()"
                                        class="flex items-center text-xs px-3 py-1.5 bg-[#f2f7ff] text-[#0f636d] hover:bg-[#0f636d] hover:text-white border border-[#0f636d] rounded-md transition-colors font-semibold">
                                        <i class="bi bi-crosshair mr-1.5"></i> Temukan Lokasi Saya
                                    </button>
                                </div>

                                <input type="hidden" id="latitude" name="latitude" class="validate-step-2"
                                    value="{{ old('latitude', $property->latitude ?? '') }}">
                                <input type="hidden" id="longitude" name="longitude" class="validate-step-2"
                                    value="{{ old('longitude', $property->longitude ?? '') }}">

                                <div class="mb-2 relative z-[1000]">
                                    <div class="relative">
                                        <i
                                            class="bi bi-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                        <input type="text" name="alamat_detail"
                                            value="{{ old('alamat_detail', $property->alamat_detail ?? '') }}"
                                            id="azureSearchInput" placeholder="Cari nama jalan atau daerah..."
                                            class="validate-step-2 w-full pl-10 pr-4 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:border-[#0f636d] focus:ring-1 focus:ring-[#0f636d]">
                                    </div>
                                    <ul id="azureSearchResults"
                                        class="hidden absolute w-full mt-1 bg-white border border-gray-200 rounded-md shadow-lg max-h-48 overflow-y-auto">
                                    </ul>
                                </div>

                                <div id="map"
                                    class="w-full h-[300px] rounded-lg border border-gray-300 z-10 relative"></div>
                                <p class="text-xs text-gray-500 mt-2">
                                    <i class="bi bi-info-circle mr-1"></i> Klik peta atau geser pin/marker biru untuk
                                    menyesuaikan titik lokasi.
                                </p>
                            </div>
                        </div>

                        {{-- Button --}}
                        <div class="flex justify-between mt-8 pt-5 border-t border-gray-100">
                            <button type="button" onclick="prevStep(2)"
                                class="inline-flex items-center px-6 py-3 bg-white border border-gray-200 hover:bg-gray-50 text-gray-600 text-sm font-bold rounded-xl transition-all shadow-sm">
                                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                                </svg> Sebelumnya
                            </button>
                            <button disabled type="button" id="btn-next-step-2" onclick="nextStep(2)"
                                class="inline-flex items-center px-6 py-3 bg-[#0d9488] hover:bg-teal-700 disabled:bg-gray-300 disabled:opacity-60 disabled:cursor-not-allowed text-white text-sm font-bold rounded-xl transition-all shadow-md hover:-translate-y-0.5">
                                Lanjut ke Media <svg class="w-4 h-4 ml-2" fill="none" viewBox="0 0 24 24"
                                    stroke-width="2.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- ========================================== --}}
                    {{-- STEP 3: MEDIA FOTO & DESKRIPSI             --}}
                    {{-- ========================================== --}}
                    <div class="wizard-step" id="step-3">
                        <div class="mb-6 pb-4 border-b border-gray-100">
                            <h5 class="text-lg font-bold text-gray-800 m-0">Media & Deskripsi Properti</h5>
                            <p class="text-xs text-gray-500 m-0 mt-1">Unggah foto (bisa pilih banyak sekaligus) dan
                                ceritakan kelebihan properti Anda.</p>
                        </div>

                        {{-- Galeri Properti --}}
                        <div class="mb-8">
                            <label class="block font-bold text-gray-600 text-sm mb-2"><i
                                    class="bi bi-images mr-2"></i>Upload Gambar Properti (Maksimal 10
                                Foto)</label>

                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-2">
                                @for ($i = 1; $i <= 10; $i++)
                                    {{-- Variable untuk menampung path gambar --}}
                                    @php
                                        $imagePath =
                                            old('temp_preview_' . $i) ??
                                            ($images->firstWhere('sort', $i)->image_path ?? null);
                                    @endphp

                                    {{-- Variable untuk menampung gambar --}}
                                    <input type="hidden" value="{{ old('temp_preview_' . $i) ?? $imagePath }}"
                                        name="temp_preview_{{ $i }}" id="temp_preview_{{ $i }}" />

                                    <div>
                                        <label for="gambar_{{ $i }}"
                                            class="relative block w-full aspect-square h-full border-2 border-dashed border-gray-300 rounded-lg overflow-hidden cursor-pointer hover:bg-gray-50 transition-colors bg-white">

                                            {{-- Letakkan ini di dalam label gambar --}}
                                            <button type="button" onclick="deleteImage(event, {{ $i }},)"
                                                id="btn_delete_{{ $i }}"
                                                class="absolute top-0 right-0 m-2 w-7 h-7 bg-rose-500 text-white rounded-full flex items-center justify-center z-30 shadow-md hover:bg-rose-700 transition-transform active:scale-90 {{ $imagePath ? '' : 'hidden' }}">
                                                <i class="bi bi-trash-fill text-xs"></i>
                                            </button>

                                            {{-- PENANDA UNIK UNTUK GAMBAR UTAMA --}}
                                            @if ($i == 1)
                                                <span
                                                    class="absolute top-0 left-0 m-2 px-2 py-1 bg-green-500 text-white text-[10px] font-bold rounded shadow-sm z-20">
                                                    <i class="bi bi-star-fill mr-1 text-yellow-300"></i> Sampul
                                                    Utama
                                                </span>
                                            @else
                                                <span
                                                    class="absolute top-0 left-0 m-2 px-2 py-1 bg-gray-600/80 text-white text-[10px] font-bold rounded z-20">
                                                    Foto {{ $i }}
                                                </span>
                                            @endif

                                            {{-- Gambar --}}
                                            <img id="preview_{{ $i }}"
                                                class="absolute top-0 left-0 w-full h-full object-cover z-10 {{ $imagePath ? '' : 'hidden' }}"
                                                alt="Preview {{ $i }}"
                                                src="{{ $imagePath ? Storage::url($imagePath) . '-image_low.webp' : null }}">

                                            {{-- Placeholder Kosong --}}
                                            <div class="{{ $imagePath ? 'hidden' : 'flex' }} flex-col items-center justify-center h-full text-gray-400 z-0"
                                                id="placeholder_{{ $i }}">
                                                <i class="bi bi-cloud-arrow-up text-4xl mb-1"></i>
                                            </div>

                                            {{-- Loading State --}}
                                            <div id="loading_{{ $i }}"
                                                class="absolute top-0 left-0 w-full h-full bg-white/75 hidden flex-col items-center justify-center z-30">
                                                {{-- Spinner CSS Tailwind --}}
                                                <div
                                                    class="w-8 h-8 border-4 border-[#0f636d] border-t-transparent rounded-full animate-spin mb-2">
                                                </div>
                                                <span class="text-xs text-[#0f636d] font-bold">Mengunggah...</span>
                                            </div>
                                        </label>

                                        <input id="gambar_{{ $i }}" type="file" class="hidden"
                                            accept="image/*" onchange="handleImageUpload(this, {{ $i }})" />
                                    </div>
                                @endfor
                            </div>
                        </div>

                        {{-- Modal Cropper --}}
                        <div id="cropModal"
                            class="fixed inset-0 z-50 hidden bg-black/80 flex items-center justify-center p-4">
                            <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl overflow-hidden">
                                <div class="p-4 border-b flex justify-between items-center">
                                    <h3 class="text-lg font-bold text-gray-800">Sesuaikan Gambar</h3>
                                    <button type="button" onclick="closeCropModal()"
                                        class="text-gray-500 hover:text-red-500">
                                        <i class="bi bi-x-lg text-xl"></i>
                                    </button>
                                </div>
                                <div class="p-4 bg-gray-100">
                                    {{-- Container untuk membatasi tinggi cropper --}}
                                    <div class="w-full max-h-[60vh] flex justify-center">
                                        <img id="imageToCrop" class="max-w-full block" src=""
                                            alt="Gambar untuk dicrop">
                                    </div>
                                </div>
                                <div class="p-4 border-t flex justify-end gap-3">
                                    <button type="button" onclick="closeCropModal()"
                                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 font-medium transition">Batal</button>
                                    <button type="button" onclick="processAndUpload()"
                                        class="px-4 py-2 bg-[#0f636d] text-white rounded hover:bg-[#0c4e56] font-medium transition flex items-center">
                                        <i class="bi bi-cloud-arrow-up mr-2"></i> Potong & Unggah
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Deskripsi Promosi <span
                                    class="text-red-500">*</span></label>
                            <textarea id="default" name="deskripsi" class="w-full">{{ old('deskripsi', $property->deskripsi ?? '') }}</textarea>
                        </div>

                        <div class="flex justify-between mt-8 pt-5 border-t border-gray-100">
                            <button type="button" onclick="prevStep(3)"
                                class="inline-flex items-center px-6 py-3 bg-white border border-gray-200 hover:bg-gray-50 text-gray-600 text-sm font-bold rounded-xl transition-all shadow-sm">
                                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                                </svg> Sebelumnya
                            </button>
                            <button type="button" onclick="nextStep(3)"
                                class="inline-flex items-center px-6 py-3 bg-[#0d9488] hover:bg-teal-700 text-white text-sm font-bold rounded-xl transition-all shadow-md hover:-translate-y-0.5">
                                Cek Preview <svg class="w-4 h-4 ml-2" fill="none" viewBox="0 0 24 24"
                                    stroke-width="2.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- ========================================== --}}
                    {{-- STEP 4: PREVIEW (READ ONLY)                --}}
                    {{-- ========================================== --}}
                    <div class="wizard-step" id="step-4">
                        <div class="mb-6 pb-4 border-b border-gray-100">
                            <h5 class="text-lg font-bold text-gray-800 m-0">Pratinjau Properti</h5>
                            <p class="text-xs text-gray-500 m-0 mt-1">Pastikan semua data sudah benar sebelum
                                dipublikasikan.</p>
                        </div>

                        <div class="bg-white rounded-2xl overflow-hidden border border-gray-200 shadow-sm mb-8 p-4 md:p-6">

                            {{-- Galeri Pratinjau --}}
                            <div id="prev-gallery-container"
                                class="flex flex-col md:flex-row gap-2 md:gap-4 mb-8 rounded-xl md:rounded-[24px] overflow-hidden h-[300px] md:h-[450px] bg-gray-50">
                                {{-- <img id="preview-image" alt="preview image"> --}}
                            </div>

                            <div class="mb-8 border-b border-gray-100 pb-6">
                                <div class="flex items-center text-gray-500 text-sm mb-3 font-medium">
                                    <i class="fas fa-map-marker-alt text-[#0d9488] mr-2 text-lg"></i>
                                    <span id="prev-lokasi">LOKASI</span>
                                </div>
                                <h1 id="prev-judul"
                                    class="font-heading text-2xl md:text-3xl font-extrabold text-gray-900 leading-tight mb-4">
                                    Judul Properti
                                </h1>
                                <div class="flex items-center justify-between">
                                    <h2 id="prev-harga"
                                        class="font-heading text-2xl md:text-3xl font-extrabold text-[#0d9488] m-0">Rp 0
                                    </h2>
                                    <span id="prev-transaksi"
                                        class="px-3 py-1 bg-green-100 text-green-700 text-xs font-bold uppercase rounded-md shadow-sm">Status</span>
                                </div>
                            </div>

                            <div class="flex flex-wrap gap-3 md:gap-4 border border-gray-100 bg-gray-50 p-4 rounded-xl mb-8"
                                id="prev-specs-container">
                                <div
                                    class="flex items-center bg-white px-4 py-2 rounded-lg shadow-sm border border-gray-100">
                                    <i class="fas fa-home text-gray-400 text-lg mr-3"></i>
                                    <div class="flex flex-col">
                                        <span
                                            class="text-[10px] text-gray-400 font-bold uppercase tracking-wide">Tipe</span>
                                        <span id="prev-tipe" class="font-bold text-gray-800">-</span>
                                    </div>
                                </div>
                                <div
                                    class="flex items-center bg-white px-4 py-2 rounded-lg shadow-sm border border-gray-100">
                                    <i class="fas fa-ruler-combined text-gray-400 text-lg mr-3"></i>
                                    <div class="flex flex-col">
                                        <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wide">L.
                                            Tanah</span>
                                        <span id="prev-luas" class="font-bold text-gray-800">- m²</span>
                                    </div>
                                </div>
                                <div
                                    class="flex items-center bg-white px-4 py-2 rounded-lg shadow-sm border border-gray-100">
                                    <i class="fas fa-file-contract text-gray-400 text-lg mr-3"></i>
                                    <div class="flex flex-col">
                                        <span
                                            class="text-[10px] text-gray-400 font-bold uppercase tracking-wide">Legalitas</span>
                                        <span id="prev-legalitas" class="font-bold text-gray-800">-</span>
                                    </div>
                                </div>

                                @if ($typeProperty == 'building')
                                    <div
                                        class="flex items-center bg-white px-4 py-2 rounded-lg shadow-sm border border-gray-100">
                                        <i class="fas fa-ruler-horizontal text-gray-400 text-lg mr-3"></i>
                                        <div class="flex flex-col">
                                            <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wide">L.
                                                Bangunan</span>
                                            <span id="prev-luas-bangunan" class="font-bold text-gray-800">- m²</span>
                                        </div>
                                    </div>
                                    <div
                                        class="flex items-center bg-white px-4 py-2 rounded-lg shadow-sm border border-gray-100">
                                        <i class="fas fa-bed text-gray-400 text-lg mr-3"></i>
                                        <div class="flex flex-col">
                                            <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wide">K.
                                                Tidur</span>
                                            <span id="prev-kt" class="font-bold text-gray-800">-</span>
                                        </div>
                                    </div>
                                    <div
                                        class="flex items-center bg-white px-4 py-2 rounded-lg shadow-sm border border-gray-100">
                                        <i class="fas fa-bath text-gray-400 text-lg mr-3"></i>
                                        <div class="flex flex-col">
                                            <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wide">K.
                                                Mandi</span>
                                            <span id="prev-km" class="font-bold text-gray-800">-</span>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div>
                                <h3 class="font-heading text-xl font-bold text-gray-900 mb-4">Tentang Properti Ini</h3>
                                <div id="prev-deskripsi"
                                    class="prose prose-sm max-w-none text-gray-600 leading-relaxed text-justify">
                                    -- Isi deskripsi --
                                </div>
                            </div>

                        </div>

                        <div class="flex justify-between mt-8 pt-5 border-t border-gray-100">
                            <button type="button" onclick="prevStep(4)"
                                class="inline-flex items-center px-6 py-3 bg-white border border-gray-200 hover:bg-gray-50 text-gray-600 text-sm font-bold rounded-xl transition-all shadow-sm">
                                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                                </svg> Edit Kembali
                            </button>
                            <button type="submit" id="btn-submit"
                                class="inline-flex items-center px-8 py-3 bg-[#10b981] hover:bg-emerald-600 text-white text-sm font-bold rounded-xl transition-all shadow-lg shadow-emerald-500/30 hover:-translate-y-0.5">
                                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg> Simpan & Publikasikan
                            </button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ asset('assets/vendors/tinymce/tinymce.min.js') }}"></script>
    {{-- Script untuk menangani logika kategori --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tipeSelect = document.getElementById('input-tipe');
            const kategoriSelect = document.getElementById('input-kategori');
            const legalitasSelect = document.getElementById('input-legalitas');

            // 1. Data Mapping untuk Kategori & Legalitas
            const dataMapping = {
                'apartemen': {
                    kategori: ['1 Lantai', '2 Lantai', '3 Lantai', 'Lainnya'],
                    legalitas: ['SHMSRS', 'HGB', 'PPJB', 'AJB', 'Lainnya']
                },
                'tanah': {
                    kategori: ['Tanah Kosong', 'Sawah', 'Kebun', 'Lainnya'],
                    legalitas: ['SHM', 'HGB', 'HGU', 'HPL', 'Girik', 'Petok_D', 'Letter_C', 'Eigendom',
                        'Sultan_Ground', 'AJB', 'Lainnya'
                    ]
                },
                'default': { // Untuk Rumah, Ruko, Kantor, Gudang
                    kategori: ['1 Lantai', '2 Lantai', '3 Lantai', 'Lainnya'],
                    legalitas: ['SHM', 'HGB', 'AJB', 'Petok_D', 'Letter_C', 'Girik', 'HP', 'Lainnya']
                }
            };

            // 2. Fungsi untuk memperbarui dropdown
            function updateDropdowns(selectedTipe, isInitial = false) {
                // Tentukan key mapping
                let key = selectedTipe;
                if (selectedTipe !== 'apartemen' && selectedTipe !== 'tanah') {
                    key = 'default';
                }

                const data = dataMapping[key];

                // Kosongkan dan isi Kategori
                kategoriSelect.innerHTML = '<option value="" disabled selected hidden>Pilih Kategori</option>';
                data.kategori.forEach(opt => {
                    const el = document.createElement('option');
                    el.value = opt;
                    el.textContent = opt;
                    // Jika initial (edit mode), pertahankan nilai lama jika cocok
                    if (isInitial && opt === "{{ old('kategori', $property->kategori ?? '') }}") el
                        .selected = true;
                    kategoriSelect.appendChild(el);
                });

                // Kosongkan dan isi Legalitas
                legalitasSelect.innerHTML = '<option value="" disabled selected hidden>Pilih Legalitas</option>';
                data.legalitas.forEach(opt => {
                    const el = document.createElement('option');
                    el.value = opt;
                    el.textContent = opt.replace('_', ' ');
                    if (isInitial && opt === "{{ old('legalitas', $property->legalitas ?? '') }}") el
                        .selected = true;
                    legalitasSelect.appendChild(el);
                });

                // Jika user mengubah tipe secara manual (bukan saat load), reset ke null
                if (!isInitial) {
                    kategoriSelect.value = "";
                    legalitasSelect.value = "";
                    // Trigger validasi step 2 agar tombol "Lanjut" kembali disabled
                    if (window.validateStep2) window.validateStep2();
                }
            }

            // 3. Event Listener saat Tipe berubah
            tipeSelect.addEventListener('change', function() {
                updateDropdowns(this.value);
            });

            // 4. Jalankan saat halaman dimuat (untuk Edit Mode)
            if (tipeSelect.value) {
                updateDropdowns(tipeSelect.value, true);
            }
        });
    </script>
    {{-- Script Untuk Wizard Pertama menangani validasi dan form input harga pada form --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const inputHarga = document.getElementById('input-harga');
            const allInputs = document.querySelectorAll('.form-input-step');
            const btnNext = document.getElementById('btn-next-1');

            // Fungsi format angka ke ribuan (titik)
            function formatRupiah(angka) {
                let numberString = angka.replace(/[^,\d]/g, '').toString();
                let split = numberString.split(',');
                let sisa = split[0].length % 3;
                let rupiah = split[0].substr(0, sisa);
                let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

                if (ribuan) {
                    let separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                }

                return split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
            }

            // Fungsi validasi form
            function validateForm() {
                let isAllFilled = true;
                allInputs.forEach(input => {
                    if (input.value.trim() === '') {
                        isAllFilled = false;
                    }
                });

                btnNext.disabled = !isAllFilled;
            }

            // Event listener untuk input harga (Format Titik)
            inputHarga.addEventListener('keyup', function(e) {
                this.value = formatRupiah(this.value);
                validateForm();
            });

            // Event listener untuk semua input (Tombol Aktif)
            allInputs.forEach(input => {
                input.addEventListener('input', validateForm);
                input.addEventListener('change', validateForm);
            });

            // Jalankan validasi awal (untuk data lama/old value)
            if (inputHarga.value) {
                inputHarga.value = formatRupiah(inputHarga.value);
            }
            validateForm();
        });
    </script>
    {{-- Script Untuk Wizard Kedua menangani validasi form --}}
    <script>
        // Definisikan validate secara global agar bisa dipanggil skrip Peta
        window.validateStep2 = function() {
            const step2 = document.getElementById('step-2');
            const nextBtn = document.getElementById('btn-next-step-2');
            const inputs = step2.querySelectorAll('.validate-step-2');

            let isValid = true;
            inputs.forEach(input => {
                if (!input.disabled) {
                    if (input.value.trim() === "" || input.value === "0") {
                        isValid = false;
                    }
                }
            });
            nextBtn.disabled = !isValid;
        };

        document.addEventListener('DOMContentLoaded', function() {
            const step2 = document.getElementById('step-2');

            // Pantau input manual
            step2.addEventListener('input', window.validateStep2);
            step2.addEventListener('change', window.validateStep2);

            // Jalankan awal
            window.validateStep2();
        });
    </script>
    {{-- Script untuk menangani logika peta --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const latInput = document.getElementById('latitude');
            const lngInput = document.getElementById('longitude');
            const kotaInput = document.getElementById('kota');
            const searchInput = document.getElementById('azureSearchInput');
            const searchResults = document.getElementById('azureSearchResults');

            // Koordinat Default (Jombang)
            let defaultLat = -7.5461;
            let defaultLng = 112.2331;

            // Ambil nilai awal jika ada (Edit mode) atau gunakan default
            let currentLat = latInput.value ? parseFloat(latInput.value) : defaultLat;
            let currentLng = lngInput.value ? parseFloat(lngInput.value) : defaultLng;
            let zoomLevel = latInput.value ? 17 : 13;

            // 1. Inisialisasi Peta
            const map = L.map('map').setView([currentLat, currentLng], zoomLevel);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap'
            }).addTo(map);

            const marker = L.marker([currentLat, currentLng], {
                draggable: true
            }).addTo(map);

            let debounceTimer; // Untuk debounce autocomplete
            searchInput.addEventListener('input', function() {
                const query = this.value;

                // Bersihkan timer sebelumnya (Debounce)
                clearTimeout(debounceTimer);

                // Jika input kurang dari 3 karakter, sembunyikan hasil
                if (query.length < 3) {
                    searchResults.classList.add('hidden');
                    return;
                }

                // Set timer baru 600ms
                debounceTimer = setTimeout(() => {
                    fetchAzureAutocomplete(query);
                }, 600);
            });

            async function fetchAzureAutocomplete(query) {
                try {
                    // Ditambahkan countrySet=ID agar hasil lebih relevan dengan lokasi di Indonesia
                    const response = await axios.get("{{ route('account.location.search') }}", {
                        params: {
                            query: query
                        }
                    });

                    const data = response.data;

                    if (data.results && data.results.length > 0) {
                        renderSearchResults(data.results);
                    } else {
                        searchResults.classList.add('hidden');
                    }
                } catch (error) {
                    searchResults.classList.add('hidden');
                    console.error("Autocomplete Error:", error);
                }
            }

            function renderSearchResults(results) {
                console.log(results);
                searchResults.innerHTML = '';
                searchResults.classList.remove('hidden');

                results.forEach(result => {
                    const li = document.createElement('li');
                    li.className =
                        "px-4 py-3 text-sm hover:bg-gray-100 cursor-pointer border-b border-gray-50 last:border-none";

                    // Format tampilan: Nama Lokasi - Alamat Lengkap
                    const name = result.address.freeformAddress;
                    const municipality = result.address.municipality ?
                        `Kota ${result.address.municipality}` :
                        '';

                    li.innerHTML = `
                        <div class="font-bold text-gray-800">${name}</div>
                        <div class="text-xs text-gray-500">${result.address.countrySecondarySubdivision || ''}${municipality}</div>
                    `;

                    li.onclick = () => {
                        selectSearchLocation(
                            result.position.lat,
                            result.position.lon,
                            result.address.freeformAddress
                        );
                    };
                    searchResults.appendChild(li);
                });
            }

            // Menutup hasil pencarian jika klik di luar area input
            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                    searchResults.classList.add('hidden');
                }
            });

            // === FUNGSI UPDATE INPUT & VALIDASI ===
            async function updateInputs(lat, lng, isGetAddress = true, isUpdateSearchInput = true) {
                latInput.value = lat.toFixed(8);
                lngInput.value = lng.toFixed(8);

                // Panggil Azure hanya jika diminta (bukan saat inisialisasi awal)
                if (isGetAddress) {
                    await getAddressFromAzure(lat, lng, isUpdateSearchInput);
                }

                window.validateStep2();
            }

            // 2. Inisialisasi Awal (Tanpa API Azure untuk hemat kuota)
            if (latInput.value && lngInput.value) {
                // Jika sudah ada data (edit mode), set marker tapi jangan tembak API
                marker.setLatLng([currentLat, currentLng]);
            } else {
                // Jika data kosong, biarkan input tetap kosong agar tombol "Next" tetap disabled
                latInput.value = '';
                lngInput.value = '';
            }

            // === FUNGSI REVERSE GEOCODING AZURE ===
            async function getAddressFromAzure(lat, lng, isUpdateSearchInput) {
                try {
                    const response = await axios.get("{{ route('account.location.reverse_geocode') }}", {
                        params: {
                            lat: lat,
                            lng: lng
                        }
                    });

                    const data = response.data;
                    if (data.addresses && data.addresses.length > 0) {
                        const addr = data.addresses[0].address;

                        // Update Kota dan Alamat Detail
                        if (kotaInput) kotaInput.value = addr.municipality || addr
                            .countrySecondarySubdivision || '';
                        if (isUpdateSearchInput && searchInput) searchInput.value = addr.freeformAddress;
                    }
                } catch (error) {
                    console.error("Gagal memuat alamat:", error);
                }
            }

            // Marker digeser
            marker.on('dragend', function() {
                const pos = marker.getLatLng();
                map.invalidateSize(); // Pastikan map konsisten
                map.panTo(pos);
                updateInputs(pos.lat, pos.lng, true, true);
            });

            // Klik pada Peta
            map.on('click', function(e) {
                marker.setLatLng(e.latlng);
                map.panTo(e.latlng);
                updateInputs(e.latlng.lat, e.latlng.lng, true, true);
            });

            // Fitur Temukan Lokasi Saya
            window.findMyLocation = function() {
                const btn = document.getElementById('btnFindMe');
                const originalText = btn.innerHTML;

                btn.innerHTML = '<i class="bi bi-hourglass-split mr-1.5 animate-spin"></i> Mencari...';
                btn.disabled = true;

                if (!navigator.geolocation) {
                    alert("Geolokasi tidak didukung.");
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                    return;
                }

                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const uLat = position.coords.latitude;
                        const uLng = position.coords.longitude;

                        // Centering Peta
                        map.invalidateSize();
                        map.setView([uLat, uLng], 17);
                        marker.setLatLng([uLat, uLng]);

                        // Update koordinat & tembak API Azure
                        updateInputs(uLat, uLng, true, true);

                        btn.innerHTML = '<i class="bi bi-check2-circle mr-1.5"></i> Berhasil';
                        setTimeout(() => {
                            btn.innerHTML = originalText;
                            btn.disabled = false;
                        }, 2000);
                    },
                    function(error) {
                        alert("Gagal mengambil lokasi. Pastikan izin GPS aktif.");
                        btn.innerHTML = originalText;
                        btn.disabled = false;
                    }, {
                        enableHighAccuracy: true,
                        timeout: 10000
                    }
                );
            };

            // Integrasi Azure Search (Sama seperti sebelumnya namun panggil updateInputs)
            window.selectSearchLocation = function(lat, lon, addressText) {
                map.invalidateSize();
                map.setView([lat, lon], 17);
                marker.setLatLng([lat, lon]);
                updateInputs(lat, lon, true, false); // false agar search input tidak tertimpa balik
                searchInput.value = addressText;
                searchResults.classList.add('hidden');
            };
        });
    </script>
    {{-- Script Untuk Wizard Ketiga menangani validasi form --}}
    <script></script>
    {{-- Script untuk menangani logika perpindahan wizard --}}
    <script>
        const isBuilding = {{ $typeProperty == 'building' ? 'true' : 'false' }};

        // Init TinyMCE
        tinymce.init({
            selector: '#default',
            height: 300,
            menubar: false,
            plugins: 'lists link',
            toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright | bullist numlist',
            setup: function(editor) {
                editor.on('change', function() {
                    tinymce.triggerSave();
                });
            }
        });

        // LOGIKA WIZARD
        function nextStep(currentStep) {
            document.getElementById('step-' + currentStep).classList.remove('active');
            document.getElementById('indicator-' + currentStep).classList.remove('active');
            document.getElementById('indicator-' + currentStep).classList.add('completed');

            let nextStepNum = currentStep + 1;
            document.getElementById('step-' + nextStepNum).classList.add('active');
            document.getElementById('indicator-' + nextStepNum).classList.add('active');

            if (nextStepNum === 4) generatePreview();
        }

        function prevStep(currentStep) {
            document.getElementById('step-' + currentStep).classList.remove('active');
            document.getElementById('indicator-' + currentStep).classList.remove('active');

            let prevStepNum = currentStep - 1;
            document.getElementById('step-' + prevStepNum).classList.add('active');
            document.getElementById('indicator-' + prevStepNum).classList.add('active');
            document.getElementById('indicator-' + prevStepNum).classList.remove('completed');
        }

        // GENERATE PREVIEW
        function generatePreview() {
            const formatter = new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            });

            document.getElementById('prev-judul').innerText = document.getElementById('input-judul').value ||
                'Belum ada judul';
            document.getElementById('prev-harga').innerText = formatter.format(document.getElementById('input-harga')
                .value.replaceAll('.', '') || 0);

            const trElem = document.getElementById('input-transaksi');
            document.getElementById('prev-transaksi').innerText = trElem && trElem.value !== "" ? trElem.options[trElem
                .selectedIndex].text : 'Status';

            document.getElementById('prev-lokasi').innerText = document.getElementById('azureSearchInput').value ||
                'Belum ada lokasi';

            const tipeElem = document.getElementById('input-tipe');
            document.getElementById('prev-tipe').innerText = tipeElem && tipeElem.value !== "" ? tipeElem.options[tipeElem
                .selectedIndex].text : '-';

            const legElem = document.getElementById('input-legalitas');
            document.getElementById('prev-legalitas').innerText = legElem && legElem.value !== "" ? legElem.options[legElem
                .selectedIndex].text : '-';

            document.getElementById('prev-luas').innerText = (document.getElementById('input-luas').value || '0') + ' m²';

            if (isBuilding) {
                document.getElementById('prev-luas-bangunan').innerText = (document.getElementById('input-luas-bangunan')
                    .value || '0') + ' m²';
                document.getElementById('prev-kt').innerText = document.getElementById('input-kt').value || '-';
                document.getElementById('prev-km').innerText = document.getElementById('input-km').value || '-';
            }

            tinymce.triggerSave();
            document.getElementById('prev-deskripsi').innerHTML = document.getElementById('default').value ||
                'Belum ada deskripsi.';

            let uploadedImages = [];
            for (let i = 1; i <= 10; i++) {
                let imgEl = document.getElementById(`preview_${i}`);
                if (imgEl && !imgEl.classList.contains('hidden') && imgEl.src !== "") {
                    uploadedImages.push(imgEl.src.replace('low', 'high'));
                }
            }

            const galleryContainer = document.getElementById('prev-gallery-container');
            let total = uploadedImages.length;

            if (total === 0) {
                galleryContainer.innerHTML =
                    `<div class="w-full h-full flex items-center justify-center text-gray-400"><svg class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" /></svg></div>`;
            } else if (total === 1) {
                galleryContainer.innerHTML =
                    `<div class="relative w-full h-full"><img src="${uploadedImages[0]}" class="w-full h-full object-cover"></div>`;
            } else {
                let html =
                    `<div class="relative w-full md:w-1/2 h-full"><img src="${uploadedImages[0]}" class="w-full h-full object-cover"></div>`;
                let gridClass = 'grid-cols-1 grid-rows-1';
                if (total === 3) gridClass = 'grid-cols-1 grid-rows-2';
                if (total >= 4) gridClass = 'grid-cols-2 grid-rows-2';

                html += `<div class="hidden md:grid w-1/2 gap-4 ${gridClass} h-full p-2 bg-white">`;
                for (let i = 1; i < Math.min(5, total); i++) {
                    let extraClass = (total === 4 && i === 1) ? 'col-span-2' : '';
                    let overlay = '';
                    if (i === 4 && total > 5) {
                        overlay =
                            `<div class="absolute inset-0 bg-black/60 flex flex-col items-center justify-center text-white"><span class="font-bold text-sm">+${total - 5} Foto</span></div>`;
                    }
                    html += `<div class="relative w-full h-full overflow-hidden rounded-xl ${extraClass}">
                                <img src="${uploadedImages[i]}" class="w-full h-full object-cover">
                                ${overlay}
                             </div>`;
                }
                html += `</div>`;
                galleryContainer.innerHTML = html;
            }
        }

        // LOADING BUTTON SAAT SUBMIT
        document.getElementById('wizardForm').addEventListener('submit', function() {
            const btnSubmit = document.getElementById('btn-submit');
            btnSubmit.innerHTML =
                '<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Menyimpan...';
            btnSubmit.classList.add('opacity-75', 'cursor-not-allowed');
            btnSubmit.disabled = true;
        });
    </script>
    {{-- Script untuk menangani Logika upload gambar --}}
    <script>
        const isEdit = {{ json_encode($isEdit ?? false) }};
        const typeProperty = "{{ $typeProperty }}";
        // const storageBaseUrl = "{{ asset('storage') }}/";
        let cropper = null;
        let currentFile = null;
        let currentSortNumber = null;
        let currentInputElement = null;

        /**
         * 1. VALIDASI JUMLAH FOTO
         * Mengecek apakah minimal 5 foto sudah terpenuhi
         */
        function validateStep3Photos() {
            let uploadedCount = 0;
            for (let i = 1; i <= 10; i++) {
                const val = document.getElementById(`temp_preview_${i}`).value;
                if (val && val.trim() !== "") {
                    uploadedCount++;
                }
            }

            const badge = document.getElementById('photo-counter-badge');
            const nextBtn = document.getElementById('btn-next-step-3'); // Pastikan ID tombol ini benar di Blade

            if (badge) {
                badge.innerText = `Minimal: ${uploadedCount} / 5 Foto`;
                if (uploadedCount >= 5) {
                    badge.className =
                        "px-3 py-1 rounded-full text-[10px] font-bold bg-green-50 text-green-600 border border-green-100 transition-all";
                    badge.innerHTML = `<i class="bi bi-check-circle-fill mr-1"></i> Syarat Terpenuhi (${uploadedCount}/5)`;
                } else {
                    badge.className =
                        "px-3 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-600 border border-amber-100 transition-all";
                }
            }

            if (nextBtn) {
                nextBtn.disabled = (uploadedCount < 5);
            }
        }

        /**
         * FUNGSI: HAPUS FOTO (Mode Antrean / Array)
         * Tidak menghapus langsung ke server, tapi mencatat path ke array form.
         */
        function deleteImage(event, sortNumber) {
            event.preventDefault();

            // Bersihkan input
            const tempInput = document.getElementById(`temp_preview_${sortNumber}`);
            const previewImg = document.getElementById(`preview_${sortNumber}`);
            const placeholder = document.getElementById(`placeholder_${sortNumber}`);
            const deleteBtn = document.getElementById(`btn_delete_${sortNumber}`);

            tempInput.value = "";
            if (previewImg) {
                previewImg.src = "";
                previewImg.classList.add('hidden');
            }

            if (placeholder) {
                placeholder.classList.remove('hidden');
                placeholder.classList.add('flex');
            }

            if (deleteBtn) {
                deleteBtn.classList.add('hidden');
            }

            // 3. Update Validasi Minimal 5 Foto
            if (typeof validateStep3Photos === 'function') {
                validateStep3Photos(); //[cite: 1]
            }
        }

        /**
         * 3. LOGIKA KOMPRESI & CANVAS
         */
        async function compressToWebP(canvas, maxKB, type) {
            const targetDim = type === 'low' ? 320 : type === 'hd' ? 1024 : 800;
            let finalCanvas = canvas;

            if (canvas.width > targetDim || canvas.height > targetDim) {
                const scaledCanvas = document.createElement('canvas');
                let width = canvas.width;
                let height = canvas.height;
                if (width > height) {
                    height *= targetDim / width;
                    width = targetDim;
                } else {
                    width *= targetDim / height;
                    height = targetDim;
                }
                scaledCanvas.width = width;
                scaledCanvas.height = height;
                const ctx = scaledCanvas.getContext('2d');
                ctx.imageSmoothingEnabled = true;
                ctx.imageSmoothingQuality = 'high';
                ctx.drawImage(canvas, 0, 0, width, height);
                finalCanvas = scaledCanvas;
            }

            let quality = 0.9;
            let blob = await new Promise(res => finalCanvas.toBlob(res, 'image/webp', quality));
            while (blob.size > maxKB * 1024 && quality > 0.2) {
                quality -= 0.1;
                blob = await new Promise(res => finalCanvas.toBlob(res, 'image/webp', quality));
            }
            return blob;
        }

        function getOriginalCanvas(file) {
            return new Promise((resolve) => {
                const img = new Image();
                img.onload = () => {
                    const canvas = document.createElement('canvas');
                    canvas.width = img.width;
                    canvas.height = img.height;
                    canvas.getContext('2d').drawImage(img, 0, 0);
                    resolve(canvas);
                };
                img.src = URL.createObjectURL(file);
            });
        }

        /**
         * 4. HANDLING UPLOAD & CROP
         */
        function handleImageUpload(inputElement, sortNumber) {
            const file = inputElement.files[0];
            if (!file) return;

            if (file.size > 8 * 1024 * 1024) {
                Swal.fire('Peringatan', 'Ukuran gambar maksimal 8MB', 'warning');
                inputElement.value = '';
                return;
            }

            currentFile = file;
            currentSortNumber = sortNumber;
            currentInputElement = inputElement;

            const imageToCrop = document.getElementById('imageToCrop');
            imageToCrop.src = URL.createObjectURL(file);
            document.getElementById('cropModal').classList.remove('hidden');

            if (cropper) cropper.destroy();
            cropper = new Cropper(imageToCrop, {
                aspectRatio: 1,
                viewMode: 1,
                dragMode: 'move',
                autoCropArea: 0.8,
                toggleDragModeOnDblclick: false,
            });
        }

        function closeCropModal() {
            document.getElementById('cropModal').classList.add('hidden');
            if (cropper) cropper.destroy();
            if (currentInputElement) currentInputElement.value = '';
        }

        async function processAndUpload() {
            if (!cropper) return;

            const sortNumber = currentSortNumber;
            const tempPreviewEl = document.getElementById(`temp_preview_${sortNumber}`);
            const previewEl = document.getElementById(`preview_${sortNumber}`);
            const placeholderEl = document.getElementById(`placeholder_${sortNumber}`);
            const loadingEl = document.getElementById(`loading_${sortNumber}`);
            const deleteBtn = document.getElementById(`btn_delete_${sortNumber}`);
            const propertyId = document.getElementById('propertyId').value;

            document.getElementById('cropModal').classList.add('hidden');
            loadingEl.classList.remove('hidden');
            loadingEl.classList.add('flex');

            try {
                const croppedCanvas = cropper.getCroppedCanvas();
                const originalCanvas = await getOriginalCanvas(currentFile);

                const [blobHigh, blobLow, blobOri] = await Promise.all([
                    compressToWebP(croppedCanvas, 150, 'high'),
                    compressToWebP(croppedCanvas, 50, 'low'),
                    compressToWebP(originalCanvas, 350, 'hd')
                ]);

                const formData = new FormData();
                formData.append('image_high', blobHigh, 'high.webp');
                formData.append('image_low', blobLow, 'low.webp');
                formData.append('image_ori', blobOri, 'ori.webp');
                formData.append('id', propertyId);
                formData.append('sort', sortNumber);
                formData.append('isEdit', isEdit);
                formData.append('typeProperty', typeProperty);

                const response = await fetch("{{ route('user.property.uploadImage') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content'),
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const result = await response.json();

                if (result.status === 'success') {
                    previewEl.src = result.image_path;
                    previewEl.classList.remove('hidden');
                    placeholderEl.classList.add('hidden');
                    deleteBtn.classList.remove('hidden');

                    // Simpan path ke input hidden
                    tempPreviewEl.value = result.image_path.replace('/storage/', '').replace('-image_low.webp', '');

                    validateStep3Photos(); // Update status tombol[cite: 2]
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                Swal.fire('Gagal', error.message || 'Terjadi kesalahan saat upload', 'error');
                placeholderEl.classList.remove('hidden');
            } finally {
                loadingEl.classList.add('hidden');
                loadingEl.classList.remove('flex');
                if (cropper) cropper.destroy();
            }
        }

        // Inisialisasi awal saat halaman dimuat (untuk mode Edit)
        document.addEventListener('DOMContentLoaded', function() {
            validateStep3Photos();
        });
    </script>
@endsection
