@extends('layouts.admin')

@section('style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" />
    <style>
        /* Styling Dasar Override (Font & Transisi) */
        .font-heading {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .font-body {
            font-family: 'Inter', sans-serif;
        }

        .form-input:focus,
        .form-textarea:focus,
        .form-select:focus {
            border-color: #0d9488;
            box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.15);
            outline: none;
        }

        /* Kustomisasi Tampilan Cropper.js */
        .cropper-view-box {
            outline: 2px solid rgba(255, 255, 255, 0.9) !important;
            border-radius: 8px;
        }

        .cropper-line,
        .cropper-point,
        .cropper-center {
            display: none !important;
        }

        .cropper-modal {
            background-color: #000 !important;
            opacity: 0.85 !important;
        }
    </style>
@endsection

@section('content')
    <div id="main" class="min-h-screen bg-gray-50/50 p-4 sm:p-6 lg:p-8 font-['Inter']">

        {{-- ==========================================================
         HEADER & BREADCRUMB
         ========================================================== --}}
        <div class="mb-8 max-w-7xl mx-auto flex flex-col md:flex-row justify-between md:items-center gap-4">
            <div>
                <h2 class="text-2xl font-extrabold text-gray-900 m-0 font-['Plus_Jakarta_Sans'] tracking-tight">
                    {{ $isEdit ? 'Edit Portofolio' : 'Tambah Portofolio' }}
                </h2>
                <p class="text-sm text-gray-500 m-0 mt-1">
                    {{ $isEdit ? 'Perbarui data proyek portofolio Anda di sini.' : 'Masukkan data proyek portofolio baru ke dalam sistem.' }}
                </p>
            </div>

            <a href="{{ route('portofolios.index') }}"
                class="inline-flex items-center px-4 py-2.5 bg-white border border-gray-200 hover:bg-gray-50 text-gray-600 text-sm font-bold rounded-xl transition-colors no-underline shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                Kembali
            </a>
        </div>

        {{-- ==========================================================
         FORM UTAMA (GRID LAYOUT)
         ========================================================== --}}
        <form action="{{ route($isEdit ? 'portofolios.update' : 'portofolios.store', $portofolio ?? null) }}" method="POST"
            enctype="multipart/form-data" class="max-w-7xl mx-auto">

            @csrf
            @method($isEdit ? 'PUT' : 'POST')

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8">

                {{-- BAGIAN KIRI: INFORMASI UTAMA --}}
                <div
                    class="lg:col-span-8 bg-white p-6 md:p-8 rounded-3xl shadow-[0_4px_25px_rgba(0,0,0,0.03)] border border-gray-100 flex flex-col h-full">

                    <h5 class="text-lg font-bold text-gray-800 m-0 mb-6 pb-4 border-b border-gray-100">Informasi Proyek</h5>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                        {{-- Input Judul --}}
                        <div>
                            <label for="judul" class="block text-sm font-bold text-gray-700 mb-2">Judul Portofolio <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="judul" id="judul"
                                class="form-input w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm transition-all @error('judul') border-red-500 @enderror"
                                value="{{ old('judul', $isEdit ? $portofolio->judul : '') }}"
                                placeholder="Contoh: Desain Interior Rumah Tipe 36" required>
                            @error('judul')
                                <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Input Tipe (Design / Build) --}}
                        <div>
                            <label for="tipe" class="block text-sm font-bold text-gray-700 mb-2">Tipe Proyek <span
                                    class="text-red-500">*</span></label>
                            <select name="tipe" id="tipe"
                                class="form-select w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm transition-all cursor-pointer @error('tipe') border-red-500 @enderror"
                                required>
                                <option value="" disabled
                                    {{ old('tipe', $isEdit ? $portofolio->tipe : '') == '' ? 'selected' : '' }}>-- Pilih
                                    Tipe --</option>
                                <option value="design"
                                    {{ old('tipe', $isEdit ? $portofolio->tipe : '') == 'Design' ? 'selected' : '' }}>Hanya
                                    Desain (Design)</option>
                                <option value="build"
                                    {{ old('tipe', $isEdit ? $portofolio->tipe : '') == 'Build' ? 'selected' : '' }}>
                                    Konstruksi Nyata (Build)</option>
                            </select>
                            @error('tipe')
                                <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Input Pemilik --}}
                    <div class="mb-5">
                        <label for="pemilik" class="block text-sm font-bold text-gray-700 mb-2">Pemilik / Klien <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="pemilik" id="pemilik"
                            class="form-input w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm transition-all @error('pemilik') border-red-500 @enderror"
                            value="{{ old('pemilik', $portofolio->pemilik ?? '') }}"
                            placeholder="Masukkan Nama Pemilik atau Instansi" required>
                        @error('pemilik')
                            <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Input Alamat --}}
                    <div class="mb-5 flex-grow flex flex-col">
                        <label for="alamat" class="block text-sm font-bold text-gray-700 mb-2">Alamat Lengkap Proyek <span
                                class="text-red-500">*</span></label>
                        <textarea name="alamat" id="alamat" rows="4"
                            class="form-textarea w-full flex-grow px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm transition-all resize-none @error('alamat') border-red-500 @enderror"
                            placeholder="Contoh: Perumahan Graha Indah Blok A1, Kec. Sidoarjo, Kab. Sidoarjo" required>{{ old('alamat', $portofolio->alamat ?? '') }}</textarea>
                        @error('alamat')
                            <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                {{-- BAGIAN KANAN: MEDIA & SUBMIT --}}
                <div class="lg:col-span-4 flex flex-col gap-6">

                    {{-- Box Upload Media --}}
                    <div
                        class="bg-white p-6 md:p-8 rounded-3xl shadow-[0_4px_25px_rgba(0,0,0,0.03)] border border-gray-100">
                        <h5 class="text-lg font-bold text-gray-800 m-0 mb-6 pb-4 border-b border-gray-100">Gambar Proyek
                        </h5>

                        <div class="mb-2 flex items-center justify-between">
                            <label
                                class="block text-sm font-bold text-gray-700">{{ $isEdit ? 'Ganti Gambar' : 'Unggah Gambar' }}</label>
                            @if ($isEdit)
                                <span
                                    class="text-[10px] bg-gray-100 text-gray-500 px-2 py-1 rounded font-bold uppercase">Opsional</span>
                            @endif
                        </div>

                        {{-- Input File Hidden --}}
                        <input type="file" name="gambar" id="gambar" class="hidden" accept="image/*"
                            onchange="previewImage(this)" {{ $isEdit ? '' : 'required' }}>

                        {{-- Area Drop/Preview Gambar --}}
                        @php $existingImg = $isEdit && $portofolio->gambar ? asset('storage/' . $portofolio->gambar) : null; @endphp

                        <label for="gambar"
                            class="relative block w-full aspect-[4/3] rounded-2xl border-2 border-dashed border-gray-300 bg-gray-50 cursor-pointer hover:bg-[#f0fdfa] hover:border-[#0d9488] transition-all duration-300 overflow-hidden group">

                            {{-- Jika ada gambar --}}
                            <img id="image-preview" src="{{ $existingImg ?? '' }}" alt="Preview"
                                class="absolute inset-0 w-full h-full object-cover z-10 transition-transform duration-500 group-hover:scale-105"
                                style="display: {{ $existingImg ? 'block' : 'none' }};">

                            {{-- Overlay Gelap saat Hover (Hanya jika ada gambar) --}}
                            <div id="image-overlay"
                                class="absolute inset-0 bg-black/40 flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity z-20"
                                style="display: {{ $existingImg ? 'flex' : 'none' }};">
                                <i class="bi bi-pencil-square text-white text-2xl mb-1"></i>
                                <span class="text-white text-xs font-bold">Ganti Gambar</span>
                            </div>

                            {{-- Placeholder jika kosong --}}
                            <div id="upload-content"
                                class="absolute inset-0 flex flex-col items-center justify-center text-gray-400 z-0"
                                style="display: {{ $existingImg ? 'none' : 'flex' }};">
                                <svg class="w-10 h-10 mb-2 text-gray-300 group-hover:text-[#0d9488] transition-colors"
                                    fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                </svg>
                                <h6
                                    class="text-sm font-bold text-gray-700 mb-1 group-hover:text-[#0d9488] transition-colors">
                                    Pilih File Gambar</h6>
                                <p class="text-xs text-gray-400 m-0">Maks. 2MB (JPG, PNG)</p>
                            </div>
                        </label>

                        @error('gambar')
                            <p class="text-red-500 text-xs mt-2 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Box Aksi Submit --}}
                    <div
                        class="bg-white p-6 rounded-3xl shadow-[0_4px_25px_rgba(0,0,0,0.03)] border border-gray-100 flex flex-col gap-3">
                        <button type="submit" id="submitBtn"
                            class="w-full flex items-center justify-center px-6 py-3.5 bg-[#0d9488] hover:bg-teal-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-teal-600/30 transform hover:-translate-y-0.5">
                            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
                            </svg>
                            {{ $isEdit ? 'Simpan Perubahan' : 'Publish Portofolio' }}
                        </button>

                        @if ($isEdit)
                            <a href="{{ route('portofolios.index') }}"
                                class="w-full flex items-center justify-center px-6 py-3 bg-gray-50 hover:bg-gray-100 text-gray-600 font-bold rounded-xl transition-all no-underline">
                                Batal Edit
                            </a>
                        @else
                            <button type="reset" onclick="resetImage()"
                                class="w-full flex items-center justify-center px-6 py-3 bg-gray-50 hover:bg-gray-100 text-gray-600 font-bold rounded-xl transition-all">
                                Reset Form
                            </button>
                        @endif
                    </div>

                </div>

            </div>
        </form>

        {{-- ==========================================================
         MODAL CROPPER (TAILWIND THEME DISCORD)
         ========================================================== --}}
        <div id="cropModal"
            class="fixed inset-0 z-[9999] hidden items-center justify-center bg-black/80 backdrop-blur-sm opacity-0 transition-opacity duration-300">
            <div class="bg-[#2b2d31] w-[95%] max-w-4xl rounded-2xl shadow-2xl overflow-hidden flex flex-col transform scale-95 transition-transform duration-300"
                id="cropModalContent">

                {{-- Modal Header --}}
                <div class="px-6 py-4 border-b border-[#1e1f22] flex justify-between items-center bg-[#2b2d31]">
                    <h5 class="text-[#f2f3f5] font-bold text-lg m-0">Sesuaikan Posisi Gambar</h5>
                    <button type="button" onclick="cancelCrop()"
                        class="text-gray-400 hover:text-white transition-colors focus:outline-none">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Modal Body (Area Cropper) --}}
                <div class="w-full bg-black h-[50vh] md:h-[65vh] flex items-center justify-center relative">
                    <img id="image-to-crop" src="" alt="Picture" class="max-w-full max-h-full block">
                </div>

                {{-- Modal Footer --}}
                <div class="px-6 py-4 border-t border-[#1e1f22] flex justify-end gap-3 bg-[#2b2d31]">
                    <button type="button" onclick="cancelCrop()"
                        class="px-5 py-2 text-[#dbdee1] hover:text-white font-medium hover:underline transition-all">
                        Batal
                    </button>
                    <button type="button" id="cropButton"
                        class="px-6 py-2 bg-[#0d9488] hover:bg-teal-500 text-white font-bold rounded-lg transition-all shadow-lg">
                        Terapkan Potongan
                    </button>
                </div>

            </div>
        </div>

    </div>
