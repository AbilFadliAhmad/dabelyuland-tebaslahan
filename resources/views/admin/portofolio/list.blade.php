@extends('layouts.admin')

@section('content')
    <div class="w-full min-h-screen bg-gray-50/50 p-4 sm:p-6 lg:p-8 font-['Inter']">

        {{-- ==========================================================
         HEADER AREA & TOMBOL TAMBAH
         ========================================================== --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 m-0">Data Portofolio</h2>
                <p class="text-sm text-gray-500 m-0 mt-1">Kelola daftar hasil karya dan proyek properti Dabelyuland.</p>
            </div>

            <a href="{{ route('portofolios.create') }}"
                class="inline-flex items-center px-5 py-2.5 bg-[#0d9488] hover:bg-teal-700 text-white text-sm font-bold rounded-xl transition-all shadow-md shadow-teal-600/20 hover:-translate-y-0.5 no-underline">
                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Tambah Proyek Baru
            </a>
        </div>

        {{-- ==========================================================
         AREA TABEL
         ========================================================== --}}
        <div class="bg-white rounded-2xl shadow-[0_2px_15px_rgba(0,0,0,0.03)] border border-gray-100 overflow-hidden">
            <div class="p-4 sm:p-6">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse whitespace-nowrap md:whitespace-normal" id="table1">

                        {{-- Header Tabel --}}
                        <thead>
                            <tr class="bg-gray-50/80">
                                {{-- Kolom No disembunyikan di Mobile --}}
                                <th
                                    class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider w-[5%] text-center hidden md:table-cell border-b-2 border-gray-100">
                                    No</th>
                                <th
                                    class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider w-[40%] border-b-2 border-gray-100">
                                    Properti & Informasi</th>
                                {{-- Kolom Alamat disembunyikan di Tablet/Mobile --}}
                                <th
                                    class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider w-[25%] hidden lg:table-cell border-b-2 border-gray-100">
                                    Alamat Proyek</th>
                                <th
                                    class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider w-[15%] text-center border-b-2 border-gray-100">
                                    Tipe</th>
                                <th
                                    class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider w-[15%] text-center border-b-2 border-gray-100">
                                    Aksi</th>
                            </tr>
                        </thead>

                        {{-- Isi Tabel --}}
                        <tbody class="divide-y divide-gray-100">
                            @forelse($portofolios as $key => $item)
                                <tr class="hover:bg-gray-50/50 transition-colors group align-middle">

                                    {{-- Data No disembunyikan di Mobile --}}
                                    <td class="py-4 px-4 text-center text-sm font-bold text-gray-400 hidden md:table-cell">
                                        {{ $key + 1 }}
                                    </td>

                                    {{-- Kolom Info Utama (Foto + Judul + Pemilik) --}}
                                    <td class="py-4 px-4">
                                        <div class="flex items-center gap-3 md:gap-4">

                                            {{-- Foto Responsif (Kecil di HP, Lebih besar di Desktop) --}}
                                            @if ($item->gambar)
                                                <div
                                                    class="w-[80px] h-[60px] md:w-[120px] md:h-[80px] shrink-0 rounded-lg overflow-hidden shadow-sm border border-gray-200">
                                                    <img src="{{ asset('storage/' . $item->gambar) }}"
                                                        alt="Foto {{ $item->judul }}"
                                                        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                                                        loading="lazy">
                                                </div>
                                            @else
                                                <div
                                                    class="w-[80px] h-[60px] md:w-[120px] md:h-[80px] bg-gray-100 flex items-center justify-center text-gray-400 shrink-0 rounded-lg border border-gray-200 border-dashed">
                                                    <i class="bi bi-image text-2xl"></i>
                                                </div>
                                            @endif

                                            {{-- Teks Info --}}
                                            <div class="min-w-0 flex-1">
                                                <h6
                                                    class="mb-1 font-bold text-gray-900 truncate text-sm md:text-base max-w-[200px] md:max-w-[250px]">
                                                    {{ $item->judul }}
                                                </h6>

                                                <div class="flex items-center text-gray-500 mb-1 text-xs md:text-sm">
                                                    <i class="bi bi-person-badge mr-1.5 text-[#0d9488]"></i>
                                                    <span
                                                        class="truncate max-w-[150px]">{{ $item->pemilik ?? 'Tidak ada data pemilik' }}</span>
                                                </div>

                                                {{-- [MOBILE ONLY] Alamat pindah ke bawah judul jika layar kecil --}}
                                                <div class="block lg:hidden text-gray-500 mt-2 text-xs leading-snug line-clamp-2"
                                                    title="{{ $item->alamat }}">
                                                    <i class="bi bi-geo-alt mr-1 text-red-500"></i>
                                                    {{ $item->alamat ?? '-' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Kolom Alamat (Desktop Only) --}}
                                    <td class="hidden lg:table-cell py-4 px-4">
                                        <div class="text-gray-500 text-sm leading-relaxed line-clamp-2"
                                            title="{{ $item->alamat }}">
                                            <i class="bi bi-geo-alt mr-1 text-red-500"></i> {{ $item->alamat ?? '-' }}
                                        </div>
                                    </td>

                                    {{-- Kolom Tipe (Menggantikan Status) --}}
                                    <td class="text-center py-4 px-4">
                                        @if (strtolower($item->tipe) == 'design')
                                            <span
                                                class="inline-flex items-center justify-center bg-blue-50 text-blue-600 border border-blue-200 px-3 py-1.5 rounded-lg font-bold text-xs shadow-sm">
                                                <i class="bi bi-pen mr-1.5 hidden sm:inline-block"></i> Design
                                            </span>
                                        @elseif(strtolower($item->tipe) == 'build')
                                            <span
                                                class="inline-flex items-center justify-center bg-amber-50 text-amber-600 border border-amber-200 px-3 py-1.5 rounded-lg font-bold text-xs shadow-sm">
                                                <i class="bi bi-bricks mr-1.5 hidden sm:inline-block"></i> Build
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center justify-center bg-gray-100 text-gray-600 border border-gray-200 px-3 py-1.5 rounded-lg font-bold text-xs shadow-sm">
                                                {{ $item->tipe ?? '-' }}
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Kolom Aksi --}}
                                    <td class="text-center py-4 px-4">
                                        <div class="flex flex-row justify-center items-center gap-2">
                                            {{-- Tombol Edit --}}
                                            <a href="{{ route('portofolios.edit', $item->id) }}"
                                                class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white transition-colors shadow-sm"
                                                title="Edit Proyek">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                                </svg>
                                            </a>

                                            {{-- Tombol Hapus --}}
                                            <form action="{{ route('portofolios.destroy', $item->id) }}" method="POST"
                                                class="m-0 p-0"
                                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus portofolio ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-colors shadow-sm cursor-pointer border-0"
                                                    title="Hapus Proyek">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                                        stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                {{-- State Jika Data Kosong --}}
                                <tr>
                                    <td colspan="5" class="py-16 text-center">
                                        <div class="flex flex-col items-center justify-center text-gray-500">
                                            <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M3.75 9.776c.112-.017.227-.026.344-.026h15.812c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 0 0-1.883 2.542l.857 6a2.25 2.25 0 0 0 2.227 1.932H19.05a2.25 2.25 0 0 0 2.227-1.932l.857-6a2.25 2.25 0 0 0-1.883-2.542m-16.5 0V6A2.25 2.25 0 0 1 6 3.75h3.879a1.5 1.5 0 0 1 1.06.44l2.122 2.12a1.5 1.5 0 0 0 1.06.44H18A2.25 2.25 0 0 1 20.25 9v.776" />
                                            </svg>
                                            <h6 class="text-lg font-bold text-gray-500 m-0 mb-1">Belum ada portofolio</h6>
                                            <p class="text-sm text-gray-400 m-0">Klik tombol "Tambah Proyek Baru" untuk
                                                memasukkan data.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tableElement = document.querySelector("#table1");
            if (tableElement && typeof simpleDatatables !== 'undefined') {
                new simpleDatatables.DataTable(tableElement, {
                    searchable: true,
                    fixedHeight: false,
                    perPage: 10,
                    labels: {
                        placeholder: "Cari portofolio...",
                        perPage: "{select} data per halaman",
                        noRows: "Portofolio tidak ditemukan",
                        info: "Menampilkan {start} - {end} dari {rows} data"
                    }
                });
            }
        });
    </script>
@endsection
