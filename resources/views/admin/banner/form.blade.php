@extends('layouts.admin')

@section('style')
    <style>
        /* Transisi Halus */
        .fade-switch {
            transition: opacity 0.3s ease, transform 0.3s ease;
        }

        .fade-hide {
            opacity: 0;
            transform: translateY(10px);
            pointer-events: none;
            position: absolute;
            visibility: hidden;
        }

        .fade-show {
            opacity: 1;
            transform: translateY(0);
            position: relative;
            visibility: visible;
        }

        /* Hover Effects */
        .file-input-wrapper:hover {
            border-color: #0d9488;
            background-color: #f0fdfa;
        }

        .ref-slot:hover {
            border-color: #0d9488;
            background-color: #f0fdfa;
            cursor: pointer;
        }

        /* Cropper */
        .img-container {
            max-height: 60vh;
            width: 100%;
            background-color: #f3f4f6;
            text-align: center;
            overflow: hidden;
        }

        .img-container img {
            display: block;
            max-width: 100%;
            max-height: 60vh;
            margin: 0 auto;
        }

        .cropper-view-box {
            outline: 2px solid #0d9488;
            border-radius: 4px;
        }

        .cropper-line,
        .cropper-point,
        .cropper-center {
            display: none !important;
        }

        .cropper-modal {
            background-color: rgba(0, 0, 0, 0.8);
        }

        /* Tab Styles */
        .tab-active {
            background-color: #0d9488;
            color: white;
            shadow: 0 4px 6px -1px rgba(13, 148, 136, 0.2);
        }

        .tab-inactive {
            background-color: transparent;
            color: #4b5563;
        }

        .tab-inactive:hover {
            background-color: #f3f4f6;
        }
    </style>
@endsection