@endsection

@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

    <script>
        // Variabel Global
        let cropper = null;
        let selectedOriginalFile = null;

        const imageToCrop = document.getElementById('image-to-crop');
        const modalElement = document.getElementById('cropModal');
        const modalContent = document.getElementById('cropModalContent');
        const fileInput = document.getElementById('gambar');
        const preview = document.getElementById('image-preview');
        const content = document.getElementById('upload-content');
        const overlay = document.getElementById('image-overlay');

        // Referensi gambar lama
        const existingImageUrl = "{{ $existingImg ?? '' }}";

        // ==========================================
        // 1. TAMPILKAN MODAL & INIT CROPPER
        // ==========================================
        function previewImage(input) {
            if (input.files && input.files[0]) {
                selectedOriginalFile = input.files[0];
                const reader = new FileReader();

                reader.onload = function(e) {
                    imageToCrop.src = e.target.result;
                    openModal();

                    // Inisialisasi Cropper setelah modal tampil
                    setTimeout(() => {
                        if (cropper) cropper.destroy();
                        cropper = new Cropper(imageToCrop, {
                            aspectRatio: 4 / 3, // Menggunakan rasio 4:3 agar pas di grid
                            viewMode: 1,
                            dragMode: 'move',
                            autoCropArea: 0.9,
                            guides: false,
                            center: false,
                            highlight: false,
                            background: false,
                            cropBoxMovable: true,
                            cropBoxResizable: true,
                            toggleDragModeOnDblclick: false,
                        });
                    }, 100);
                }
                reader.readAsDataURL(selectedOriginalFile);
            }
        }

        // ==========================================
        // 2. KONTROL TAMPILAN MODAL (TAILWIND)
        // ==========================================
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
            fileInput.value = ''; // Reset input agar bisa pilih file yg sama lagi
        }

        // ==========================================
        // 3. PROSES CROP & KOMPRESI WEBP
        // ==========================================
        document.getElementById('cropButton').addEventListener('click', function() {
            if (!cropper) return;

            const originalBtnText = this.innerHTML;
            this.innerHTML =
                '<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Memproses...';
            this.disabled = true;

            const canvas = cropper.getCroppedCanvas({
                width: 800,
                height: 600, // Menyesuaikan rasio 4:3
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high'
            });

            let quality = 0.90;
            const targetSizeByte = 40 * 1024;

            function compressCanvas() {
                canvas.toBlob(function(blob) {
                    if (!blob) {
                        alert("Gagal memproses gambar.");
                        resetModalBtn();
                        return;
                    }
                    if (blob.size > targetSizeByte && quality > 0.1) {
                        quality -= 0.1;
                        compressCanvas();
                    } else {
                        // Ekstrak nama asli
                        let originalNameBase = selectedOriginalFile.name.substring(0, selectedOriginalFile
                            .name.lastIndexOf('.')) || selectedOriginalFile.name;
                        let newFileName = originalNameBase + "_" + Math.floor(Date.now() / 1000) + ".webp";

                        const webpFile = new File([blob], newFileName, {
                            type: 'image/webp',
                            lastModified: Date.now()
                        });

                        // Tampilkan hasil crop ke Box Preview
                        preview.src = URL.createObjectURL(blob);
                        preview.style.display = 'block';
                        content.style.display = 'none';
                        overlay.style.display = 'flex'; // Aktifkan overlay edit

                        // Pindahkan file hasil crop ke dalam input file form
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(webpFile);
                        fileInput.files = dataTransfer.files;

                        closeModal();
                        resetModalBtn();
                    }
                }, 'image/webp', quality);
            }

            setTimeout(compressCanvas, 100);

            function resetModalBtn() {
                const btn = document.getElementById('cropButton');
                btn.innerHTML = originalBtnText;
                btn.disabled = false;
            }
        });

        // ==========================================
        // 4. RESET FORM & GAMBAR
        // ==========================================
        function resetImage() {
            if (existingImageUrl !== "") {
                // Jika Edit, kembalikan gambar lama
                preview.src = existingImageUrl;
                preview.style.display = 'block';
                content.style.display = 'none';
                overlay.style.display = 'flex';
            } else {
                // Jika Create, kosongkan
                preview.style.display = 'none';
                preview.src = '';
                content.style.display = 'flex';
                overlay.style.display = 'none';
            }
            fileInput.value = '';
            selectedOriginalFile = null;
        }
    </script>
@endsection
