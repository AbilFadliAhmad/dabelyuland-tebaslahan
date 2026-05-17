@extends('layouts.admin')

@section('style')
<style>
    .table-audit th {
        font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 700; text-transform: uppercase;
        font-size: 0.75rem; letter-spacing: 0.05em; color: #64748b; background-color: #f8fafc;
        border-bottom: 2px solid #e2e8f0; padding: 1rem 1.5rem;
    }
    .table-audit td { padding: 1rem 1.5rem; vertical-align: middle; border-bottom: 1px solid #f1f5f9; }
    .table-audit tbody tr:hover { background-color: #f8fafc; }
</style>
@endsection

@section('content')
<div class="w-full min-h-screen p-4 sm:p-6 lg:p-8 font-['Inter'] bg-gray-50/50">

    {{-- HEADER --}}
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-8 gap-4">
        <div>
            <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900 m-0 font-['Plus_Jakarta_Sans'] tracking-tight">Audit Log Properti</h2>
            <p class="text-sm text-gray-500 m-0 mt-2">Pantau seluruh aktivitas penambahan, perubahan, dan penghapusan data oleh agen.</p>
        </div>
        <button class="px-5 py-2.5 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 font-bold rounded-xl shadow-sm transition-all text-sm flex items-center gap-2">
            <i class="fas fa-download"></i> Export PDF
        </button>
    </div>

    {{-- TABEL AUDIT --}}
    <div class="bg-white rounded-3xl shadow-[0_4px_25px_rgba(0,0,0,0.03)] border border-gray-100 overflow-hidden">
        
        {{-- Toolbar / Filter --}}
        <div class="p-5 border-b border-gray-100 flex flex-col sm:flex-row gap-4 bg-gray-50/30">
            <div class="relative w-full sm:w-80">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" id="searchLog" class="w-full pl-11 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-[#0d9488]" placeholder="Cari nama agen atau ID properti...">
            </div>
            <select class="w-full sm:w-48 px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-600 focus:outline-none focus:border-[#0d9488]">
                <option value="">Semua Aktivitas</option>
                <option value="create">Penambahan (Create)</option>
                <option value="update">Perubahan (Update)</option>
                <option value="delete">Penghapusan (Delete)</option>
            </select>
        </div>

        {{-- Tabel --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left table-audit">
                <thead>
                    <tr>
                        <th class="w-[15%]">Waktu</th>
                        <th class="w-[20%]">Pelaku</th>
                        <th class="w-[15%] text-center">Aktivitas</th>
                        <th class="w-[25%]">Target Properti</th>
                        <th class="w-[25%]">Detail Modifikasi</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Dummy Row 1: CREATE --}}
                    <tr>
                        <td>
                            <p class="text-sm font-bold text-gray-900 m-0">17 Apr 2026</p>
                            <p class="text-xs text-gray-500 m-0">14:30 WIB</p>
                        </td>
                        <td>
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-teal-100 text-[#0d9488] flex items-center justify-center text-xs font-bold">BS</div>
                                <span class="text-sm font-bold text-gray-900">Budi Santoso</span>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="inline-flex px-2.5 py-1 bg-green-50 text-green-600 text-[10px] font-extrabold uppercase rounded-lg border border-green-100">
                                Ditambahkan
                            </span>
                        </td>
                        <td>
                            <p class="text-sm font-bold text-gray-800 m-0">Rumah Minimalis Malang</p>
                            <p class="text-xs text-gray-500 m-0">ID: #PRP-8821</p>
                        </td>
                        <td><span class="text-xs text-gray-600 bg-gray-50 px-2 py-1 rounded">Membuat listing baru</span></td>
                    </tr>

                    {{-- Dummy Row 2: UPDATE --}}
                    <tr>
                        <td>
                            <p class="text-sm font-bold text-gray-900 m-0">17 Apr 2026</p>
                            <p class="text-xs text-gray-500 m-0">10:15 WIB</p>
                        </td>
                        <td>
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">SA</div>
                                <span class="text-sm font-bold text-gray-900">Siti Aminah</span>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="inline-flex px-2.5 py-1 bg-blue-50 text-blue-600 text-[10px] font-extrabold uppercase rounded-lg border border-blue-100">
                                Diubah
                            </span>
                        </td>
                        <td>
                            <p class="text-sm font-bold text-gray-800 m-0">Tanah Kavling Suhat</p>
                            <p class="text-xs text-gray-500 m-0">ID: #LND-1023</p>
                        </td>
                        <td><span class="text-xs text-gray-600 bg-gray-50 px-2 py-1 rounded">Harga: Rp400Jt &rarr; Rp380Jt</span></td>
                    </tr>

                    {{-- Dummy Row 3: DELETE --}}
                    <tr>
                        <td>
                            <p class="text-sm font-bold text-gray-900 m-0">16 Apr 2026</p>
                            <p class="text-xs text-gray-500 m-0">09:00 WIB</p>
                        </td>
                        <td>
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center text-xs font-bold">JA</div>
                                <span class="text-sm font-bold text-gray-900">Joko Anwar</span>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="inline-flex px-2.5 py-1 bg-rose-50 text-rose-600 text-[10px] font-extrabold uppercase rounded-lg border border-rose-100">
                                Dihapus
                            </span>
                        </td>
                        <td>
                            <p class="text-sm font-bold text-gray-800 m-0 line-through text-gray-400">Ruko 2 Lantai</p>
                            <p class="text-xs text-gray-500 m-0">ID: #PRP-0012</p>
                        </td>
                        <td><span class="text-xs text-rose-500 bg-rose-50 px-2 py-1 rounded">Menghapus listing permanen</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        <div class="p-4 border-t border-gray-100 bg-gray-50 flex justify-end">
            <span class="text-sm text-gray-500">Menampilkan 3 aktivitas terbaru</span>
        </div>
    </div>
</div>
@endsection