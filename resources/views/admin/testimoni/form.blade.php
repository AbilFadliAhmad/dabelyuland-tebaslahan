@extends('layouts.admin')

@section('style')
    {{-- Memanggil CSS Cropper.js --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" />
    <style>
        /* Styling Dasar Override (Font & Transisi) */
        .font-heading {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .font-body {
            font-family: 'Inter', sans-serif;
        }

        /* Custom Focus State untuk Input */
        .form-input:focus,
        .form-textarea:focus {
            border-color: #0d9488;
            box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.15);
            outline: none;
        }

        /* Efek Hover untuk Kotak Upload Foto Profil */
        .avatar-upload-box {
            transition: all 0.3s ease;
        }

        .avatar-upload-box:hover {
            border-color: #0d9488;
            background-color: #f0fdfa;
        }

        /* Rating Stars Custom CSS */
        .star-container {
            position: relative;
            display: inline-flex;
            cursor: pointer;
        }

        .star-base,
        .star-fill {
            display: flex;
            align-items: center;
        }

        .star-base svg {
            color: #e5e7eb;
            /* gray-200 */
        }

        .star-fill {
            position: absolute;
            top: 0;
            left: 0;
            overflow: hidden;
            white-space: nowrap;
            pointer-events: none;
        }

        .star-fill svg {
            color: #f59e0b;
            /* amber-500 */
        }

        /* Transisi halus saat bintang terisi penuh/setengah */
        #interactive-stars {
            transition: transform 0.1s ease;
        }

        #interactive-stars:active {
            transform: scale(0.95);
        }

        /* Hapus tombol panah (spinner) bawaan browser pada input number */
        .rating-input-field::-webkit-inner-spin-button,
        .rating-input-field::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .rating-input-field {
            -moz-appearance: textfield;
            background: transparent;
            border: none;
            outline: none;
            width: 45px;
            text-align: center;
        }

        .rating-input-field:focus {
            border-bottom: 2px solid #d97706;
            background-color: #fef3c7;
            border-radius: 4px;
        }

        /* ==========================================
                   STYLING KHUSUS CROPPER AVATAR BULAT
                   ========================================== */
        .img-container {
            max-height: 50vh;
            width: 100%;
            background-color: #000;
            text-align: center;
            overflow: hidden;
        }

        .img-container img {
            display: block;
            max-width: 100%;
            max-height: 50vh;
            margin: 0 auto;
        }

        /* Mengubah kotak seleksi crop menjadi bulat (visual saja) */
        .cropper-view-box,
        .cropper-face {
            border-radius: 50%;
        }

        /* Menyembunyikan garis bantu karena ini avatar bulat */
        .cropper-view-box {
            outline: 2px solid #0d9488;
            outline-color: rgba(13, 148, 136, 0.75);
        }

        .cropper-line,
        .cropper-point,
        .cropper-center {
            display: none !important;
        }
    </style>
@endsection

