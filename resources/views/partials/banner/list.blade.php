@extends('layouts.admin')

@section('style')
    <link rel="stylesheet" href="{{ asset('assets/vendors/sweetalert2/sweetalert2.min.css') }}">
    <style>
        /* Meng-override gaya bawaan Simple-Datatables (jika digunakan) agar selaras dengan Tailwind */
        .dataTable-wrapper {
            font-family: 'Inter', sans-serif !important;
        }

        .dataTable-input {
            border: 1px solid #e5e7eb !important;
            border-radius: 0.5rem !important;
            padding: 0.5rem 1rem !important;
            outline: none !important;
            font-size: 0.875rem !important;
        }

        .dataTable-input:focus {
            border-color: #0d9488 !important;
            box-shadow: 0 0 0 2px rgba(13, 148, 136, 0.1) !important;
        }

        .dataTable-selector {
            border: 1px solid #e5e7eb !important;
            border-radius: 0.5rem !important;
            padding: 0.4rem 1.5rem 0.4rem 0.75rem !important;
        }

        .dataTable-table>thead>tr>th {
            border-bottom: 2px solid #e5e7eb !important;
            padding-bottom: 1rem !important;
        }

        .dataTable-table>tbody>tr>td {
            vertical-align: middle !important;
            padding: 1rem !important;
            border-bottom: 1px solid #f3f4f6 !important;
        }

        .dataTable-info {
            color: #6b7280 !important;
            font-size: 0.875rem !important;
        }

        .dataTable-pagination a {
            border: 1px solid #e5e7eb !important;
            border-radius: 0.375rem !important;
            color: #4b5563 !important;
        }

        .dataTable-pagination .active a {
            background-color: #0d9488 !important;
            color: white !important;
            border-color: #0d9488 !important;
        }
    </style>
@endsection

