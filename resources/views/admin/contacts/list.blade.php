@extends('layouts.admin')

@section('style')
    <link rel="stylesheet" href="{{ asset('assets/vendors/sweetalert2/sweetalert2.min.css') }}">
    <style>
        /* Kustomisasi gaya bawaan Simple-Datatables agar serasi dengan Tailwind */
        .dataTable-wrapper {
            font-family: 'Inter', sans-serif !important;
        }

        .dataTable-input {
            border: 1px solid #e5e7eb !important;
            border-radius: 0.75rem !important;
            padding: 0.6rem 1rem !important;
            outline: none !important;
            font-size: 0.875rem !important;
        }

        .dataTable-input:focus {
            border-color: #0d9488 !important;
            box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.1) !important;
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
            padding: 1rem 1.25rem !important;
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
                <h2 class="text-2xl font-bold text-gray-900 m-0">Kategori Kontak</h2>
                <p class="text-sm text-gray-500 m-0 mt-1">Kelola daftar kontak, karyawan, atau tim Dabelyuland.</p>
            </div>

            {{-- Tombol Tambah Kontak Baru --}}
            <a href="{{ route('contacts.create') }}"
                class="inline-flex items-center px-5 py-2.5 bg-[#0d9488] hover:bg-teal-700 text-white text-sm font-bold rounded-xl transition-all shadow-md shadow-teal-600/20 hover:-translate-y-0.5 no-underline">
                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z" />
                </svg>
                Tambah Kontak
            </a>
        </div>

        {{-- ==========================================================
         AREA TABEL DAFTAR KONTAK
         ========================================================== --}}
        <div class="bg-white rounded-3xl shadow-[0_4px_25px_rgba(0,0,0,0.03)] border border-gray-100 overflow-hidden">
            <div class="p-4 sm:p-6">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse whitespace-nowrap" id="table1">

                        {{-- Header Tabel --}}
                        <thead>
                            <tr class="bg-gray-50/80">
                                <th
                                    class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider w-[5%] text-center hidden md:table-cell border-b-2 border-gray-100">
                                    No</th>
                                <th
                                    class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider w-[35%] border-b-2 border-gray-100">
                                    Profil Kontak</th>
                                <th
                                    class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider w-[25%] border-b-2 border-gray-100">
                                    Email</th>
                                <th
                                    class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider w-[20%] border-b-2 border-gray-100">
                                    Nomor WhatsApp</th>
                                <th
                                    class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider w-[15%] text-center border-b-2 border-gray-100">
                                    Aksi</th>
                            </tr>
                        </thead>

                        {{-- Isi Tabel --}}
                        <tbody class="divide-y divide-gray-100">
                            @php $no = 1; @endphp

                            @if ($contacts->count() > 0)
                                @foreach ($contacts as $item)
                                    <tr class="hover:bg-gray-50/80 transition-colors group align-middle">

                                        {{-- Kolom Nomor --}}
                                        <td
                                            class="py-4 px-4 text-center text-sm font-bold text-gray-400 hidden md:table-cell">
                                            {{ $no++ }}
                                        </td>

                                        {{-- Kolom Profil Kontak (Foto, Nama, Jabatan) digabung agar rapi --}}
                                        <td class="py-4 px-4">
                                            <div class="flex items-center gap-4">
                                                {{-- Foto Profil --}}
                                                @if ($item->foto)
                                                    <div
                                                        class="w-12 h-12 rounded-full overflow-hidden shadow-sm border-2 border-white shrink-0">
                                                        <img src="{{ asset('storage/' . $item->foto) }}"
                                                            alt="Foto {{ $item->nama }}"
                                                            class="w-full h-full object-cover">
                                                    </div>
                                                @else
                                                    <div
                                                        class="w-12 h-12 bg-teal-50 rounded-full flex items-center justify-center text-[#0d9488] shrink-0 border-2 border-white shadow-sm">
                                                        <span
                                                            class="text-lg font-bold">{{ substr($item->nama, 0, 1) }}</span>
                                                    </div>
                                                @endif

                                                {{-- Nama & Jabatan --}}
                                                <div class="flex flex-col">
                                                    <span class="text-sm font-bold text-gray-900">{{ $item->nama }}</span>
                                                    <span
                                                        class="text-xs text-gray-500 mt-0.5">{{ $item->jabatan ?? 'Jabatan tidak diatur' }}</span>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Kolom Email --}}
                                        <td class="py-4 px-4">
                                            <div class="flex items-center text-gray-600 text-sm">
                                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="2" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                                                </svg>
                                                {{ $item->email }}
                                            </div>
                                        </td>

                                        {{-- Kolom Nomor WhatsApp --}}
                                        <td class="py-4 px-4">
                                            <div
                                                class="flex items-center text-sm font-semibold text-[#0d9488] bg-teal-50 px-3 py-1.5 rounded-lg w-max border border-teal-100">
                                                <i class="bi bi-whatsapp mr-2 text-green-500"></i>
                                                {{ $item->nowa }}
                                            </div>
                                        </td>

                                        {{-- Kolom Tombol Aksi Admin --}}
                                        <td class="py-4 px-4 text-center">
                                            <div class="flex items-center justify-center gap-2">

                                                <a href="{{ route('contacts.edit', $item->id) }}"
                                                    class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white transition-colors shadow-sm"
                                                    title="Edit Kontak">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                                        stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                                    </svg>
                                                </a>

                                                <form action="{{ route('contacts.destroy', $item->id) }}" method="POST"
                                                    class="m-0 p-0"
                                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus data kontak ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-colors shadow-sm border-0 cursor-pointer"
                                                        title="Hapus Kontak">
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
                                    <td colspan="5" class="py-16 text-center">
                                        <div class="flex flex-col items-center justify-center text-gray-500">
                                            <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                            </svg>
                                            <h6 class="text-lg font-bold text-gray-700 mb-1">Belum ada kontak</h6>
                                            <p class="text-sm m-0">Silakan klik tombol "Tambah Kontak" untuk mulai mendata
                                                tim Anda.</p>
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
    <script src="{{ asset('assets/vendors/sweetalert2/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('assets/js/sweetalert-delete.js') }}"></script>

    {{-- Inisialisasi Simple-Datatables --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tableElement = document.querySelector("#table1");
            if (tableElement && typeof simpleDatatables !== 'undefined') {
                new simpleDatatables.DataTable(tableElement, {
                    searchable: true,
                    fixedHeight: false,
                    perPage: 10,
                    labels: {
                        placeholder: "Cari kontak...",
                        perPage: "{select} data per halaman",
                        noRows: "Kontak tidak ditemukan",
                        info: "Menampilkan {start} - {end} dari {rows} data"
                    }
                });
            }
        });
    </script>
@endsection