@section('content')
    <div id="main" class="min-h-screen bg-gray-50/50 p-4 sm:p-6 lg:p-8 font-['Inter']">

        {{-- HEADER & BREADCRUMB --}}
        <div class="mb-8 max-w-5xl mx-auto flex flex-col md:flex-row justify-between md:items-center gap-4">
            <div>
                <h2 class="text-2xl font-extrabold text-gray-900 m-0 font-['Plus_Jakarta_Sans'] tracking-tight">
                    {{ $isEdit ? 'Edit Testimoni' : 'Tambah Testimoni Baru' }}
                </h2>
                <p class="text-sm text-gray-500 m-0 mt-1">
                    {{ $isEdit ? 'Perbarui data dan ulasan klien.' : 'Masukkan data ulasan atau feedback dari klien baru.' }}
                </p>
            </div>

            <a href="{{ route('testimonis.index') }}"
                class="inline-flex items-center px-4 py-2.5 bg-white border border-gray-200 hover:bg-gray-50 text-gray-600 text-sm font-bold rounded-xl transition-colors no-underline shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                Kembali
            </a>
        </div>

        {{-- FORM UTAMA TESTIMONI --}}
        <form action="{{ route($isEdit ? 'testimonis.update' : 'testimonis.store', $isEdit ? $testimoni->id : null) }}"
            method="POST" enctype="multipart/form-data" class="max-w-5xl mx-auto" id="testimoniForm">

            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8">

                {{-- KOLOM KIRI: Data Teks --}}
                <div
                    class="lg:col-span-8 bg-white p-6 md:p-8 rounded-3xl shadow-[0_4px_25px_rgba(0,0,0,0.03)] border border-gray-100 flex flex-col h-full">

                    <h5 class="text-lg font-bold text-gray-800 m-0 mb-6 pb-4 border-b border-gray-100">Informasi Klien</h5>

                    {{-- Input Nama Klien --}}
                    <div class="mb-5">
                        <label for="nama" class="block text-sm font-bold text-gray-700 mb-2">Nama Klien <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="nama" id="nama"
                            class="form-input w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm transition-all @error('nama') border-red-500 @enderror"
                            value="{{ old('nama', $isEdit ? $testimoni->nama : '') }}"
                            placeholder="Masukkan Nama Lengkap Klien" required>
                        @error('nama')
                            <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Input Pekerjaan --}}
                    <div class="mb-5">
                        <label for="pekerjaan" class="block text-sm font-bold text-gray-700 mb-2">Pekerjaan /
                            Instansi</label>
                        <input type="text" name="pekerjaan" id="pekerjaan"
                            class="form-input w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm transition-all @error('pekerjaan') border-red-500 @enderror"
                            value="{{ old('pekerjaan', $isEdit ? $testimoni->pekerjaan : '') }}"
                            placeholder="Contoh: Pengusaha / Pegawai Negeri Sipil">
                        <p class="text-xs text-gray-400 mt-1 mb-0">Opsional, namun membantu meningkatkan kredibilitas
                            testimoni.</p>
                        @error('pekerjaan')
                            <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Input Rating --}}
                    <div class="mb-5 bg-amber-50/50 border border-amber-100 p-4 rounded-2xl">
                        <label class="block text-sm font-bold text-gray-800 mb-1">Penilaian (Rating) <span
                                class="text-red-500">*</span></label>
                        <p class="text-xs text-gray-500 mb-3">Geser mouse pada bintang atau <strong>ketik angka secara
                                langsung</strong> (misal: 4.8).</p>

                        <div class="flex items-center gap-4">
                            <div class="star-container" id="interactive-stars" onmousemove="handleStarMove(event)"
                                onmouseleave="handleStarLeave()" onclick="setRating()">
                                <div class="star-base">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                            </path>
                                        </svg>
                                    @endfor
                                </div>
                                <div class="star-fill" id="star-fill" style="width: 0%;">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <svg class="w-8 h-8 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                            </path>
                                        </svg>
                                    @endfor
                                </div>
                            </div>

                            <div
                                class="bg-amber-100 px-3 py-1.5 rounded-lg border border-amber-200 shadow-sm flex items-center transition-all focus-within:ring-2 focus-within:ring-amber-400 focus-within:bg-white">
                                <input type="number" name="rating" id="rating-input" step="0.1" min="0"
                                    max="5" class="rating-input-field font-bold text-amber-700 text-lg"
                                    value="{{ old('rating', $isEdit ? $testimoni->rating : 0) }}" required>
                                <span class="text-amber-600 text-xs font-bold">/ 5.0</span>
                            </div>
                        </div>
                        @error('rating')
                            <p class="text-red-500 text-xs mt-2 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Input Isi Testimoni --}}
                    <div class="mb-2 flex-grow flex flex-col">
                        <label for="testimoni" class="block text-sm font-bold text-gray-700 mb-2">Isi Testimoni <span
                                class="text-red-500">*</span></label>
                        <textarea name="testimoni" id="testimoni" rows="5"
                            class="form-textarea w-full flex-grow px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm transition-all resize-none @error('testimoni') border-red-500 @enderror"
                            placeholder="Tuliskan ulasan, kesan, atau pengalaman klien di sini..." required>{{ old('testimoni', $isEdit ? $testimoni->testimoni : '') }}</textarea>
                        @error('testimoni')
                            <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                {{-- KOLOM KANAN: Foto Profil & Submit --}}
                <div class="lg:col-span-4 flex flex-col gap-6">

                    {{-- Box Upload Foto Profil Klien --}}
                    <div
                        class="bg-white p-6 md:p-8 rounded-3xl shadow-[0_4px_25px_rgba(0,0,0,0.03)] border border-gray-100 text-center flex flex-col items-center">
                        <h5 class="text-lg font-bold text-gray-800 m-0 mb-6 pb-4 border-b border-gray-100 w-full text-left">
                            Foto Klien</h5>

                        {{-- Input File Hidden --}}
                        <input type="file" name="foto" id="foto" class="hidden" accept="image/*"
                            onchange="openCropper(this)">

                        @php $existingImg = $isEdit && $testimoni->foto ? asset('storage/' . $testimoni->foto) : null; @endphp

                        {{-- Trigger Buka Cropper --}}
                        <label for="foto"
                            class="avatar-upload-box relative w-40 h-40 rounded-full border-4 border-dashed border-gray-300 bg-gray-50 cursor-pointer flex items-center justify-center overflow-hidden group shadow-sm mb-4">

                            <img id="avatar-preview" src="{{ $existingImg ?? '' }}" alt="Preview"
                                class="absolute inset-0 w-full h-full object-cover z-10 transition-transform duration-500 group-hover:scale-105"
                                style="display: {{ $existingImg ? 'block' : 'none' }};">

                            <div id="avatar-overlay"
                                class="absolute inset-0 bg-black/50 flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity z-20"
                                style="display: {{ $existingImg ? 'flex' : 'none' }};">
                                <i class="fas fa-camera text-white text-2xl mb-1"></i>
                                <span class="text-white text-[10px] font-bold uppercase tracking-wider">Ganti Foto</span>
                            </div>

                            <div id="avatar-placeholder"
                                class="flex flex-col items-center justify-center text-gray-400 z-0"
                                style="display: {{ $existingImg ? 'none' : 'flex' }};">
                                <svg class="w-10 h-10 mb-1 text-gray-300 group-hover:text-[#0d9488] transition-colors"
                                    fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                </svg>
                                <span
                                    class="text-[10px] font-bold uppercase tracking-wider group-hover:text-[#0d9488]">Pilih
                                    Foto</span>
                            </div>
                        </label>

                        <p class="text-xs text-gray-500 m-0 leading-relaxed">
                            @if ($isEdit)
                                Kosongkan jika tidak ingin mengganti foto saat ini.
                            @else
                                Pilih foto untuk memotong ke rasio 1:1. Maksimal 2MB.
                            @endif
                        </p>

                        @error('foto')
                            <p class="text-red-500 text-xs mt-2 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Box Aksi Submit & Batal --}}
                    <div
                        class="bg-white p-6 rounded-3xl shadow-[0_4px_25px_rgba(0,0,0,0.03)] border border-gray-100 flex flex-col gap-3">
                        <button type="submit"
                            class="w-full flex items-center justify-center px-6 py-3.5 bg-[#0d9488] hover:bg-teal-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-teal-600/30 transform hover:-translate-y-0.5">
                            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
                            </svg>
                            {{ $isEdit ? 'Simpan Perubahan' : 'Publish Testimoni' }}
                        </button>

                        @if ($isEdit)
                            <a href="{{ route('testimonis.index') }}"
                                class="w-full flex items-center justify-center px-6 py-3 bg-gray-50 hover:bg-gray-100 text-gray-600 font-bold rounded-xl transition-all no-underline">
                                Batal Edit
                            </a>
                        @else
                            <button type="button" onclick="resetFormAndAvatar()"
                                class="w-full flex items-center justify-center px-6 py-3 bg-gray-50 hover:bg-gray-100 text-gray-600 font-bold rounded-xl transition-all">
                                Reset Form
                            </button>
                        @endif
                    </div>

                </div>

            </div>
        </form>
    </div>

    {{-- ==========================================================
         MODAL CROPPER JS
         ========================================================== --}}
    <div id="cropModal"
        class="fixed inset-0 z-[9999] hidden items-center justify-center bg-black/80 backdrop-blur-sm opacity-0 transition-opacity duration-300">
        <div class="bg-[#2b2d31] w-[95%] max-w-xl rounded-2xl shadow-2xl overflow-hidden flex flex-col transform scale-95 transition-transform duration-300"
            id="cropModalContent">
            <div class="px-6 py-4 border-b border-[#1e1f22] flex justify-between items-center bg-[#2b2d31] shrink-0">
                <h5 class="text-[#f2f3f5] font-bold text-lg m-0 font-['Plus_Jakarta_Sans']">Sesuaikan Foto Klien</h5>
                <button type="button" onclick="cancelCrop()"
                    class="text-gray-400 hover:text-white transition-colors focus:outline-none">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="w-full bg-black h-[50vh] flex items-center justify-center relative img-container">
                <img id="image-to-crop" src="" alt="Picture" class="max-w-full max-h-full block">
            </div>
            <div class="px-6 py-4 border-t border-[#1e1f22] flex justify-end gap-3 bg-[#2b2d31] shrink-0">
                <button type="button" onclick="cancelCrop()"
                    class="px-5 py-2 text-[#dbdee1] hover:text-white font-medium hover:underline transition-all">Batal</button>
                <button type="button" id="btn-crop"
                    class="px-6 py-2 bg-[#0d9488] hover:bg-teal-500 text-white font-bold rounded-lg transition-all shadow-lg flex items-center">
                    <i class="fas fa-crop-alt mr-2"></i> Potong & Simpan
                </button>
            </div>
        </div>
    </div>