@section('content')
    <div id="main" class="min-h-screen bg-gray-50/50 p-4 sm:p-6 lg:p-8 font-['Inter']">

        {{-- ==========================================================
         HEADER AREA & TOMBOL TAMBAH
         ========================================================== --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 m-0">Manajemen Banner</h2>
                <p class="text-sm text-gray-500 m-0 mt-1">Kelola gambar banner utama yang tampil di halaman depan website.
                </p>
            </div>

            {{-- Tombol Aksi Upload Banner --}}
            <a href="{{ route('account.banner.create') }}"
                class="inline-flex items-center px-5 py-2.5 bg-[#0d9488] hover:bg-teal-700 text-white text-sm font-bold rounded-xl transition-all shadow-md shadow-teal-600/20 hover:-translate-y-0.5 no-underline">
                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Tambah Banner
            </a>
        </div>

        {{-- ==========================================================
         AREA TABEL DAFTAR BANNER
         ========================================================== --}}
        <div class="bg-white rounded-2xl shadow-[0_2px_15px_rgba(0,0,0,0.03)] border border-gray-100 overflow-hidden">
            <div class="p-4 sm:p-6">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse" id="table1">

                        {{-- Header Tabel --}}
                        <thead>
                            <tr class="bg-gray-50/80">
                                <th
                                    class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider w-[5%] text-center">
                                    No</th>
                                <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider w-[55%]">
                                    Preview Banner</th>
                                <th
                                    class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider w-[20%] text-center">
                                    Status Visibilitas</th>
                                <th
                                    class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider w-[20%] text-center">
                                    Aksi Pengaturan</th>
                            </tr>
                        </thead>

                        {{-- Isi Tabel --}}
                        <tbody class="divide-y divide-gray-100">
                            @php $no = 1; @endphp

                            @if (isset($images) && count($images) > 0)
                                @foreach ($images as $image)
                                    <tr class="hover:bg-gray-50/50 transition-colors group">

                                        {{-- Kolom Nomor --}}
                                        <td class="py-4 px-4 text-center text-sm font-bold text-gray-400">
                                            {{ $no++ }}
                                        </td>

                                        {{-- Kolom Preview Gambar --}}
                                        <td class="py-4 px-4">
                                            @if ($image->image)
                                                <div
                                                    class="w-48 h-20 md:w-64 md:h-28 rounded-xl overflow-hidden shadow-sm border border-gray-200 cursor-pointer">
                                                    <img src="{{ asset('storage/' . $image->image) }}" alt="Preview Banner"
                                                        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                                                        loading="lazy">
                                                </div>
                                            @else
                                                <div
                                                    class="w-48 h-20 md:w-64 md:h-28 bg-gray-100 rounded-xl flex flex-col items-center justify-center text-gray-400 border border-gray-200 border-dashed">
                                                    <i class="fas fa-image text-2xl mb-1 opacity-50"></i>
                                                    <span class="text-[10px] font-medium uppercase tracking-wider">Gambar
                                                        Hilang</span>
                                                </div>
                                            @endif
                                        </td>

                                        {{-- Kolom Status Aktif/Tidak Aktif --}}
                                        <td class="py-4 px-4 text-center">
                                            @if ($image->status === 'menunggu')
                                                <span
                                                    class="inline-block px-3 py-1 bg-amber-50 text-amber-600 text-xs font-bold rounded-md border border-amber-100">
                                                    Menunggu
                                                </span>
                                            @else
                                                <label class="relative inline-flex items-center cursor-pointer">
                                                    <input type="checkbox" class="sr-only peer toggle-status"
                                                        data-id="{{ $image->id }}"
                                                        {{ $image->status == 'aktif' ? 'checked' : '' }}>
                                                    <div
                                                        class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#0d9488]">
                                                    </div>
                                                    <span
                                                        class="ml-3 text-sm font-bold min-w-[50px] {{ $image->status == 'aktif' ? 'text-[#0d9488]' : 'text-gray-500' }} status-text-{{ $image->id }}">
                                                        {{ $image->status == 'aktif' ? 'Aktif' : 'Mati' }}
                                                    </span>
                                                </label>
                                            @endif
                                        </td>

                                        {{-- Kolom Tombol Aksi --}}
                                        <td class="py-4 px-4 text-center">
                                            <div class="flex items-center justify-center gap-6">

                                                {{-- LOGIKA STATUS MENUNGGU --}}
                                                @if ($image->status === 'menunggu')
                                                    @if (auth()->user()->role === 'admin')
                                                        {{-- Tombol Verifikasi (Hanya Admin) --}}
                                                        <button onclick="verifyBanner({{ $image->id }})" type="button"
                                                            class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-50 text-blue-700 hover:bg-blue-600 hover:text-white text-xs font-bold rounded-xl transition-all shadow-sm border border-blue-100 hover:border-transparent cursor-pointer">
                                                            <i class="fas fa-clipboard-check text-sm"></i> Verifikasi
                                                        </button>
                                                    @else
                                                        {{-- Pesan Menunggu (Hanya User) --}}
                                                        <div
                                                            class="flex flex-col items-center justify-center text-amber-500">
                                                            <i class="fas fa-spinner fa-spin text-lg mb-1"></i>
                                                            <span
                                                                class="text-[10px] font-bold uppercase tracking-wider text-center leading-tight">Menunggu<br>Review
                                                                Admin</span>
                                                        </div>
                                                    @endif

                                                    {{-- LOGIKA STATUS SELAIN MENUNGGU (AKTIF/NONAKTIF) --}}
                                                @else
                                                    <a href="{{ route('account.banner.edit', $image->id) }}"
                                                        class="inline-flex items-center justify-center w-8 aspect-square rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white transition-colors shadow-sm"
                                                        title="Edit Banner">
                                                        <i class="fas fa-edit"></i>
                                                    </a>

                                                    <form action="{{ route('account.banner.destroy', $image->id) }}"
                                                        method="POST" class="m-0 p-0"
                                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus banner ini? Tindakan ini tidak dapat dibatalkan.')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="inline-flex items-center justify-center w-8 aspect-square rounded-lg bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-colors shadow-sm border-0 cursor-pointer"
                                                            title="Hapus Banner">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                @endif

                                            </div>
                                        </td>

                                    </tr>
                                @endforeach
                            @else
                                {{-- State Jika Data Kosong --}}
                                <tr>
                                    <td colspan="4" class="py-16 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <i class="fas fa-image text-gray-300 text-5xl mb-4 opacity-50"></i>
                                            <h6 class="text-lg font-bold text-gray-500 m-0 mb-1">Belum ada banner</h6>
                                            <p class="text-sm text-gray-400 m-0">Silakan klik tombol "Tambah Banner" untuk
                                                memulai.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        {{-- Footer --}}
        <footer class="mt-8 pb-6 text-center md:text-left">
            <p class="text-sm text-gray-400 m-0">2026 &copy; Dabelyuland Indonesia</p>
        </footer>

    </div>
@endsection

@section('script')
    {{-- Inisialisasi Simple-Datatables jika file plugin tersedia --}}
    <script>
        document.querySelectorAll('.toggle-status').forEach(toggle => {
            toggle.addEventListener('change', function() {
                let bannerId = this.getAttribute('data-id');
                let isChecked = this.checked ? 1 : 0;
                let statusText = document.querySelector(`.status-text-${bannerId}`);

                // Ubah teks sementara saat proses
                statusText.innerText = "...";
                let url = "{{ route('account.banner.toggle-status', ':id') }}";
                url = url.replace(':id', bannerId);
                fetch(url, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content')
                        },
                        body: JSON.stringify({
                            is_active: isChecked
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update tampilan teks sesuai status
                            statusText.innerText = isChecked ? 'Aktif' : 'Mati';
                            statusText.className =
                                `ml-3 text-sm font-bold min-w-[50px] ${isChecked ? 'text-[#0d9488]' : 'text-gray-500'} status-text-${bannerId}`;
                        } else {
                            alert('Gagal merubah status');
                            this.checked = !this.checked; // Kembalikan toggle jika gagal
                            statusText.innerText = this.checked ? 'Aktif' : 'Mati';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan jaringan');
                        this.checked = !this.checked;
                    });
            });
        });

        function verifyBanner(bannerId) {
            Swal.fire({
                title: '<span class="font-bold text-gray-800">Verifikasi Banner</span>',
                html: '<p class="text-sm text-gray-500">Tentukan apakah Anda ingin menyetujui atau menolak penayangan banner ini.</p>',
                icon: 'question',
                showDenyButton: true,
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-check mr-1"></i> Terima',
                denyButtonText: '<i class="fas fa-times mr-1"></i> Tolak',
                cancelButtonText: 'Batal',
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'px-4 py-2 bg-[#0d9488] hover:bg-teal-700 text-white font-bold rounded-lg mx-1 transition-colors',
                    denyButton: 'px-4 py-2 bg-red-500 hover:bg-red-600 text-white font-bold rounded-lg mx-1 transition-colors',
                    cancelButton: 'px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold rounded-lg mx-1 transition-colors',
                    popup: 'rounded-2xl shadow-xl border border-gray-100'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit Form untuk Menerima (Aktif)
                    submitVerification(bannerId, 'aktif');
                } else if (result.isDenied) {
                    // Submit Form untuk Menolak (Nonaktif / Ditolak)
                    submitVerification(bannerId, 'nonaktif');
                }
            });
        }

        function submitVerification(bannerId, actionStatus) {
            // Membuat form virtual untuk disubmit secara POST
            const form = document.createElement('form');
            form.method = 'POST';

            // Sesuaikan route URL ini dengan route controller verifikasi kamu
            form.action = `{{ route('admin.banner.verify-banner') }}`;

            // Tambahkan CSRF Token
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);

            // Tambahkan Status Keputusan (Aktif / Nonaktif)
            const statusInput = document.createElement('input');
            statusInput.type = 'hidden';
            statusInput.name = 'status';
            statusInput.value = actionStatus;
            form.appendChild(statusInput);

            // Tambahkan ID Banner
            const bannerIdInput = document.createElement('input');
            bannerIdInput.type = 'hidden';
            bannerIdInput.name = 'banner_id';
            bannerIdInput.value = bannerId;
            form.appendChild(bannerIdInput);

            document.body.appendChild(form);
            form.submit();
        }
    </script>
@endsection