@section('content')
    <div id="main" class="min-h-screen bg-gray-50/50 p-4 sm:p-6 lg:p-8 font-['Inter'] relative z-0">

        {{-- HEADER --}}
        <div class="mb-8 max-w-5xl mx-auto">
            <a href="{{ route('banner.index') }}"
                class="inline-flex items-center text-sm font-semibold text-gray-500 hover:text-[#0d9488] transition-colors no-underline mb-4">
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                Kembali ke Daftar Banner
            </a>
            <h2 class="text-2xl font-extrabold text-gray-900 m-0 font-['Plus_Jakarta_Sans']">
                {{ isset($isEdit) && $isEdit ? 'Edit Banner' : 'Buat Banner Baru' }}
            </h2>
            <p class="text-sm text-gray-500 mt-1 m-0">Pilih metode pembuatan banner: unggah manual atau gunakan AI Generator
                kami.</p>
        </div>

        <div class="max-w-5xl mx-auto">

            {{-- METODE SELECTION (TABS) --}}
            <div class="bg-white p-2 rounded-2xl shadow-sm border border-gray-100 flex flex-col sm:flex-row gap-2 mb-6">
                <button type="button" onclick="switchMethod('manual')" id="tab-manual"
                    class="flex-1 py-3 px-4 rounded-xl text-sm font-bold flex items-center justify-center gap-2 transition-all tab-active">
                    <i class="fas fa-upload"></i> Upload Manual
                </button>
                <button type="button" onclick="switchMethod('ai-ref')" id="tab-ai-ref"
                    class="flex-1 py-3 px-4 rounded-xl text-sm font-bold flex items-center justify-center gap-2 transition-all tab-inactive">
                    <i class="fas fa-magic"></i> AI: Kombinasi Referensi
                </button>
                <button type="button" onclick="switchMethod('ai-prompt')" id="tab-ai-prompt"
                    class="flex-1 py-3 px-4 rounded-xl text-sm font-bold flex items-center justify-center gap-2 transition-all tab-inactive">
                    <i class="fas fa-keyboard"></i> AI: Teks Prompt Murni
                </button>
            </div>

            <form
                action="{{ route(isset($isEdit) && $isEdit ? 'banner.update' : 'banner.store', isset($isEdit) && $isEdit ? $banner->id : null) }}"
                method="POST" enctype="multipart/form-data" id="bannerForm">
                @csrf
                @if (isset($isEdit) && $isEdit)
                    @method('PUT')
                @endif

                {{-- Input Hidden untuk menyimpan metode yang dipilih --}}
                <input type="hidden" name="generation_method" id="generation_method" value="manual">
                {{-- Input Hidden untuk hasil AI --}}
                <input type="hidden" name="ai_generated_image_url" id="ai_generated_image_url" value="">

                <div
                    class="bg-white rounded-3xl shadow-[0_4px_25px_rgba(0,0,0,0.03)] border border-gray-100 overflow-hidden p-6 md:p-8 mb-6">

                    {{-- ==========================================================
                     MODE 1: UPLOAD MANUAL (DEFAULT)
                     ========================================================== --}}
                    <div id="section-manual" class="fade-switch fade-show w-full">
                        <label class="block text-sm font-bold text-gray-700 mb-3">Pilih File Banner <span
                                class="text-red-500">*</span></label>
                        <input type="file" id="image" name="image" accept="image/*" class="hidden"
                            onchange="previewImage(this)">

                        <label for="image" id="image-preview-container" style="aspect-ratio: 2/1;"
                            class="relative block w-full bg-gray-50 border-2 border-dashed border-gray-300 rounded-2xl overflow-hidden cursor-pointer hover:bg-[#f0fdfa] hover:border-[#0d9488] transition-all duration-300 group {{ isset($isEdit) && $isEdit && isset($banner->image) ? 'border-solid border-gray-200' : '' }}">
                            @if (isset($isEdit) && $isEdit && isset($banner->image))
                                <img id="banner-preview" src="{{ asset('storage/' . $banner->image) }}"
                                    class="absolute inset-0 w-full h-full object-cover z-10 transition-transform duration-500 group-hover:scale-105"
                                    alt="Preview Banner">
                            @else
                                <img id="banner-preview" src=""
                                    class="absolute inset-0 w-full h-full object-cover z-10 hidden transition-transform duration-500 group-hover:scale-105"
                                    alt="Preview Banner">
                            @endif

                            <div id="image-overlay"
                                class="absolute inset-0 bg-black/40 flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity z-20"
                                style="display: {{ isset($isEdit) && $isEdit && isset($banner->image) ? 'flex' : 'none' }};">
                                <i class="bi bi-camera text-white text-3xl mb-2"></i>
                                <span class="text-white text-sm font-bold">Ganti Banner</span>
                            </div>

                            <div id="placeholder-content"
                                class="absolute inset-0 flex flex-col items-center justify-center text-gray-400 z-0 {{ isset($isEdit) && $isEdit && isset($banner->image) ? 'hidden' : '' }}">
                                <svg class="w-12 h-12 mb-3 text-gray-300 group-hover:text-[#0d9488] transition-colors"
                                    fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                </svg>
                                <span
                                    class="text-base font-bold text-gray-700 group-hover:text-[#0d9488] transition-colors mb-1">Pilih
                                    Gambar Banner</span>
                                <span class="text-xs font-medium text-gray-400">Klik area ini untuk mengunggah (Format:
                                    JPG/PNG, Max: 2MB)</span>
                            </div>
                        </label>
                        @error('image')
                            <p class="text-red-500 text-xs font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- ==========================================================
                     MODE 2: AI (KOMBINASI REFERENSI GAMBAR + TEKS)
                     ========================================================== --}}
                    <div id="section-ai-ref" class="fade-switch fade-hide w-full">
                        <div class="mb-6 p-4 bg-teal-50 border border-teal-100 rounded-xl flex items-start gap-3">
                            <i class="fas fa-info-circle text-[#0d9488] mt-0.5"></i>
                            <div class="text-sm text-teal-900 leading-relaxed">
                                <span class="font-bold">Kombinasi AI:</span> Unggah maksimal 3 gambar.
                                <br>• Jika 1 gambar: AI akan menjadikannya sebagai inspirasi gaya.
                                <br>• Jika 2-3 gambar: AI akan menggabungkan elemen visual dari semua gambar tersebut
                                menjadi 1 banner baru.
                                <br>• <span class="font-bold">Baru:</span> Isi teks di bawah ini agar AI bisa menuliskannya
                                di dalam banner.
                            </div>
                        </div>

                        {{-- Area Upload Gambar Referensi --}}
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
                            {{-- Slot 1 --}}
                            <div class="flex flex-col">
                                <label class="text-xs font-bold text-gray-600 mb-2">Referensi 1 (Utama)</label>
                                <input type="file" id="ref1" name="ai_refs[]" accept="image/*" class="hidden"
                                    onchange="previewRef(this, 'preview-ref1')">
                                <label for="ref1"
                                    class="ref-slot aspect-[4/3] bg-gray-50 border-2 border-dashed border-gray-300 rounded-xl flex items-center justify-center relative overflow-hidden group">
                                    <img id="preview-ref1" src=""
                                        class="absolute inset-0 w-full h-full object-cover hidden" alt="Ref 1">
                                    <div class="flex flex-col items-center text-gray-400 group-hover:text-[#0d9488] z-0">
                                        <i class="fas fa-plus text-xl mb-1"></i><span
                                            class="text-[10px] font-bold">Upload</span>
                                    </div>
                                </label>
                            </div>
                            {{-- Slot 2 --}}
                            <div class="flex flex-col">
                                <label class="text-xs font-bold text-gray-600 mb-2">Referensi 2 (Opsional)</label>
                                <input type="file" id="ref2" name="ai_refs[]" accept="image/*" class="hidden"
                                    onchange="previewRef(this, 'preview-ref2')">
                                <label for="ref2"
                                    class="ref-slot aspect-[4/3] bg-gray-50 border-2 border-dashed border-gray-300 rounded-xl flex items-center justify-center relative overflow-hidden group">
                                    <img id="preview-ref2" src=""
                                        class="absolute inset-0 w-full h-full object-cover hidden" alt="Ref 2">
                                    <div class="flex flex-col items-center text-gray-400 group-hover:text-[#0d9488] z-0">
                                        <i class="fas fa-plus text-xl mb-1"></i><span
                                            class="text-[10px] font-bold">Upload</span>
                                    </div>
                                </label>
                            </div>
                            {{-- Slot 3 --}}
                            <div class="flex flex-col">
                                <label class="text-xs font-bold text-gray-600 mb-2">Referensi 3 (Opsional)</label>
                                <input type="file" id="ref3" name="ai_refs[]" accept="image/*" class="hidden"
                                    onchange="previewRef(this, 'preview-ref3')">
                                <label for="ref3"
                                    class="ref-slot aspect-[4/3] bg-gray-50 border-2 border-dashed border-gray-300 rounded-xl flex items-center justify-center relative overflow-hidden group">
                                    <img id="preview-ref3" src=""
                                        class="absolute inset-0 w-full h-full object-cover hidden" alt="Ref 3">
                                    <div class="flex flex-col items-center text-gray-400 group-hover:text-[#0d9488] z-0">
                                        <i class="fas fa-plus text-xl mb-1"></i><span
                                            class="text-[10px] font-bold">Upload</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        {{-- AREA INPUT TEKS DALAM BANNER (PENTING!) --}}
                        <div class="bg-gray-50 p-6 rounded-2xl border border-gray-100 mb-6">
                            <h6
                                class="text-sm font-bold text-gray-800 mb-4 border-b border-gray-200 pb-2 flex items-center gap-2">
                                <i class="fas fa-font text-[#0d9488]"></i> Konten & Tulisan Dalam Banner
                            </h6>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-bold text-gray-700 mb-1.5">Judul Utama / Headline
                                        (Paling Besar)</label>
                                    <input type="text" id="ai_comb_title" name="ai_text[title]"
                                        placeholder="Contoh: Diskon Akhir Tahun 50%!"
                                        class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-[#0d9488] transition-all">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1.5">Subjudul / Slogan
                                        (Sedang)</label>
                                    <input type="text" id="ai_comb_subtitle" name="ai_text[subtitle]"
                                        placeholder="Contoh: Hanya Berlaku Bulan Ini"
                                        class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-[#0d9488] transition-all">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1.5">Tombol / Call to Action
                                        (Jika ada)</label>
                                    <input type="text" id="ai_comb_cta" name="ai_text[cta]"
                                        placeholder="Contoh: Hubungi Kami Sekarang"
                                        class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-[#0d9488] transition-all">
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-xs font-bold text-gray-700 mb-1.5">Detail Teks / Deskripsi
                                        (Kecil)</label>
                                    <textarea id="ai_comb_desc" name="ai_text[desc]" rows="2"
                                        placeholder="Contoh: *Syarat & ketentuan berlaku. Unit terbatas."
                                        class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-[#0d9488] transition-all"></textarea>
                                </div>
                            </div>
                        </div>

                        <button type="button" onclick="generateAIBanner('ref')"
                            class="w-full py-3 bg-gradient-to-r from-[#0d9488] to-teal-500 hover:from-teal-600 hover:to-teal-400 text-white font-bold rounded-xl shadow-lg transition-all flex items-center justify-center gap-2">
                            <i class="fas fa-bolt"></i> Generate Banner (Kombinasi Gambar & Teks)
                        </button>
                    </div>

                    {{-- ==========================================================
                     MODE 3: AI (PROMPT TEKS MURNI)
                     ========================================================== --}}
                    <div id="section-ai-prompt" class="fade-switch fade-hide w-full">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1.5">Warna Utama</label>
                                <input type="text" id="ai_color_primary" placeholder="Contoh: Biru Laut, Putih, Emas"
                                    class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-[#0d9488] transition-all">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1.5">Warna Aksen / Sekunder</label>
                                <input type="text" id="ai_color_secondary"
                                    placeholder="Contoh: Kuning Neon, Hijau Daun"
                                    class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-[#0d9488] transition-all">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-gray-700 mb-1.5">Gaya Desain / Vibe</label>
                                <select id="ai_style"
                                    class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-[#0d9488] transition-all">
                                    <option value="modern_minimalist">Modern Minimalist</option>
                                    <option value="luxury_elegant">Luxury & Elegan</option>
                                    <option value="corporate_professional">Korporat Profesional</option>
                                    <option value="warm_family">Warm & Family Friendly</option>
                                    <option value="futuristic">Futuristik / Cyber</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-gray-700 mb-1.5">Detail / Deskripsi
                                    Banner</label>
                                <textarea id="ai_prompt" rows="3"
                                    placeholder="Contoh: Banner perumahan dengan latar belakang pegunungan, ada tulisan 'Diskon 50%' di tengah dengan huruf tebal..."
                                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-[#0d9488] transition-all"></textarea>
                            </div>
                        </div>

                        <button type="button" onclick="generateAIBanner('prompt')"
                            class="w-full py-3 bg-gradient-to-r from-[#0d9488] to-teal-500 hover:from-teal-600 hover:to-teal-400 text-white font-bold rounded-xl shadow-lg transition-all flex items-center justify-center gap-2">
                            <i class="fas fa-magic"></i> Generate Banner (Dari Teks)
                        </button>
                    </div>

                    {{-- AREA HASIL GENERATE AI (Hanya muncul jika pakai AI) --}}
                    <div id="ai-result-area" class="hidden mt-8 pt-8 border-t border-gray-200">
                        <h6 class="text-sm font-bold text-gray-800 mb-3 flex items-center gap-2"><i
                                class="fas fa-check-circle text-green-500"></i> Hasil Generasi AI</h6>
                        <div
                            class="relative w-full aspect-[2/1] bg-gray-100 rounded-2xl overflow-hidden border border-gray-200 shadow-inner">
                            <img id="ai-final-preview" src="" class="w-full h-full object-cover">
                            {{-- Loading Overlay --}}
                            <div id="ai-loading"
                                class="absolute inset-0 bg-white/90 backdrop-blur-sm flex flex-col items-center justify-center z-10">
                                <div
                                    class="w-12 h-12 border-4 border-teal-100 border-t-[#0d9488] rounded-full animate-spin mb-3">
                                </div>
                                <p class="text-sm font-bold text-gray-700 animate-pulse">AI sedang melukis banner Anda...
                                </p>
                                <p class="text-xs text-gray-400 mt-1">Harap tunggu beberapa detik.</p>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- ==========================================================
                 PENGATURAN STATUS & TOMBOL SIMPAN
                 ========================================================== --}}
                <div
                    class="bg-white rounded-3xl shadow-[0_4px_25px_rgba(0,0,0,0.03)] border border-gray-100 p-6 md:p-8 flex flex-col sm:flex-row justify-between items-center gap-4">
                    <div class="w-full sm:w-1/2">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Status Penayangan <span
                                class="text-red-500">*</span></label>
                        <select id="status" name="status"
                            class="w-full max-w-xs px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium text-gray-700 focus:outline-none focus:border-[#0d9488] transition-all">
                            <option value="1"
                                {{ old('status', isset($isEdit) && $isEdit ? $banner->status : 1) == 1 ? 'selected' : '' }}>
                                🟢 Aktif (Tampilkan)</option>
                            <option value="0"
                                {{ old('status', isset($isEdit) && $isEdit ? $banner->status : 1) == 0 ? 'selected' : '' }}>
                                🔴 Tidak Aktif (Sembunyikan)</option>
                        </select>
                    </div>

                    <div class="flex gap-3 w-full sm:w-auto mt-4 sm:mt-0">
                        <a href="{{ route('banner.index') }}"
                            class="flex-1 sm:flex-none justify-center inline-flex items-center px-6 py-3 bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 text-sm font-bold rounded-xl transition-all shadow-sm no-underline">
                            Batal
                        </a>
                        <button type="submit"
                            class="flex-1 sm:flex-none justify-center inline-flex items-center px-8 py-3 bg-gray-900 hover:bg-black text-white text-sm font-bold rounded-xl transition-all shadow-lg hover:-translate-y-0.5">
                            <i class="fas fa-save mr-2"></i> Simpan Banner
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>

    {{-- Modal Cropper JS (Tetap dipertahankan untuk Manual Upload) --}}
    {{-- ... (Kode Cropper Modal persis seperti sebelumnya) ... --}}
    <div id="cropModal"
        class="fixed inset-0 z-[9999] hidden items-center justify-center bg-black/80 backdrop-blur-sm opacity-0 transition-opacity duration-300">
        <div class="bg-[#2b2d31] w-[95%] max-w-4xl rounded-2xl shadow-2xl overflow-hidden flex flex-col transform scale-95 transition-transform duration-300"
            id="cropModalContent">
            <div class="px-6 py-4 border-b border-[#1e1f22] flex justify-between items-center bg-[#2b2d31] shrink-0">
                <h5 class="text-[#f2f3f5] font-bold text-lg m-0 font-['Plus_Jakarta_Sans']">Sesuaikan Posisi Banner</h5>
                <button type="button" onclick="cancelCrop()"
                    class="text-gray-400 hover:text-white transition-colors focus:outline-none">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="w-full bg-black h-[50vh] md:h-[65vh] flex items-center justify-center relative img-container">
                <img id="image-to-crop" src="" alt="Picture" class="max-w-full max-h-full block">
            </div>
            <div class="px-6 py-4 border-t border-[#1e1f22] flex justify-end gap-3 bg-[#2b2d31] shrink-0">
                <button type="button" onclick="cancelCrop()"
                    class="px-5 py-2 text-[#dbdee1] hover:text-white font-medium hover:underline transition-all">Batal</button>
                <button type="button" id="btn-crop"
                    class="px-6 py-2 bg-[#0d9488] hover:bg-teal-500 text-white font-bold rounded-lg transition-all shadow-lg flex items-center">
                    <i class="bi bi-crop mr-2"></i> Potong & Simpan
                </button>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    <script>
        // ==========================================
        // LOGIKA TAB SWITCHER
        // ==========================================
        function switchMethod(method) {
            // Set Value Hidden
            document.getElementById('generation_method').value = method;

            // Reset Tabs Visual
            const tabs = ['manual', 'ai-ref', 'ai-prompt'];
            tabs.forEach(t => {
                const btn = document.getElementById('tab-' + t);
                const sec = document.getElementById('section-' + t);

                if (t === method) {
                    btn.className = btn.className.replace('tab-inactive', 'tab-active');
                    sec.classList.remove('fade-hide');
                    sec.classList.add('fade-show');
                } else {
                    btn.className = btn.className.replace('tab-active', 'tab-inactive');
                    sec.classList.remove('fade-show');
                    sec.classList.add('fade-hide');
                }
            });

            // Sembunyikan hasil AI jika kembali ke manual
            if (method === 'manual') {
                document.getElementById('ai-result-area').classList.add('hidden');
            }
        }

        // ==========================================
        // LOGIKA PREVIEW REFERENSI AI
        // ==========================================
        function previewRef(input, imgId) {
            const imgEl = document.getElementById(imgId);
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imgEl.src = e.target.result;
                    imgEl.classList.remove('hidden');
                    // Sembunyikan icon plus
                    imgEl.nextElementSibling.style.display = 'none';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        // ==========================================
        // LOGIKA SIMULASI GENERATE AI
        // ==========================================
        function generateAIBanner(type) {
            const resultArea = document.getElementById('ai-result-area');
            const loadingOverlay = document.getElementById('ai-loading');
            const finalPreview = document.getElementById('ai-final-preview');

            // Tampilkan Area Hasil dengan status Loading
            resultArea.classList.remove('hidden');
            loadingOverlay.classList.remove('hidden');
            finalPreview.src = ''; // Kosongkan dulu

            // Di sini kamu akan melakukan AJAX call ke backend Laravel kamu yang terhubung ke API AI (DALL-E, Midjourney, dll)
            // Untuk UI ini, kita buat simulasi loading 3 detik

            setTimeout(() => {
                loadingOverlay.classList.add('hidden');
                // Simulasi hasil gambar (URL dummy 2:1)
                const dummyResultUrl =
                    'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80';
                finalPreview.src = dummyResultUrl;

                // Simpan URL hasil ke input hidden agar bisa disave saat form di-submit
                document.getElementById('ai_generated_image_url').value = dummyResultUrl;

                // Pindah scroll ke hasil
                resultArea.scrollIntoView({
                    behavior: 'smooth',
                    block: 'end'
                });
            }, 3000);
        }

        // ==========================================
        // LOGIKA CROPPER JS (UNTUK MANUAL UPLOAD)
        // (Dipertahankan sama seperti kode aslimu)
        // ==========================================
        let cropper = null;
        let selectedOriginalFile = null;

        const imageToCrop = document.getElementById('image-to-crop');
        const modalElement = document.getElementById('cropModal');
        const modalContent = document.getElementById('cropModalContent');
        const fileInput = document.getElementById('image');

        const previewEl = document.getElementById('banner-preview');
        const placeholderEl = document.getElementById('placeholder-content');
        const containerEl = document.getElementById('image-preview-container');
        const overlayEl = document.getElementById('image-overlay');

        const isEditMode = {{ isset($isEdit) && $isEdit ? 'true' : 'false' }};
        const hasOldImage = "{{ isset($banner) && isset($banner->image) ? 'yes' : '' }}" !== "";

        function previewImage(input) {
            if (input.files && input.files[0]) {
                selectedOriginalFile = input.files[0];
                const reader = new FileReader();

                reader.onload = function(e) {
                    imageToCrop.src = e.target.result;
                    openModal();
                    imageToCrop.onload = function() {
                        if (cropper) cropper.destroy();
                        cropper = new Cropper(imageToCrop, {
                            aspectRatio: 3 / 1,
                            viewMode: 1,
                            dragMode: 'move',
                            autoCropArea: 0.95,
                            guides: false,
                            center: false,
                            highlight: false,
                            background: false,
                            cropBoxMovable: true,
                            cropBoxResizable: true,
                            toggleDragModeOnDblclick: false,
                        });
                    };
                }
                reader.readAsDataURL(selectedOriginalFile);
            }
        }

        function openModal() {
            modalElement.classList.remove('hidden');
            modalElement.classList.add('flex');
            setTimeout(() => {
                modalElement.classList.remove('opacity-0');
                modalContent.classList.remove('scale-95');
            }, 10);
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            modalElement.classList.add('opacity-0');
            modalContent.classList.add('scale-95');
            setTimeout(() => {
                modalElement.classList.add('hidden');
                modalElement.classList.remove('flex');
                if (cropper) {
                    cropper.destroy();
                    cropper = null;
                }
                imageToCrop.src = '';
                document.body.style.overflow = 'auto';
            }, 300);
        }

        function cancelCrop() {
            closeModal();
            fileInput.value = '';
            if (!isEditMode || !hasOldImage) {
                previewEl.classList.add('hidden');
                previewEl.src = "";
                placeholderEl.classList.remove('hidden');
                overlayEl.style.display = 'none';
                containerEl.classList.add('border-dashed', 'border-gray-300');
                containerEl.classList.remove('border-solid', 'border-gray-200');
            }
        }

        document.getElementById('btn-crop').addEventListener('click', function() {
            if (!cropper) return;
            const originalBtnText = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Memproses...';
            this.disabled = true;

            const canvas = cropper.getCroppedCanvas({
                width: 1920,
                height: 960,
                fillColor: '#fff'
            });
            canvas.toBlob(function(blob) {
                if (!blob) {
                    alert("Gagal memproses gambar.");
                    resetModalBtn(originalBtnText);
                    return;
                }
                let originalNameBase = selectedOriginalFile.name.substring(0, selectedOriginalFile.name
                    .lastIndexOf('.')) || selectedOriginalFile.name;
                const newFile = new File([blob], originalNameBase + "_cropped.jpg", {
                    type: 'image/jpeg',
                    lastModified: Date.now()
                });

                previewEl.src = URL.createObjectURL(blob);
                previewEl.classList.remove('hidden');
                placeholderEl.classList.add('hidden');
                containerEl.classList.remove('border-dashed', 'border-gray-300');
                containerEl.classList.add('border-solid', 'border-gray-200');
                overlayEl.style.display = 'flex';

                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(newFile);
                fileInput.files = dataTransfer.files;
                closeModal();
                resetModalBtn(originalBtnText);
            }, 'image/jpeg', 0.85);
        });

        function resetModalBtn(text) {
            const btn = document.getElementById('btn-crop');
            btn.innerHTML = text;
            btn.disabled = false;
        }
    </script>
@endsection