@endsection

@section('script')
    {{-- Memanggil Javascript Cropper.js --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

    <script>
        // ==========================================
        // 1. LOGIKA INTERACTIVE STAR RATING DECIMAL
        // ==========================================
        const starContainer = document.getElementById('interactive-stars');
        const starFill = document.getElementById('star-fill');
        const ratingInput = document.getElementById('rating-input');

        let currentLockedRating = parseFloat(ratingInput.value) || 0;
        let hoverRatingValue = 0;

        document.addEventListener('DOMContentLoaded', () => {
            updateStarVisual(currentLockedRating);
            ratingInput.value = currentLockedRating.toFixed(1);
        });

        ratingInput.addEventListener('input', function() {
            let val = parseFloat(this.value);
            if (isNaN(val)) val = 0;
            if (val < 0) val = 0;
            if (val > 5) val = 5;
            currentLockedRating = val;
            updateStarVisual(val);
        });

        ratingInput.addEventListener('blur', function() {
            let val = parseFloat(this.value);
            if (isNaN(val) || val < 0) val = 0;
            if (val > 5) val = 5;
            this.value = val.toFixed(1);
            currentLockedRating = val;
            updateStarVisual(val);
        });

        function calculateRating(event) {
            const rect = starContainer.getBoundingClientRect();
            let x = event.clientX - rect.left;
            let ratio = x / rect.width;
            let calculatedRating = ratio * 5;
            calculatedRating = Math.round(calculatedRating * 10) / 10;
            if (calculatedRating < 0.1) calculatedRating = 0.1;
            if (calculatedRating > 5) calculatedRating = 5;
            return calculatedRating;
        }

        function updateStarVisual(rating) {
            const percentage = (rating / 5) * 100;
            starFill.style.width = `${percentage}%`;
        }

        function handleStarMove(event) {
            hoverRatingValue = calculateRating(event);
            updateStarVisual(hoverRatingValue);
            ratingInput.value = hoverRatingValue.toFixed(1);
        }

        function handleStarLeave() {
            updateStarVisual(currentLockedRating);
            ratingInput.value = currentLockedRating.toFixed(1);
        }

        function setRating() {
            currentLockedRating = hoverRatingValue;
            ratingInput.value = currentLockedRating.toFixed(1);
            ratingInput.classList.add('text-amber-500');
            setTimeout(() => ratingInput.classList.remove('text-amber-500'), 150);
        }


        // ==========================================
        // 2. LOGIKA CROPPER JS UNTUK AVATAR (1:1)
        // ==========================================
        let cropper = null;
        let selectedOriginalFile = null;

        const imageToCrop = document.getElementById('image-to-crop');
        const modalElement = document.getElementById('cropModal');
        const modalContent = document.getElementById('cropModalContent');
        const fileInput = document.getElementById('foto');

        const previewEl = document.getElementById('avatar-preview');
        const placeholderEl = document.getElementById('avatar-placeholder');
        const overlayEl = document.getElementById('avatar-overlay');

        // Fungsi dipanggil saat file dipilih
        function openCropper(input) {
            if (input.files && input.files[0]) {
                selectedOriginalFile = input.files[0];
                const reader = new FileReader();

                reader.onload = function(e) {
                    imageToCrop.src = e.target.result;
                    openModal();
                    imageToCrop.onload = function() {
                        if (cropper) cropper.destroy();
                        // Inisialisasi Cropper dengan rasio 1:1
                        cropper = new Cropper(imageToCrop, {
                            aspectRatio: 1 / 1,
                            viewMode: 1,
                            dragMode: 'move',
                            autoCropArea: 0.95,
                            guides: false, // Disembunyikan karena frame-nya bulat
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
            // Jika membatalkan crop, kosongkan input file
            fileInput.value = '';
        }

        // Eksekusi potong gambar
        document.getElementById('btn-crop').addEventListener('click', function() {
            if (!cropper) return;
            const originalBtnText = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Memproses...';
            this.disabled = true;

            // Atur ukuran hasil potongan ke 500x500 px agar tidak terlalu besar
            const canvas = cropper.getCroppedCanvas({
                width: 500,
                height: 500,
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

                // Tampilkan hasil crop ke lingkaran avatar di form
                previewEl.src = URL.createObjectURL(blob);
                previewEl.style.display = 'block';
                placeholderEl.style.display = 'none';
                overlayEl.style.display = 'flex';

                // Masukkan file hasil crop ke dalam input file tersembunyi
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(newFile);
                fileInput.files = dataTransfer.files;

                closeModal();
                resetModalBtn(originalBtnText);
            }, 'image/jpeg', 0.85); // Kualitas 85% untuk kompresi ringan
        });

        function resetModalBtn(text) {
            const btn = document.getElementById('btn-crop');
            btn.innerHTML = text;
            btn.disabled = false;
        }

        // ==========================================
        // 3. LOGIKA RESET FORM
        // ==========================================
        function resetFormAndAvatar() {
            document.getElementById('nama').value = '';
            document.getElementById('pekerjaan').value = '';
            document.getElementById('testimoni').value = '';

            const isEditMode = {{ $isEdit ? 'true' : 'false' }};
            const hasOldImage = "{{ $existingImg ?? '' }}" !== "";

            // Reset Gambar
            if (isEditMode && hasOldImage) {
                previewEl.src = "{{ $existingImg ?? '' }}";
                previewEl.style.display = 'block';
                overlayEl.style.display = 'flex';
                placeholderEl.style.display = 'none';
            } else {
                previewEl.src = "";
                previewEl.style.display = 'none';
                overlayEl.style.display = 'none';
                placeholderEl.style.display = 'flex';
            }
            fileInput.value = '';

            // Reset Rating
            currentLockedRating = 0;
            ratingInput.value = (0).toFixed(1);
            updateStarVisual(0);
        }
    </script>
@endsection
