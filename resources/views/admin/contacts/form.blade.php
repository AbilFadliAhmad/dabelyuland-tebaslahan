@extends('layouts.admin')

@section('style')
    {{-- Memanggil CSS Cropper.js --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" />
    <style>
        /* Styling Dasar Override */
        .font-heading {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .font-body {
            font-family: 'Inter', sans-serif;
        }

        /* Custom Focus State untuk Input */
        .form-input:focus {
            border-color: #0d9488;
            box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.15);
            outline: none;
        }

        /* Efek Hover untuk Kotak Upload Foto (Avatar) */
        .avatar-upload-box {
            transition: all 0.3s ease;
        }

        .avatar-upload-box:hover {
            border-color: #0d9488;
            background-color: #f0fdfa;
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

        {{-- ==========================================================
         HEADER & TOMBOL KEMBALI
         ========================================================== --}}
        <div class="mb-8 max-w-5xl mx-auto flex flex-col md:flex-row justify-between md:items-center gap-4">
            <div>
                <h2 class="text-2xl font-extrabold text-gray-900 m-0 font-['Plus_Jakarta_Sans'] tracking-tight">
                    {{ $isEdit ? 'Edit Data Kontak' : 'Tambah Kontak Baru' }}
                </h2>
                <p class="text-sm text-gray-500 m-0 mt-1">
                    {{ $isEdit ? 'Perbarui informasi detail kontak atau tim Anda.' : 'Masukkan informasi kontak atau anggota tim baru ke dalam sistem.' }}
                </p>
            </div>

            <a href="{{ route('contacts.index') }}"
                class="inline-flex items-center px-4 py-2.5 bg-white border border-gray-200 hover:bg-gray-50 text-gray-600 text-sm font-bold rounded-xl transition-colors no-underline shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                Kembali ke Daftar
            </a>
        </div>

        {{-- ==========================================================
         FORM UTAMA KONTAK
         ========================================================== --}}
        <form action="{{ route(!$isEdit ? 'contacts.store' : 'contacts.update', !$isEdit ? null : $contact->id) }}"
            method="POST" enctype="multipart/form-data" class="max-w-5xl mx-auto" id="contactForm">

            @csrf
            @method($isEdit ? 'PUT' : 'POST')

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8">

                {{-- =======================================
                 KOLOM KIRI: Informasi Detail Teks
                 ======================================= --}}
                <div
                    class="lg:col-span-8 bg-white p-6 md:p-8 rounded-3xl shadow-[0_4px_25px_rgba(0,0,0,0.03)] border border-gray-100 flex flex-col h-full">

                    <h5 class="text-lg font-bold text-gray-800 m-0 mb-6 pb-4 border-b border-gray-100">Informasi Dasar</h5>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                        {{-- Input Nama --}}
                        <div class="md:col-span-2">
                            <label for="nama" class="block text-sm font-bold text-gray-700 mb-2">Nama Lengkap <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="nama" id="nama"
                                class="form-input w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm transition-all @error('nama') border-red-500 @enderror"
                                value="{{ old('nama', !$isEdit ? '' : $contact->nama) }}"
                                placeholder="Masukkan nama lengkap" required>
                            @error('nama')
                                <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Input Jabatan --}}
                        <div>
                            <label for="jabatan" class="block text-sm font-bold text-gray-700 mb-2">Posisi /
                                Jabatan</label>
                            <input type="text" name="jabatan" id="jabatan"
                                class="form-input w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm transition-all @error('jabatan') border-red-500 @enderror"
                                value="{{ old('jabatan', !$isEdit ? '' : $contact->jabatan) }}"
                                placeholder="Contoh: Sales Marketing">
                            @error('jabatan')
                                <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Input Email --}}
                        <div>
                            <label for="email" class="block text-sm font-bold text-gray-700 mb-2">Alamat Email</label>
                            <input type="email" name="email" id="email"
                                class="form-input w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm transition-all @error('email') border-red-500 @enderror"
                                value="{{ old('email', !$isEdit ? '' : $contact->email) }}"
                                placeholder="email@perusahaan.com">
                            @error('email')
                                <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Input No WhatsApp --}}
                    <div class="mb-5 flex-grow">
                        <label for="nowa" class="block text-sm font-bold text-gray-700 mb-2">Nomor WhatsApp <span
                                class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="bi bi-whatsapp text-green-500 text-lg"></i>
                            </div>
                            <input type="text" name="nowa" id="nowa"
                                class="form-input w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm transition-all font-semibold @error('nowa') border-red-500 @enderror"
                                value="{{ old('nowa', !$isEdit ? '' : $contact->nowa) }}"
                                placeholder="Contoh: 081234567890" required>
                        </div>
                        <p class="text-xs text-gray-400 mt-1.5 mb-0">Masukkan nomor sesuai format yang dicontohkan</p>
                        @error('nowa')
                            <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                {{-- =======================================
                 KOLOM KANAN: Foto Profil & Aksi Submit
                 ======================================= --}}
                <div class="lg:col-span-4 flex flex-col gap-6">

                    {{-- Box Upload Foto --}}
                    <div
                        class="bg-white p-6 md:p-8 rounded-3xl shadow-[0_4px_25px_rgba(0,0,0,0.03)] border border-gray-100 text-center flex flex-col items-center">
                        <h5 class="text-lg font-bold text-gray-800 m-0 mb-6 pb-4 border-b border-gray-100 w-full text-left">
                            Foto Profil</h5>

                        {{-- Input File Asli (Hidden) - Menggunakan onchange dari Cropper --}}
                        <input type="file" name="foto" id="foto" class="hidden" accept="image/*"
                            onchange="openCropper(this)">

                        {{-- Area Lingkaran Drop/Preview Foto --}}
                        @php $existingImg = $isEdit && $contact->foto ? asset('storage/' . $contact->foto) : null; @endphp

                        <label for="foto"
                            class="avatar-upload-box relative w-40 h-40 rounded-full border-4 border-dashed border-gray-300 bg-gray-50 cursor-pointer flex items-center justify-center overflow-hidden group shadow-sm mb-4">

                            {{-- Menampilkan Gambar (Baru / Lama) --}}
                            <img id="avatar-preview" src="{{ $existingImg ?? '' }}" alt="Preview Foto"
                                class="absolute inset-0 w-full h-full object-cover z-10 transition-transform duration-500 group-hover:scale-105"
                                style="display: {{ $existingImg ? 'block' : 'none' }};">

                            {{-- Overlay Gelap saat Kursor Hover (Hanya jika gambar sudah ada) --}}
                            <div id="avatar-overlay"
                                class="absolute inset-0 bg-black/50 flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity z-20"
                                style="display: {{ $existingImg ? 'flex' : 'none' }};">
                                <i class="bi bi-camera text-white text-2xl mb-1"></i>
                                <span class="text-white text-[10px] font-bold uppercase tracking-wider">Ganti Foto</span>
                            </div>

                            {{-- Placeholder Default (Jika belum ada gambar) --}}
                            <div id="avatar-placeholder" class="flex flex-col items-center justify-center text-gray-400 z-0"
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
                                Kosongkan jika tidak ingin mengubah foto saat ini.
                            @else
                                Pilih foto untuk memotong ke rasio 1:1. Maksimal 2MB.
                            @endif
                        </p>

                        @error('foto')
                            <p class="text-red-500 text-xs mt-2 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Box Aksi Submit & Reset --}}
                    <div
                        class="bg-white p-6 rounded-3xl shadow-[0_4px_25px_rgba(0,0,0,0.03)] border border-gray-100 flex flex-col gap-3">

                        {{-- Tombol Utama Simpan --}}
                        <button type="submit"
                            class="w-full flex items-center justify-center px-6 py-3.5 bg-[#0d9488] hover:bg-teal-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-teal-600/30 transform hover:-translate-y-0.5">
                            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
                            </svg>
                            {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Kontak' }}
                        </button>

                        {{-- Tombol Batal/Reset --}}
                        @if ($isEdit)
                            <a href="{{ route('contacts.index') }}"
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
                <h5 class="text-[#f2f3f5] font-bold text-lg m-0 font-['Plus_Jakarta_Sans']">Sesuaikan Foto Kontak</h5>
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
                    <i class="bi bi-crop mr-2"></i> Potong & Simpan
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
        // LOGIKA CROPPER JS UNTUK AVATAR (1:1)
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

                    // Inisialisasi Cropper setelah gambar selesai diload ke DOM
                    imageToCrop.onload = function() {
                        if (cropper) cropper.destroy();

                        cropper = new Cropper(imageToCrop, {
                            aspectRatio: 1 / 1, // Rasio Persegi 1:1
                            viewMode: 1,
                            dragMode: 'move',
                            autoCropArea: 0.95,
                            guides: false, // Sembunyikan grid bantu untuk avatar bulat
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

        // Animasi Buka Modal
        function openModal() {
            modalElement.classList.remove('hidden');
            modalElement.classList.add('flex');
            setTimeout(() => {
                modalElement.classList.remove('opacity-0');
                modalContent.classList.remove('scale-95');
            }, 10);
            document.body.style.overflow = 'hidden';
        }

        // Animasi Tutup Modal
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
            // Jika membatalkan crop, kosongkan memori input agar bisa milih gambar yang sama ulang
            fileInput.value = '';
        }

        // Aksi Tombol "Potong & Simpan"
        document.getElementById('btn-crop').addEventListener('click', function() {
            if (!cropper) return;

            const originalBtnText = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Memproses...';
            this.disabled = true;

            // Ukuran Canvas diatur ke 500x500 px agar tidak berat dikirim ke server
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

                // Tampilkan Hasil Potongan ke UI Form
                previewEl.src = URL.createObjectURL(blob);
                previewEl.style.display = 'block';
                placeholderEl.style.display = 'none';
                overlayEl.style.display = 'flex';

                // Masukkan file object baru ke input form `<input type="file" name="foto">`
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(newFile);
                fileInput.files = dataTransfer.files;

                closeModal();
                resetModalBtn(originalBtnText);
            }, 'image/jpeg', 0.85); // Compress 85% untuk efisiensi performa
        });

        function resetModalBtn(text) {
            const btn = document.getElementById('btn-crop');
            btn.innerHTML = text;
            btn.disabled = false;
        }

        // ==========================================
        // 3. LOGIKA RESET FORM (Jika Tombol Reset Diklik)
        // ==========================================
        function resetFormAndAvatar() {
            // Kosongkan Teks
            document.getElementById('nama').value = '';
            document.getElementById('jabatan').value = '';
            document.getElementById('email').value = '';
            document.getElementById('nowa').value = '';

            const isEditMode = {{ $isEdit ? 'true' : 'false' }};
            const hasOldImage = "{{ $existingImg ?? '' }}" !== "";

            // Reset Avatar (Kembali ke Default / Loncati File Input)
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
        }
    </script>
    <script src="{{ asset('assets/js/main.js') }}"></script>
@endsection
