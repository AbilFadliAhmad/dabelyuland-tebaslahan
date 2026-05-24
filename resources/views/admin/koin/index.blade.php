@extends('layouts.admin')

@section('style')
    <style>
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(226, 232, 240, 0.8);
        }

        /* Preview Badge untuk Tema */
        .theme-pill-gold {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fcd34d;
        }

        .theme-pill-dark {
            background: #1f2937;
            color: #f9fafb;
            border: 1px solid #374151;
        }

        .theme-pill-popular {
            background: #f0fdfa;
            color: #0f766e;
            border: 1px solid #5eead4;
        }

        .theme-pill-basic {
            background: #f8fafc;
            color: #475569;
            border: 1px solid #e2e8f0;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 10px;
        }

        /* Transisi Tab Fade In */
        .tab-content {
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
@endsection

@section('content')
    <div class="p-4 sm:p-6 lg:p-8 font-['Inter'] bg-[#f8fafc] min-h-screen">
        <div class="max-w-[95rem] mx-auto">

            {{-- HEADER UTAMA --}}
            <div class="mb-8">
                <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900 m-0 font-['Plus_Jakarta_Sans'] tracking-tight">
                    Pusat Kendali Koin
                </h2>
                <p class="text-sm text-gray-500 mt-1">Atur paket top-up koin dan tarif layanan promosi properti secara
                    dinamis.</p>
            </div>

            {{-- SUB-MENU / TAB NAVIGATION --}}
            <div
                class="flex space-x-2 bg-gray-200/50 p-1.5 rounded-2xl w-max mb-8 border border-gray-200 overflow-x-auto max-w-full">
                <button onclick="switchTab('paket')" id="btn-tab-paket"
                    class="px-6 py-3 rounded-xl font-bold text-sm transition-all bg-white text-[#0d9488] shadow-sm flex items-center gap-2">
                    <i class="fas fa-box-open"></i> Paket Top Up Koin
                </button>
                <button onclick="switchTab('tarif')" id="btn-tab-promo"
                    class="px-6 py-3 rounded-xl font-bold text-sm transition-all text-gray-500 hover:text-gray-700 flex items-center gap-2 hover:bg-gray-100">
                    <i class="fas fa-tags"></i> Tarif Layanan
                </button>
            </div>

            {{-- =========================================================================================
             SECTION 1: MANAJEMEN PAKET TOP UP KOIN (LOGIKA ASLI)
             ========================================================================================= --}}
            <div id="section-paket" class="tab-content hidden">

                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-6">
                    <h3 class="text-xl font-extrabold text-gray-800 m-0 font-['Plus_Jakarta_Sans']">Daftar Paket Penjualan
                    </h3>
                    <button onclick="toggleModal('modalPackage')"
                        class="w-full md:w-auto flex items-center justify-center gap-2 px-6 py-3 bg-[#0d9488] hover:bg-[#0f766e] text-white font-bold text-sm rounded-xl shadow-lg shadow-teal-900/10 transition-all active:scale-95">
                        <i class="fas fa-plus-circle"></i> Tambah Paket
                    </button>
                </div>

                {{-- STATISTIK --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4 md:gap-6 mb-8">
                    <div
                        class="bg-white border border-gray-100 p-4 md:p-6 rounded-[2rem] shadow-sm flex items-center gap-4">
                        <div
                            class="w-12 h-12 bg-teal-50 text-[#0d9488] rounded-2xl flex items-center justify-center text-xl shrink-0">
                            <i class="fas fa-box"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest m-0">Total Paket</p>
                            <h4 class="text-2xl font-black text-gray-900 m-0">
                                {{ isset($packages) ? $packages->count() : 0 }}</h4>
                        </div>
                    </div>
                    <div
                        class="bg-white border border-gray-100 p-4 md:p-6 rounded-[2rem] shadow-sm flex items-center gap-4">
                        <div
                            class="w-12 h-12 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center text-xl shrink-0">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest m-0">Paket Aktif</p>
                            <h4 class="text-2xl font-black text-gray-900 m-0">
                                {{ isset($packages) ? $packages->where('is_active', true)->count() : 0 }}</h4>
                        </div>
                    </div>
                </div>

                {{-- DATA TABLE PAKET KOIN --}}
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden mb-10">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50/50">
                                    <th
                                        class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100">
                                        Visual</th>
                                    <th
                                        class="px-6 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100">
                                        Jumlah Koin</th>
                                    <th
                                        class="px-6 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100">
                                        Harga</th>
                                    <th
                                        class="px-6 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100">
                                        Tema & Status</th>
                                    <th
                                        class="px-6 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 text-right">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @if (isset($packages) && $packages->count() > 0)
                                    @foreach ($packages as $pkg)
                                        <tr class="group hover:bg-gray-50/50 transition-colors">
                                            <td class="px-8 py-4">
                                                <div
                                                    class="w-14 h-14 bg-gray-50 rounded-2xl p-2 border border-gray-100 group-hover:scale-110 transition-transform">
                                                    <img src="/assets/images/icon/koin.svg"
                                                        onerror="this.outerHTML='<i class=\'fas fa-coins text-3xl text-amber-400\'></i>'"
                                                        class="w-full h-full object-contain">
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="text-lg font-black text-gray-900">{{ $pkg->koin }}</span>
                                                <span class="text-xs text-gray-400 font-bold ml-1 uppercase">Koin</span>
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="font-extrabold text-[#0d9488]">Rp
                                                    {{ number_format($pkg->harga, 0, ',', '.') }}</span>
                                                <p class="text-[10px] text-gray-400 m-0">{{ $pkg->saving ?? '-' }}</p>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="flex items-center gap-2 mb-1">
                                                    <span
                                                        class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-wider theme-pill-{{ $pkg->theme }}">{{ $pkg->theme }}</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <div class="flex items-center justify-end gap-2">
                                                    <button onclick='openPackageModal({{ $pkg }})'
                                                        class="w-9 h-9 rounded-xl bg-blue-50 text-blue-500 hover:bg-blue-500 hover:text-white flex items-center justify-center transition-all shadow-sm">
                                                        <i class="fas fa-edit text-xs"></i>
                                                    </button>
                                                    <form action="{{ route('admin.koin.destroy', $pkg->id) }}"
                                                        method="POST" onsubmit="return confirm('Hapus paket ini?')">
                                                        @csrf @method('DELETE')
                                                        <button
                                                            class="w-9 h-9 rounded-xl bg-rose-50 text-rose-500 hover:bg-rose-500 hover:text-white flex items-center justify-center transition-all shadow-sm">
                                                            <i class="fas fa-trash-alt text-xs"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="5" class="px-8 py-20 text-center">
                                            <p class="text-xs text-gray-400">Belum ada paket koin.</p>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- =========================================================================================
             SECTION 2: MANAJEMEN BIAYA PROMO (DINAMIS CRUD FULL)
             ========================================================================================= --}}
            <div id="section-promo" class="tab-content hidden">

                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-6">
                    <div>
                        <h3 class="text-xl font-extrabold text-gray-800 m-0 font-['Plus_Jakarta_Sans']">Aturan Tarif Promo
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">Buat variasi durasi promosi (harian, mingguan, bulanan) dengan
                            harga koin yang Anda tentukan.</p>
                    </div>
                    <button onclick="openTarifModal()"
                        class="w-full md:w-auto flex items-center justify-center gap-2 px-6 py-3 bg-amber-500 hover:bg-amber-600 text-white font-bold text-sm rounded-xl shadow-lg shadow-amber-500/20 transition-all active:scale-95">
                        <i class="fas fa-plus-circle"></i> Tambah Tarif
                    </button>
                </div>

                {{-- Data table Tarif --}}
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden mb-10">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50/50">
                                    <th
                                        class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100">
                                        Layanan</th>
                                    <th
                                        class="px-6 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100">
                                        Nama Durasi</th>
                                    <th
                                        class="px-6 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100">
                                        Total Hari</th>
                                    <th
                                        class="px-6 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100">
                                        Biaya Koin</th>
                                    <th
                                        class="px-6 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 text-right">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">

                                {{-- LOOPING DARI DATABASE MENGGUNAKAN $servicePrices --}}
                                @if (isset($servicePrices) && $servicePrices->count() > 0)
                                    @foreach ($servicePrices as $service)
                                        <tr class="group hover:bg-gray-50/50 transition-colors">
                                            <td class="px-8 py-5">
                                                <div class="flex items-center gap-3">
                                                    @if ($service->jenis_layanan == 'highlight')
                                                        <div
                                                            class="w-10 h-10 rounded-xl bg-amber-50 text-amber-500 flex items-center justify-center">
                                                            <i class="fas fa-star"></i>
                                                        </div>
                                                        <span class="font-bold text-gray-900">Highlight</span>
                                                    @elseif ($service->jenis_layanan == 'rekomendasi')
                                                        <div
                                                            class="w-10 h-10 rounded-xl bg-blue-50 text-blue-500 flex items-center justify-center">
                                                            <i class="fas fa-thumbs-up"></i>
                                                        </div>
                                                        <span class="font-bold text-gray-900">Rekomendasi</span>
                                                    @elseif ($service->jenis_layanan == 'banner')
                                                        <div
                                                            class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-500 flex items-center justify-center">
                                                            <i class="fas fa-image"></i>
                                                        </div>
                                                        <span class="font-bold text-gray-900">Banner</span>
                                                    @elseif ($service->jenis_layanan == 'sundul')
                                                        <div
                                                            class="w-10 h-10 rounded-xl bg-teal-50 text-teal-500 flex items-center justify-center">
                                                            <i class="fas fa-rocket"></i>
                                                        </div>
                                                        <span class="font-bold text-gray-900">Sundul</span>
                                                    @endif
                                                </div>
                                            </td>

                                            {{-- Logika khusus untuk Sundul: Nama & Hari --}}
                                            <td class="px-6 py-5 font-bold text-gray-800">
                                                {{ $service->jenis_layanan == 'sundul' ? '-' : $service->nama_durasi }}
                                            </td>
                                            <td class="px-6 py-5 text-sm text-gray-500 font-medium">
                                                {{ $service->jenis_layanan == 'sundul' ? 'Instan' : $service->jumlah_hari . ' Hari' }}
                                            </td>

                                            <td class="px-6 py-5">
                                                {{-- Warna Badge menyesuaikan tema layanan --}}
                                                @php
                                                    $badgeColor = [
                                                        'highlight' => 'bg-amber-100 text-amber-700',
                                                        'rekomendasi' => 'bg-blue-100 text-blue-700',
                                                        'banner' => 'bg-indigo-100 text-indigo-700',
                                                        'sundul' => 'bg-teal-100 text-teal-700',
                                                    ][$service->jenis_layanan];
                                                @endphp
                                                <span
                                                    class="px-3 py-1 {{ $badgeColor }} rounded-lg text-sm font-black flex items-center gap-1.5 w-max">
                                                    <i class="fas fa-coins text-[10px]"></i> {{ $service->biaya_koin }}
                                                </span>
                                            </td>

                                            <td class="px-6 py-5 text-right">
                                                <div class="flex items-center justify-end gap-2">
                                                    <button onclick='openTarifModal({!! json_encode($service) !!})'
                                                        class="w-9 h-9 rounded-xl bg-blue-50 text-blue-500 hover:bg-blue-500 hover:text-white flex items-center justify-center transition-all shadow-sm">
                                                        <i class="fas fa-edit text-xs"></i>
                                                    </button>

                                                    <form
                                                        action="{{ route('admin.service.price.destroy', $service->id) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus tarif ini?')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit"
                                                            class="w-9 h-9 rounded-xl bg-rose-50 text-rose-500 hover:bg-rose-500 hover:text-white flex items-center justify-center transition-all shadow-sm">
                                                            <i class="fas fa-trash-alt text-xs"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="5" class="px-8 py-20 text-center">
                                            <p class="text-xs text-gray-400">Belum ada aturan tarif layanan yang dibuat.
                                            </p>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- MODAL PAKET KOIN (ADD & EDIT) --}}
    <div id="modalPackage"
        class="fixed inset-0 z-[999] hidden items-center justify-center bg-gray-900/60 backdrop-blur-sm p-4 transition-all">
        <div
            class="bg-white w-full max-w-2xl rounded-[2.5rem] shadow-2xl flex flex-col transform transition-all duration-300 scale-95 opacity-0 modal-content">
            <div class="p-8 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <h3 id="packageModalTitle" class="font-black text-gray-900 text-xl tracking-tight">Tambah Paket Koin</h3>
                <button type="button" onclick="toggleModal('modalPackage')"
                    class="w-10 h-10 flex items-center justify-center rounded-full bg-white shadow-sm text-gray-400 hover:text-rose-500 transition-all"><i
                        class="fas fa-times"></i></button>
            </div>

            <form id="packageForm" action="{{ route('admin.koin.store') }}" method="POST" class="p-8 space-y-6">
                @csrf
                <div id="packageMethod"></div> {{-- Tempat Method PUT --}}

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Jumlah
                            Koin</label>
                        <input type="number" name="koin" id="pkg_koin" required
                            class="w-full px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl font-bold outline-none focus:border-[#0d9488]">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Harga
                            (IDR)</label>
                        <input type="number" name="harga" id="pkg_harga" required
                            class="w-full px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl font-bold outline-none focus:border-[#0d9488]">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Tema
                            Visual</label>
                        <select name="theme" id="pkg_theme"
                            class="w-full px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl font-bold">
                            <option value="basic">Basic (Putih)</option>
                            <option value="popular">Popular (Teal)</option>
                            <option value="dark">Premium (Hitam)</option>
                            <option value="gold">Ultimate (Emas)</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Badge
                            Promo</label>
                        <input type="text" name="badge" id="pkg_badge"
                            class="w-full px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Info
                            Hemat</label>
                        <input type="text" name="saving" id="pkg_saving"
                            class="w-full px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl">
                    </div>
                    <div class="space-y-2">
                        <label
                            class="text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Deskripsi</label>
                        <input type="text" name="desc" id="pkg_desc"
                            class="w-full px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl">
                    </div>
                </div>

                <div class="flex items-center gap-6 p-4 bg-gray-50 rounded-2xl border border-gray-100">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_best" id="pkg_is_best" value="1"
                            class="w-5 h-5 rounded-md text-[#0d9488]">
                        <span class="text-xs font-bold text-gray-700">Set Unggulan</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" id="pkg_is_active" value="1" checked
                            class="w-5 h-5 rounded-md text-[#0d9488]">
                        <span class="text-xs font-bold text-gray-700">Aktifkan</span>
                    </label>
                </div>

                <div class="pt-4 flex gap-3">
                    <button type="button" onclick="toggleModal('modalPackage')"
                        class="flex-1 py-4 bg-gray-100 text-gray-600 rounded-2xl font-black">Batal</button>
                    <button type="submit" id="packageSubmitBtn"
                        class="flex-[2] py-4 bg-[#0d9488] text-white rounded-2xl font-black shadow-lg shadow-teal-900/20">Simpan
                        Paket</button>
                </div>
            </form>
        </div>
    </div>


    {{-- MODAL PROMO (ADD & EDIT) --}}
    <div id="modalTarif"
        class="fixed inset-0 z-[999] hidden items-center justify-center bg-gray-900/60 backdrop-blur-sm p-4 transition-all">
        <div
            class="bg-white w-full max-w-lg rounded-[2.5rem] shadow-2xl flex flex-col transform transition-all duration-300 scale-95 opacity-0 modal-content">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50 rounded-t-[2.5rem]">
                <h3 id="promoModalTitle" class="font-black text-gray-900 text-lg tracking-tight">Tambah Tarif</h3>
                <button type="button" onclick="toggleModal('modalTarif')"
                    class="w-8 h-8 flex items-center justify-center rounded-full bg-white shadow-sm text-gray-400 hover:text-rose-500 transition-all">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="promoForm" action="{{ route('admin.service.price.store') }}" method="POST"
                class="p-6 space-y-5">
                @csrf
                <div id="promoMethod"></div>

                <div class="space-y-2">
                    <label class="text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Jenis
                        Layanan</label>
                    {{-- Tambahkan onchange untuk mendeteksi pilihan layanan --}}
                    <select name="jenis_layanan" id="promo_jenis" required onchange="handlePromoTypeChange(this.value)"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all outline-none font-bold text-gray-900">
                        <option value="highlight">Highlight</option>
                        <option value="rekomendasi">Rekomendasi</option>
                        <option value="banner">Banner</option>
                        <option value="sundul">Token Sundul</option>
                    </select>
                </div>

                {{-- Container Durasi yang bisa disembunyikan --}}
                <div id="durationFields" class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Nama
                            Durasi</label>
                        <input type="text" name="nama_durasi" id="promo_nama" placeholder="Misal: 3 Minggu" required
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all outline-none font-bold text-gray-900">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Waktu
                            (Hari)</label>
                        <input type="number" name="jumlah_hari" id="promo_hari" placeholder="Misal: 21" required
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all outline-none font-bold text-gray-900">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Biaya Koin</label>
                    <div class="relative">
                        <i class="fas fa-coins absolute left-4 top-1/2 -translate-y-1/2 text-amber-500"></i>
                        <input type="number" name="biaya_koin" id="promo_koin" placeholder="Contoh: 150" required
                            class="w-full pl-12 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all outline-none font-bold text-gray-900">
                    </div>
                </div>

                <div class="pt-2 flex gap-3">
                    <button type="button" onclick="toggleModal('modalTarif')"
                        class="flex-1 py-3 bg-gray-100 text-gray-600 rounded-xl font-black text-sm">Batal</button>
                    <button type="submit"
                        class="flex-[2] py-3 bg-amber-500 text-white rounded-xl font-black text-sm shadow-lg shadow-amber-500/20">
                        Simpan Tarif
                    </button>
                </div>
            </form>
        </div>
    </div>


