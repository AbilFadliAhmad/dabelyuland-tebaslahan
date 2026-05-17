@extends('layouts.admin')

@section('style')
    <link rel="stylesheet" href="{{ asset('assets/vendors/sweetalert2/sweetalert2.min.css') }}">
    <style>
        /* Meng-override gaya bawaan Simple-Datatables agar selaras dengan Tailwind */
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
                <h2 class="text-2xl font-bold text-gray-900 m-0">Daftar Testimoni</h2>
                <p class="text-sm text-gray-500 m-0 mt-1">Kelola ulasan dan pengalaman klien terhadap Dabelyuland.</p>
            </div>

            {{-- Tombol Tambah Testimoni Baru --}}
            <a href="{{ route('testimonis.create') }}"
                class="inline-flex items-center px-5 py-2.5 bg-[#0d9488] hover:bg-teal-700 text-white text-sm font-bold rounded-xl transition-all shadow-md shadow-teal-600/20 hover:-translate-y-0.5 no-underline">
                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Tambah Testimoni
            </a>
        </div>

        {{-- ==========================================================
         AREA TABEL DAFTAR TESTIMONI
         ========================================================== --}}
        <div class="bg-white rounded-2xl shadow-[0_2px_15px_rgba(0,0,0,0.03)] border border-gray-100 overflow-hidden">
            <div class="p-4 sm:p-6">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse" id="table1">

                        {{-- Header Tabel --}}
                        <thead>
                            <tr class="bg-gray-50/80">
                                <th
                                    class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider w-[5%] text-center hidden md:table-cell border-b-2 border-gray-100">
                                    No</th>
                                <th
                                    class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider w-[25%] border-b-2 border-gray-100">
                                    Profil Klien</th>
                                <th
                                    class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider w-[45%] border-b-2 border-gray-100">
                                    Isi Testimoni</th>
                                <th
                                    class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider w-[10%] text-center hidden lg:table-cell border-b-2 border-gray-100">
                                    Tanggal</th>
                                <th
                                    class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider w-[15%] text-center border-b-2 border-gray-100">
                                    Aksi</th>
                            </tr>
                        </thead>

                        {{-- Isi Tabel --}}
                        <tbody class="divide-y divide-gray-100">
                            @php $no = 1; @endphp

                            @if (isset($testimonis) && count($testimonis) > 0)
                                @foreach ($testimonis as $testimoni)
                                    <tr class="hover:bg-gray-50/50 transition-colors group align-middle">

                                        {{-- Kolom Nomor (Sembunyi di HP) --}}
                                        <td
                                            class="py-4 px-4 text-center text-sm font-bold text-gray-400 hidden md:table-cell">
                                            {{ $no++ }}
                                        </td>

                                        {{-- Kolom Profil Klien (Foto + Nama + Pekerjaan) --}}
                                        <td class="py-4 px-4">
                                            <div class="flex items-center gap-3">
                                                {{-- Foto Profil Bulat --}}
                                                @if ($testimoni->foto)
                                                    <div
                                                        class="w-12 h-12 rounded-full overflow-hidden shadow-sm border-2 border-white shrink-0">
                                                        <img src="{{ asset('storage/' . $testimoni->foto) }}"
                                                            alt="{{ $testimoni->nama }}" class="w-full h-full object-cover">
                                                    </div>
                                                @else
                                                    <div
                                                        class="w-12 h-12 bg-[#F0F7F7] rounded-full flex items-center justify-center text-[#0d9488] shrink-0 border-2 border-white shadow-sm">
                                                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                                                            stroke-width="1.5" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                                        </svg>
                                                    </div>
                                                @endif

                                                {{-- Teks Nama & Job --}}
                                                <div class="min-w-0">
                                                    <h6 class="text-sm font-bold text-gray-900 m-0 truncate">
                                                        {{ $testimoni->nama }}</h6>
                                                    <p class="text-xs text-gray-500 m-0 mt-0.5 truncate">
                                                        {{ $testimoni->pekerjaan }}</p>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Kolom Teks Testimoni (Line Clamp / Truncate) --}}
                                        <td class="py-4 px-4">
                                            <div
                                                class="relative bg-gray-50 rounded-xl p-3 border border-gray-100 group-hover:bg-white transition-colors">
                                                <svg class="absolute top-2 left-2 w-4 h-4 text-gray-300" fill="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path
                                                        d="M11.192 15.757c0-.88-.23-1.618-.69-2.217-.326-.412-.768-.683-1.327-.812-.55-.128-1.07-.137-1.54-.028-.16-.95.153-1.536.934-1.75.191-.054.402-.075.632-.061l.245.022.046-.242c.071-.382.128-.8.172-1.25.044-.452.066-.882.066-1.287 0-.583-.166-1.018-.5-1.303-.335-.285-.81-.428-1.428-.428-1.242 0-2.222.381-2.937 1.144-.716.762-1.074 1.837-1.074 3.226 0 .524.081 1.05.244 1.58.163.529.388 1.045.674 1.548.286.503.626.963 1.018 1.38.393.417.842.766 1.346 1.045.505.28 1.053.473 1.644.58.591.107 1.205.112 1.84.015.114-.017.202-.108.202-.224v-1.127c0-.12-.093-.217-.206-.217-.468.001-.904-.047-1.307-.144l-.066-.251zm10.231 0c0-.88-.23-1.618-.69-2.217-.326-.412-.768-.683-1.327-.812-.55-.128-1.07-.137-1.54-.028-.16-.95.153-1.536.934-1.75.191-.054.402-.075.632-.061l.245.022.046-.242c.071-.382.128-.8.172-1.25.044-.452.066-.882.066-1.287 0-.583-.166-1.018-.5-1.303-.335-.285-.81-.428-1.428-.428-1.242 0-2.222.381-2.937 1.144-.716.762-1.074 1.837-1.074 3.226 0 .524.081 1.05.244 1.58.163.529.388 1.045.674 1.548.286.503.626.963 1.018 1.38.393.417.842.766 1.346 1.045.505.28 1.053.473 1.644.58.591.107 1.205.112 1.84.015.114-.017.202-.108.202-.224v-1.127c0-.12-.093-.217-.206-.217-.468.001-.904-.047-1.307-.144l-.066-.251z" />
                                                </svg>
                                                <p class="text-sm text-gray-600 m-0 ml-6 line-clamp-2"
                                                    title="{{ $testimoni->testimoni }}">
                                                    "{{ $testimoni->testimoni }}"
                                                </p>
                                            </div>
                                        </td>

                                        {{-- Kolom Tanggal (Sembunyi di HP & Tablet kecil) --}}
                                        <td class="py-4 px-4 text-center hidden lg:table-cell">
                                            <span
                                                class="text-xs font-medium text-gray-500 bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-100">
                                                {{ date('d M Y', strtotime($testimoni->created_at)) }}
                                            </span>
                                        </td>

                                        {{-- Kolom Tombol Aksi (Edit & Hapus) --}}
                                        <td class="py-4 px-4 text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                <a href="{{ route('testimonis.edit', $testimoni) }}"
                                                    class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white transition-colors shadow-sm"
                                                    title="Edit Testimoni">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                                        stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                                    </svg>
                                                </a>

                                                <form action="{{ route('testimonis.destroy', $testimoni) }}" method="POST"
                                                    class="m-0 p-0"
                                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus testimoni ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-colors shadow-sm border-0 cursor-pointer"
                                                        title="Hapus Testimoni">
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
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                            </svg>
                                            <h6 class="text-lg font-bold text-gray-500 m-0 mb-1">Belum ada testimoni</h6>
                                            <p class="text-sm text-gray-400 m-0">Silakan klik tombol "Tambah Testimoni"
                                                untuk memulai.</p>
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

    {{-- Inisialisasi Simple-Datatables --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let table1 = document.querySelector('#table1');
            if (table1 && typeof simpleDatatables !== 'undefined') {
                new simpleDatatables.DataTable(table1, {
                    searchable: true,
                    fixedHeight: false,
                    perPage: 10,
                    labels: {
                        placeholder: "Cari testimoni...",
                        perPage: "{select} data per halaman",
                        noRows: "Testimoni tidak ditemukan",
                        info: "Menampilkan {start} - {end} dari {rows} data"
                    }
                });
            }
        });
    </script>
@endsection
