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
            <a href="{{ route('banner.create') }}"
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
                                                    <svg class="w-6 h-6 mb-1" fill="none" viewBox="0 0 24 24"
                                                        stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                                    </svg>
                                                    <span class="text-[10px] font-medium uppercase tracking-wider">Gambar
                                                        Hilang</span>
                                                </div>
                                            @endif
                                        </td>

                                        {{-- Kolom Status Aktif/Tidak Aktif --}}
                                        <td class="py-4 px-4 text-center">
                                            <label class="relative inline-flex items-center cursor-pointer">
                                                <input type="checkbox" class="sr-only peer toggle-status"
                                                    data-id="{{ $image->id }}" {{ $image->is_active ? 'checked' : '' }}>
                                                <div
                                                    class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#0d9488]">
                                                </div>
                                                <span
                                                    class="ml-3 text-sm font-bold min-w-[50px] {{ $image->is_active ? 'text-[#0d9488]' : 'text-gray-500' }} status-text-{{ $image->id }}">
                                                    {{ $image->is_active ? 'Aktif' : 'Mati' }}
                                                </span>
                                            </label>
                                        </td>

                                        {{-- Kolom Tombol Aksi (Edit & Hapus) --}}
                                        <td class="py-4 px-4 text-center">
                                            <div class="flex items-center justify-center gap-2">

                                                <a href="{{ route('banner.edit', $image->id) }}"
                                                    class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white transition-colors shadow-sm"
                                                    title="Edit Banner">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                                        stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                                    </svg>
                                                </a>

                                                <form action="{{ route('banner.destroy', $image->id) }}" method="POST"
                                                    class="m-0 p-0"
                                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus banner ini? Tindakan ini tidak dapat dibatalkan.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-colors shadow-sm border-0 cursor-pointer"
                                                        title="Hapus Banner">
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                                            stroke-width="2" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                        </svg>
                                                    </button>
                                                </form>

                                            </div>
                                        </td>

                                    </tr>
                                @endforeach
                            @else
                                {{-- State Jika Data Kosong --}}
                                <tr>
                                    <td colspan="4" class="py-16 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                            </svg>
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
    <script src="{{ asset('assets/js/main.js') }}"></script>
    <script src="{{ asset('assets/js/sweetalert-delete.js') }}"></script>

    {{-- Inisialisasi Simple-Datatables jika file plugin tersedia --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let table1 = document.querySelector('#table1');
            if (table1 && typeof simpleDatatables !== 'undefined') {
                new simpleDatatables.DataTable(table1, {
                    searchable: true,
                    fixedHeight: false,
                    perPage: 10,
                    labels: {
                        placeholder: "Cari banner...",
                        perPage: "{select} data per halaman",
                        noRows: "Banner tidak ditemukan",
                        info: "Menampilkan {start} - {end} dari {rows} banner"
                    }
                });
            }
        });
        document.querySelectorAll('.toggle-status').forEach(toggle => {
            toggle.addEventListener('change', function() {
                let bannerId = this.getAttribute('data-id');
                let isChecked = this.checked ? 1 : 0;
                let statusText = document.querySelector(`.status-text-${bannerId}`);

                // Ubah teks sementara saat proses
                statusText.innerText = "...";
                let url = "{{ route('admin.banner.toggle-status', ':id') }}";
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
    </script>
@endsection