@endsection
@section('script')
    <script>
        // ==========================================
        // LOGIKA TAB NAVIGATION
        // ==========================================
        function switchTab(tabName) {
            // Menyimpan tab yang terakhir dibuka ke session storage
            localStorage.setItem('activeKoinTab', tabName);

            // Hide all sections
            document.getElementById('section-paket').classList.add('hidden');
            document.getElementById('section-paket').classList.remove('block');
            document.getElementById('section-promo').classList.add('hidden');
            document.getElementById('section-promo').classList.remove('block');

            // Reset Button Styles
            const btnPaket = document.getElementById('btn-tab-paket');
            const btnPromo = document.getElementById('btn-tab-promo');

            btnPaket.className =
                "px-6 py-3 rounded-xl font-bold text-sm transition-all text-gray-500 hover:text-gray-700 flex items-center gap-2 hover:bg-gray-100";
            btnPromo.className =
                "px-6 py-3 rounded-xl font-bold text-sm transition-all text-gray-500 hover:text-gray-700 flex items-center gap-2 hover:bg-gray-100";

            // Show Active Section & Style Active Button
            if (tabName === 'paket') {
                document.getElementById('section-paket').classList.remove('hidden');
                document.getElementById('section-paket').classList.add('block');
                btnPaket.className =
                    "px-6 py-3 rounded-xl font-bold text-sm transition-all bg-white text-[#0d9488] shadow-sm flex items-center gap-2";
            } else if (tabName === 'tarif') {
                document.getElementById('section-promo').classList.remove('hidden');
                document.getElementById('section-promo').classList.add('block');
                btnPromo.className =
                    "px-6 py-3 rounded-xl font-bold text-sm transition-all bg-white text-amber-500 shadow-sm flex items-center gap-2";
            }
        }

        // Memulihkan tab yang terbuka setelah refresh halaman (misal setelah submit form)
        document.addEventListener("DOMContentLoaded", function() {
            let activeTab = localStorage?.getItem('activeKoinTab') ?? 'paket';
            switchTab(activeTab);
        });

        // ==========================================
        // PENYATUAN LOGIKA MODAL PAKET KOIN
        // ==========================================
        function openPackageModal(data = null) {
            const form = document.getElementById('packageForm');
            const title = document.getElementById('packageModalTitle');
            const submitBtn = document.getElementById('packageSubmitBtn');
            const methodArea = document.getElementById('packageMethod');

            if (data) {
                // --- MODE EDIT ---
                title.innerText = 'Edit Paket Koin';
                submitBtn.innerText = 'Update Paket';

                // Update URL action ke route.update
                let updateUrl = "{{ route('admin.koin.update', ':id') }}";
                form.action = updateUrl.replace(':id', data.id);

                // Tambahkan spoofing method PUT untuk Laravel
                methodArea.innerHTML = '<input type="hidden" name="_method" value="PUT">';

                // Isi Value
                document.getElementById('pkg_koin').value = data.koin;
                document.getElementById('pkg_harga').value = data.harga;
                document.getElementById('pkg_theme').value = data.theme;
                document.getElementById('pkg_badge').value = data.badge || '';
                document.getElementById('pkg_saving').value = data.saving || '';
                document.getElementById('pkg_desc').value = data.desc || '';
                document.getElementById('pkg_is_best').checked = data.is_best == 1;
                document.getElementById('pkg_is_active').checked = data.is_active == 1;
            } else {
                // --- MODE TAMBAH ---
                title.innerText = 'Tambah Paket Koin';
                submitBtn.innerText = 'Simpan Paket';
                form.action = "{{ route('admin.koin.store') }}";
                methodArea.innerHTML = ''; // Hapus method PUT
                form.reset(); // Kosongkan semua input
            }

            toggleModal('modalPackage');
        }

        // ==========================================
        // PENYATUAN LOGIKA MODAL TARIF PROMO
        // ==========================================
        function openTarifModal(data = null) {
            const form = document.getElementById('promoForm');
            const title = document.getElementById('promoModalTitle');
            const methodArea = document.getElementById('promoMethod');

            if (data) {
                title.innerText = 'Edit Tarif Promo';
                let updateUrl = "{{ route('admin.service.price.update', ':id') }}"; // Pastikan route update ada
                form.action = updateUrl.replace(':id', data.id);
                methodArea.innerHTML = '<input type="hidden" name="_method" value="PATCH">';

                document.getElementById('promo_jenis').value = data.jenis_layanan;
                document.getElementById('promo_nama').value = data.nama_durasi;
                document.getElementById('promo_hari').value = data.jumlah_hari;
                document.getElementById('promo_koin').value = data.biaya_koin;

                handlePromoTypeChange(data.jenis_layanan);
            } else {
                title.innerText = 'Tambah Tarif Promo';
                form.action = "{{ route('admin.service.price.store') }}";
                methodArea.innerHTML = '';
                form.reset();

                handlePromoTypeChange('highlight');
            }

            toggleModal('modalTarif');
        }

        function handlePromoTypeChange(value) {
            const durationFields = document.getElementById('durationFields');
            const namaInput = document.getElementById('promo_nama');
            const hariInput = document.getElementById('promo_hari');

            if (value === 'sundul') {
                // Sembunyikan field durasi
                durationFields.classList.add('hidden');
                // Kosongkan value dan non-aktifkan required agar tidak error saat submit
                namaInput.value = '-';
                hariInput.value = 0;
                namaInput.required = false;
                hariInput.required = false;
            } else {
                // Tampilkan kembali
                durationFields.classList.remove('hidden');
                namaInput.required = true;
                hariInput.required = true;
                // Jika sebelumnya disembunyikan (masih berisi default '-'), kosongkan lagi
                if (namaInput.value === '-') namaInput.value = '';
                if (hariInput.value === '0') hariInput.value = '';
            }
        }

        // Logika UI Pintu (Toggle) Tetap Satu Untuk Semua
        function toggleModal(id) {
            const modal = document.getElementById(id);
            const content = modal.querySelector('.modal-content');

            if (modal.classList.contains('hidden')) {
                modal.classList.replace('hidden', 'flex');
                setTimeout(() => content.classList.replace('scale-95', 'scale-100'), 10);
                setTimeout(() => content.classList.replace('opacity-0', 'opacity-100'), 10);
            } else {
                content.classList.replace('scale-100', 'scale-95');
                content.classList.replace('opacity-100', 'opacity-0');
                setTimeout(() => modal.classList.replace('flex', 'hidden'), 300);
            }
        }
    </script>
@endsection
