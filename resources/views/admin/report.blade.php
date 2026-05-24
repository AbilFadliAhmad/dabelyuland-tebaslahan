@extends('layouts.admin')

@section('style')
    {{-- Plugin untuk DatePicker (Flatpickr) --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        /* Custom Table Styling (Layar Normal) */
        .report-table th {
            background-color: #f8fafc;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            padding: 1rem;
            border-bottom: 2px solid #e2e8f0;
        }

        .report-table td {
            padding: 1rem;
            color: #334155;
            font-size: 0.875rem;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        .report-table tr:hover td {
            background-color: #f8fafc;
        }

        /* Elemen yang HANYA MUNCUL DI PDF disembunyikan di layar normal */
        .print-only {
            display: none !important;
        }

        /* ==========================================================
                                                                                                                                                                                       PENGATURAN CETAK PDF NATIVE (AJAIB)
                                                                                                                                                                                       ========================================================== */
        @media print {

            /* 1. Sembunyikan seluruh body bawaan */
            body * {
                visibility: hidden;
            }

            /* 2. Tampilkan HANYA area laporan */
            #print-section,
            #print-section * {
                visibility: visible;
            }

            /* 3. Tarik area laporan ke pojok kiri atas halaman PDF */
            #print-section {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }

            /* 4. SEMBUNYIKAN: Tombol, Form Filter, dan GRAFIK CANVAS */
            .no-print,
            canvas,
            .chart-container {
                display: none !important;
            }

            /* 5. TAMPILKAN: Tabel Data Pengganti Grafik */
            .print-only {
                display: block !important;
            }

            /* 6. Hilangkan shadow & border tebal agar PDF lebih bersih (Kertas Putih) */
            .bg-white {
                box-shadow: none !important;
                border: 1px solid #f1f5f9 !important;
            }

            /* 7. Paksa browser mencetak warna background & text Tailwind */
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            /* 8. Cegah elemen terpotong setengah di antara 2 halaman PDF */
            .break-inside-avoid {
                page-break-inside: avoid;
            }
        }
    </style>
@endsection

@section('content')
    @php
        function formatRupiah($number)
        {
            $formatter = new NumberFormatter('id_ID', NumberFormatter::CURRENCY);
            $formatter->setAttribute(NumberFormatter::MIN_FRACTION_DIGITS, 0);

            return $formatter->format($number);
        }
    @endphp
    <div id="main" class="min-h-screen bg-gray-50/50 p-4 sm:p-6 lg:p-8 font-['Inter']">

        {{-- BUNGKUSAN UTAMA UNTUK AREA PDF --}}
        <div id="print-section">

            {{-- HEADER & FILTER AREA --}}
            <div
                class="bg-white rounded-3xl shadow-[0_4px_25px_rgba(0,0,0,0.03)] border border-gray-100 p-6 mb-8 break-inside-avoid">
                <div class="flex flex-col lg:flex-row justify-between lg:items-center gap-6">

                    <div>
                        <h2 class="text-2xl font-extrabold text-gray-900 m-0 font-['Plus_Jakarta_Sans'] tracking-tight">
                            Laporan & Analitik</h2>
                        <p class="text-sm text-gray-500 m-0 mt-1">Pantau performa website, traffic, dan perkembangan leads
                            properti secara keseluruhan.</p>
                    </div>

                    {{-- Area Filter & Tombol (Diberi class 'no-print' agar hilang saat dicetak PDF) --}}
                    <div class="flex flex-col sm:flex-row items-center gap-4 no-print">
                        <form action="{{ route('report') }}" method="GET"
                            class="flex flex-wrap sm:flex-nowrap items-center gap-3 w-full sm:w-auto" id="filterForm">
                            <select name="periode" id="periode"
                                class="w-full sm:w-auto px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold text-gray-700 focus:ring-2 focus:ring-[#0d9488]/20 focus:border-[#0d9488] transition-all cursor-pointer">
                                <option value="today">Hari Ini</option>
                                <option value="this_week">Minggu Ini</option>
                                <option value="this_month">Bulan Ini</option>
                                <option value="this_year">Tahun Ini</option>
                                <option value="custom">Custom Date Range</option>
                            </select>

                            <input type="text" name="custom_date" id="custom_date"
                                class="hidden px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm w-full sm:w-64"
                                placeholder="Pilih Rentang Tanggal...">
                            <button type="submit" class="hidden" id="btnFilterSubmit">Terapkan</button>
                        </form>

                        <div class="w-px h-8 bg-gray-200 hidden sm:block"></div>

                        <button type="button" id="btnExportPDF" onclick="window.print()"
                            class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-2.5 bg-rose-50 hover:bg-rose-500 text-rose-600 hover:text-white text-sm font-bold rounded-xl transition-colors border border-rose-100 shadow-sm">
                            <i class="bi bi-printer mr-2"></i>PDF
                        </button>
                    </div>
                </div>
            </div>

            {{-- ISI KONTEN LAPORAN --}}
            <div class="pb-10 space-y-8">

                {{-- BAGIAN 1: PENGUNJUNG & FUNNEL KONVERSI --}}
                <div class="break-inside-avoid">
                    {{-- Cards Ringkasan --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
                            <div
                                class="w-12 h-12 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-xl">
                                <i class="fas fa-globe"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">Pengunjung Website
                                </p>
                                <h3 class="text-2xl font-black text-gray-800 m-0">{{ number_format($webViews ?? 0) }}
                                </h3>
                            </div>
                        </div>
                        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
                            <div
                                class="w-12 h-12 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl">
                                <i class="fas fa-home"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">Pengunjung Properti
                                </p>
                                <h3 class="text-2xl font-black text-gray-800 m-0">{{ number_format($propViews ?? 0) }}
                                </h3>
                            </div>
                        </div>
                        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
                            <div
                                class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                                <i class="fab fa-whatsapp"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">Klik WhatsApp</p>
                                <h3 class="text-2xl font-black text-gray-800 m-0">{{ number_format($waClicks ?? 0) }}
                                </h3>
                            </div>
                        </div>
                    </div>

                    {{-- Grafik Area --}}
                    <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
                        <h5 class="text-lg font-bold text-gray-800 mb-1">Tren Funnel Pengunjung</h5>
                        <p class="text-xs text-gray-500 mb-6">Perbandingan kunjungan platform dengan interaksi langsung
                            calon pembeli (WA).</p>

                        {{-- Wadah Grafik ApexCharts (Tampil di Web) --}}
                        <div class="chart-container relative w-full h-[320px]">
                            <div id="chartPengunjung"></div>
                        </div>

                        {{-- Tabel Fallback (Hanya Tampil di PDF) --}}
                        <div class="print-only mt-4">
                            <table class="w-full text-left border border-gray-200">
                                <thead>
                                    <tr class="bg-gray-50 text-gray-700 text-sm border-b border-gray-200">
                                        <th class="p-3">Periode</th>
                                        <th class="p-3 text-blue-600">Pengunjung Website</th>
                                        <th class="p-3 text-indigo-600">Pengunjung Properti</th>
                                        <th class="p-3 text-emerald-600">Klik WhatsApp</th>
                                    </tr>
                                </thead>
                                <tbody class="text-sm">
                                    {{-- Gunakan data dinamis dari backend di sini. Ini contoh statis sesuai permintaanmu --}}
                                    <tr class="border-b">
                                        <td class="p-3 font-bold">Senin</td>
                                        <td class="p-3 text-blue-600 font-bold">120</td>
                                        <td class="p-3 text-indigo-600 font-bold">85</td>
                                        <td class="p-3 text-emerald-600 font-bold">25</td>
                                    </tr>
                                    <tr class="border-b">
                                        <td class="p-3 font-bold">Selasa</td>
                                        <td class="p-3 text-blue-600 font-bold">132</td>
                                        <td class="p-3 text-indigo-600 font-bold">90</td>
                                        <td class="p-3 text-emerald-600 font-bold">30</td>
                                    </tr>
                                    <tr class="border-b">
                                        <td class="p-3 font-bold">Rabu</td>
                                        <td class="p-3 text-blue-600 font-bold">101</td>
                                        <td class="p-3 text-indigo-600 font-bold">75</td>
                                        <td class="p-3 text-emerald-600 font-bold">15</td>
                                    </tr>
                                    <tr class="border-b">
                                        <td class="p-3 font-bold">Kamis</td>
                                        <td class="p-3 text-blue-600 font-bold">134</td>
                                        <td class="p-3 text-indigo-600 font-bold">95</td>
                                        <td class="p-3 text-emerald-600 font-bold">35</td>
                                    </tr>
                                    <tr class="border-b">
                                        <td class="p-3 font-bold">Jumat</td>
                                        <td class="p-3 text-blue-600 font-bold">90</td>
                                        <td class="p-3 text-indigo-600 font-bold">60</td>
                                        <td class="p-3 text-emerald-600 font-bold">10</td>
                                    </tr>
                                    <tr class="border-b">
                                        <td class="p-3 font-bold">Sabtu</td>
                                        <td class="p-3 text-blue-600 font-bold">230</td>
                                        <td class="p-3 text-indigo-600 font-bold">180</td>
                                        <td class="p-3 text-emerald-600 font-bold">70</td>
                                    </tr>
                                    <tr>
                                        <td class="p-3 font-bold">Minggu</td>
                                        <td class="p-3 text-blue-600 font-bold">210</td>
                                        <td class="p-3 text-indigo-600 font-bold">160</td>
                                        <td class="p-3 text-emerald-600 font-bold">65</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- BAGIAN 2: TRAFFIC --}}
                <div class="break-inside-avoid">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <div
                            class="lg:col-span-1 bg-white p-6 rounded-3xl border border-gray-100 shadow-sm chart-container">
                            <h5 class="text-lg font-bold text-gray-800 mb-4">Distribusi Traffic</h5>
                            <div class="relative w-full h-[300px] flex justify-center items-center">
                                <div id="chartTraffic" class="w-full"></div>
                            </div>
                        </div>

                        {{-- Tabel ini Tampil di Web & PDF --}}
                        <div
                            class="lg:col-span-2 bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden w-full">
                            <div class="p-6 border-b border-gray-100">
                                <h5 class="text-lg font-bold text-gray-800 m-0">Detail Sumber Pengunjung</h5>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left report-table">
                                    <thead>
                                        <tr>
                                            <th>Sumber</th>
                                            <th>Total Kunjungan</th>
                                            <th>Persentase</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div class="flex items-center text-green-500 font-semibold">
                                                    <i class="fab fa-whatsapp mr-2 text-lg"></i> WhatsApp
                                                </div>
                                            </td>
                                            <td class="font-bold">{{ number_format($trafficCounts['wa'] ?? 0) }}</td>
                                            <td>{{ $trafficSources['wa'] ?? 0 }}%</td>
                                            <td><span
                                                    class="px-2 py-1 bg-green-100 text-green-700 rounded-md text-xs font-bold">Utama</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="flex items-center text-pink-500 font-semibold">
                                                    <i class="fab fa-instagram mr-2 text-lg"></i> Instagram
                                                </div>
                                            </td>
                                            <td class="font-bold">{{ number_format($trafficCounts['ig'] ?? 0) }}</td>
                                            <td>{{ $trafficSources['ig'] ?? 0 }}%</td>
                                            <td>-</td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="flex items-center text-gray-800 font-semibold">
                                                    <i class="fab fa-twitter mr-2 text-lg"></i> Twitter / X
                                                </div>
                                            </td>
                                            <td class="font-bold">{{ number_format($trafficCounts['twitter'] ?? 0) }}
                                            </td>
                                            <td>{{ $trafficSources['twitter'] ?? 0 }}%</td>
                                            <td>-</td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="flex items-center text-blue-600 font-semibold">
                                                    <i class="fab fa-facebook mr-2 text-lg"></i> Facebook
                                                </div>
                                            </td>
                                            <td class="font-bold">{{ number_format($trafficCounts['fb'] ?? 0) }}</td>
                                            <td>{{ $trafficSources['fb'] ?? 0 }}%</td>
                                            <td>-</td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="flex items-center text-gray-500 font-semibold">
                                                    <i class="fas fa-link mr-2 text-lg"></i> Others (Direct/SEO)
                                                </div>
                                            </td>
                                            <td class="font-bold">{{ number_format($trafficCounts['other'] ?? 0) }}
                                            </td>
                                            <td>{{ $trafficSources['other'] ?? 0 }}%</td>
                                            <td>-</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SECTION: PENDAPATAN PLATFORM (REVENUE) --}}
                <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 mb-8 break-inside-avoid">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h5 class="text-xl font-bold text-gray-800 m-0">Pendapatan Platform</h5>
                            <p class="text-sm text-gray-500 mt-1">Rincian pendapatan dari layanan Membership dan Top-Up
                                Koin.</p>
                        </div>
                        <div
                            class="px-4 py-2 bg-gray-50 text-gray-600 rounded-full text-xs font-bold border border-gray-100 no-print">
                            <i class="fas fa-chart-bar mr-1"></i> Grafik Bar
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                        {{-- Bagian KIRI: Visualisasi Grafik (3 Kolom) --}}
                        <div class="lg:col-span-3">
                            <div id="chartRevenue" class="w-full h-[320px]"></div>

                            {{-- Tabel Fallback PDF --}}
                            <div class="print-only mt-4">
                                <table class="w-full text-left border border-gray-200 text-sm">
                                    <thead>
                                        <tr class="bg-gray-50 border-b">
                                            <th class="p-3">Periode</th>
                                            <th class="p-3 text-blue-600">Membership</th>
                                            <th class="p-3 text-amber-500">Top-Up Koin</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="border-b">
                                            <td class="p-3 font-bold">Januari</td>
                                            <td class="p-3 font-bold text-blue-600">Rp 1.500.000</td>
                                            <td class="p-3 font-bold text-amber-500">Rp 500.000</td>
                                        </tr>
                                        <tr class="border-b">
                                            <td class="p-3 font-bold">Februari</td>
                                            <td class="p-3 font-bold text-blue-600">Rp 2.000.000</td>
                                            <td class="p-3 font-bold text-amber-500">Rp 800.000</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Bagian KANAN: Ringkasan (1 Kolom) --}}
                        <div class="lg:col-span-1 flex flex-col gap-4">
                            {{-- Membership Card --}}
                            <div
                                class="p-5 rounded-2xl bg-blue-50/50 border border-blue-100 transition-transform hover:-translate-y-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <div
                                        class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center text-sm">
                                        <i class="fas fa-id-card"></i>
                                    </div>
                                    <span
                                        class="text-[10px] font-extrabold text-blue-800 uppercase tracking-wider">Membership</span>
                                </div>
                                <h4 class="text-lg font-black text-gray-800 m-0">
                                    {{ formatRupiah($revMembership ?? 0) }}</h4>
                            </div>

                            {{-- Koin Card --}}
                            <div
                                class="p-5 rounded-2xl bg-amber-50/50 border border-amber-100 transition-transform hover:-translate-y-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <div
                                        class="w-8 h-8 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center text-sm">
                                        <i class="fas fa-coins"></i>
                                    </div>
                                    <span class="text-[10px] font-extrabold text-amber-800 uppercase tracking-wider">Top-Up
                                        Koin</span>
                                </div>
                                <h4 class="text-lg font-black text-gray-800 m-0">{{ formatRupiah($revKoin ?? 0) }}
                                </h4>
                            </div>

                            {{-- Total Card (Dark) --}}
                            <div
                                class="p-5 rounded-2xl bg-[#0f172a] border border-slate-700 text-white relative overflow-hidden transition-transform hover:-translate-y-1 shadow-lg shadow-slate-900/20">
                                <div class="absolute -right-6 -top-6 w-24 h-24 bg-teal-500/20 blur-xl rounded-full"></div>
                                <div class="relative z-10">
                                    <div class="flex items-center gap-3 mb-2">
                                        <div
                                            class="w-8 h-8 rounded-lg bg-slate-800 border border-slate-700 text-teal-400 flex items-center justify-center text-sm">
                                            <i class="fas fa-wallet"></i>
                                        </div>
                                        <span
                                            class="text-[10px] font-extrabold text-slate-300 uppercase tracking-wider">Total</span>
                                    </div>
                                    <h4 class="text-xl font-black text-white m-0 tracking-tight">
                                        {{ formatRupiah($revTotal ?? 0) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SECTION: PERFORMA KOMISI BERSIH --}}
                <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-8 mb-8 break-inside-avoid">

                    {{-- Header Sederhana (Tanpa Card) --}}
                    <div class="mb-8">
                        <h4 class="text-xl font-bold text-gray-900 m-0">Performa Komisi Bersih</h4>
                        <p class="text-sm text-gray-500 mt-1">Analisis tren pendapatan bersih dari penjualan properti.</p>
                    </div>

                    {{-- Baris Ringkasan Angka (Clean Stats Row) --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10 border-b border-gray-100 pb-8">
                        <div class="relative">
                            <p class="text-[10px] font-bold text-teal-600 uppercase tracking-widest mb-1">Komisi Bersih</p>
                            <h4 class="text-3xl font-black text-teal-700 tracking-tight">
                                {{ formatRupiah($komisiBersih ?? 0) }}</h4>
                        </div>
                    </div>

                    {{-- Area Grafik (Di bawah Statistik) --}}
                    <div class="w-full">
                        <div id="chartKomisi" class="w-full h-[320px]"></div>
                    </div>
                </div>


                {{-- BAGIAN 5: DATA USER --}}
                <div class="break-inside-avoid">
                    <div class="grid grid-cols-1 md:grid-cols-2  gap-6 mb-6">
                        <div class="bg-[#0d9488] text-white p-6 rounded-2xl shadow-sm">
                            <p class="text-teal-100 text-sm font-bold uppercase mb-1">Total User Baru</p>
                            <h3 class="text-3xl font-black m-0 text-white">{{ number_format($totalUsers ?? 0) }}</h3>
                        </div>
                        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                            <p class="text-gray-500 text-sm font-bold uppercase mb-1">Total Properti Baru</p>
                            <h3 class="text-3xl font-black text-gray-800 m-0">{{ number_format($totalProperties ?? 0) }}
                            </h3>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
                        <h5 class="text-lg font-bold text-gray-800 mb-4">Pertumbuhan Properti dan User baru</h5>
                        <div class="chart-container relative w-full h-[300px]">
                            <div id="chartUser"></div>
                        </div>
                        {{-- Tabel Data (Hanya PDF) --}}
                        <div class="print-only mt-4">
                            <table class="w-full text-left border border-gray-200">
                                <thead>
                                    <tr class="bg-teal-50 text-teal-800 text-sm border-b border-teal-200">
                                        <th class="p-3">Bulan</th>
                                        <th class="p-3">Total Pendaftar Baru</th>
                                    </tr>
                                </thead>
                                <tbody class="text-sm">
                                    <tr class="border-b">
                                        <td class="p-3 font-bold">Januari</td>
                                        <td class="p-3 text-[#0d9488] font-bold">10 Orang</td>
                                    </tr>
                                    <tr class="border-b">
                                        <td class="p-3 font-bold">Februari</td>
                                        <td class="p-3 text-[#0d9488] font-bold">25 Orang</td>
                                    </tr>
                                    <tr class="border-b">
                                        <td class="p-3 font-bold">Maret</td>
                                        <td class="p-3 text-[#0d9488] font-bold">15 Orang</td>
                                    </tr>
                                    <tr class="border-b">
                                        <td class="p-3 font-bold">April</td>
                                        <td class="p-3 text-[#0d9488] font-bold">40 Orang</td>
                                    </tr>
                                    <tr class="border-b">
                                        <td class="p-3 font-bold">Mei</td>
                                        <td class="p-3 text-[#0d9488] font-bold">35 Orang</td>
                                    </tr>
                                    <tr>
                                        <td class="p-3 font-bold">Juni</td>
                                        <td class="p-3 text-[#0d9488] font-bold">60 Orang</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    {{-- Library CHART.JS dan Datepicker --}}
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/id.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.css">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // ==========================================
            // FILTER TANGGAL (FLATPICKR)
            // ==========================================
            const selectPeriode = document.getElementById('periode');
            const inputCustomDate = document.getElementById('custom_date');
            const urlParams = new URLSearchParams(window.location.search);


            if (inputCustomDate) {
                const fp = flatpickr(inputCustomDate, {
                    mode: "range",
                    dateFormat: "Y-m-d",
                    locale: "id",
                    onChange: function(selectedDates) {
                        if (selectedDates.length === 2) document.getElementById('filterForm').submit();
                    }
                });

                if (selectPeriode) {
                    selectPeriode.value = urlParams.get('periode') || 'this_week';
                    selectPeriode.addEventListener('change', function() {
                        if (this.value === 'custom') {
                            inputCustomDate.classList.remove('hidden');
                            fp.open();
                        } else {
                            inputCustomDate.classList.add('hidden');
                            document.getElementById('filterForm').submit();
                        }
                    });
                }

                if (selectPeriode.value === 'custom' ) {
                    inputCustomDate.classList.remove('hidden');
                    const value = urlParams.get('custom_date') || '';
                    value && fp.setDate(value.split(' - '));
                }
            }

            // ==========================================
            // SUNTIKAN DATA DINAMIS DARI CONTROLLER
            // ==========================================
            const chartLabels = @json($chartLabels);

            // Data Area Chart Pengunjung
            const dataWeb = @json($chartDataWeb);
            const dataProperty = @json($chartDataProp);
            const dataWa = @json($chartDataWa);

            // Data Polar Traffic
            const dataTraffic = @json($chartTrafficSource);

            // Data Bar Chart Pendapatan
            const dataMembership = @json($chartDataMembership);
            const dataKoin = @json($chartDataKoin);

            // Data Komisi & User (Line)
            const dataKomisi = @json($chartDataKomisi);
            const dataPendaftar = @json($chartDataUsers);
            const dataPropBaru = @json($chartDataProperties);

            // ==========================================
            // APEXCHARTS CONFIGURATION
            // ==========================================


            // ------------------------------------------
            // 1. GRAFIK PENGUNJUNG (AREA CHART)
            // ------------------------------------------
            const labelPengunjung = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

            const pengunjungOptions = {
                series: [{
                        name: 'Pengunjung Website',
                        data: dataWeb
                    },
                    {
                        name: 'Pengunjung Properti',
                        data: dataProperty
                    },
                    {
                        name: 'Klik WhatsApp',
                        data: dataWa
                    }
                ],
                chart: {
                    type: 'area',
                    height: 320,
                    fontFamily: 'Inter, sans-serif',
                    toolbar: {
                        show: false
                    }
                },
                colors: ['#3b82f6', '#6366f1', '#10b981'],
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: [2, 2, 3]
                },
                xaxis: {
                    categories: chartLabels,
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    },
                    labels: {
                        style: {
                            colors: '#64748b'
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: '#64748b'
                        }
                    }
                },
                grid: {
                    borderColor: '#f1f5f9',
                    strokeDashArray: 4,
                    padding: {
                        top: 0,
                        right: 0,
                        bottom: 0,
                        left: 10
                    }
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'right'
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.3,
                        opacityTo: 0.05,
                        stops: [0, 90, 100]
                    }
                },
                tooltip: {
                    theme: 'light',
                    y: {
                        formatter: function(val) {
                            return val + " Interaksi"
                        }
                    }
                }
            };

            if (document.querySelector("#chartPengunjung")) {
                new ApexCharts(document.querySelector("#chartPengunjung"), pengunjungOptions).render();
            }


            // ------------------------------------------
            // 2. GRAFIK TRAFFIC (POLAR AREA CHART)
            // ------------------------------------------
            const labelTraffic = ['WhatsApp', 'Instagram', 'Twitter / X', 'Facebook', 'Others'];

            const trafficOptions = {
                series: dataTraffic,
                labels: labelTraffic,
                chart: {
                    type: 'polarArea',
                    height: 320,
                    fontFamily: 'Inter, sans-serif'
                },
                stroke: {
                    colors: ['#fff'],
                    width: 2
                },
                fill: {
                    opacity: 1
                },
                colors: ['#22c55e', '#ec4899', '#1f2937', '#3b82f6', '#9ca3af'],
                yaxis: {
                    show: false
                },
                legend: {
                    position: 'bottom',
                    horizontalAlign: 'center',
                    markers: {
                        radius: 12
                    }
                },
                plotOptions: {
                    polarArea: {
                        rings: {
                            strokeWidth: 4,
                            strokeColor: '#f1f5f9'
                        },
                        spokes: {
                            strokeWidth: 3,
                            connectorColors: '#f1f5f9'
                        }
                    }
                },
                theme: {
                    monochrome: {
                        enabled: false
                    }
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return val + " %"
                        }
                    }
                }
            };

            if (document.querySelector("#chartTraffic")) {
                new ApexCharts(document.querySelector("#chartTraffic"), trafficOptions).render();
            }


            // ------------------------------------------
            // 3. GRAFIK PENDAPATAN PLATFORM (BAR CHART)
            // ------------------------------------------
            const revOptions = {
                series: [{
                        name: 'Membership',
                        data: dataMembership
                    },
                    {
                        name: 'Top-Up Koin',
                        data: dataKoin
                    }
                ],
                chart: {
                    type: 'bar',
                    height: 400,
                    fontFamily: 'Inter, sans-serif',
                    toolbar: {
                        show: false
                    }
                },
                colors: ['#3b82f6', '#f59e0b'],
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        borderRadius: 4,
                        borderRadiusApplication: 'end'
                    },
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    show: true,
                    width: 3,
                    colors: ['transparent']
                },
                xaxis: {
                    categories: chartLabels,
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    },
                    labels: {
                        style: {
                            colors: '#64748b'
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: '#64748b'
                        },
                        formatter: (value) => {
                            if (value >= 1000000) return "Rp " + (value / 1000000) + "Jt";
                            return "Rp " + (value / 1000) + "Rb";
                        }
                    }
                },
                grid: {
                    borderColor: '#f1f5f9',
                    strokeDashArray: 4,
                    padding: {
                        left: 10,
                        right: 0,
                        top: 0,
                        bottom: 0
                    }
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'left',
                    fontWeight: 700
                },
                fill: {
                    opacity: 1
                },
                tooltip: {
                    theme: 'light',
                    y: {
                        formatter: function(val) {
                            return "Rp " + val.toLocaleString('id-ID');
                        }
                    }
                }
            };

            if (document.querySelector("#chartRevenue")) {
                new ApexCharts(document.querySelector("#chartRevenue"), revOptions).render();
            }


            // ------------------------------------------
            // 4. GRAFIK KOMISI BERSIH (AREA CHART)
            // ------------------------------------------
            const labelKomisi = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'];

            const komisiOptions = {
                series: [{
                    name: 'Komisi',
                    data: dataKomisi
                }],
                chart: {
                    type: 'area',
                    height: 320,
                    fontFamily: 'Inter, sans-serif',
                    toolbar: {
                        show: false
                    }
                },
                colors: ['#0d9488'],
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.6,
                        opacityTo: 0.1,
                        stops: [0, 90, 100]
                    }
                },
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                dataLabels: {
                    enabled: false
                },
                xaxis: {
                    categories: chartLabels,
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    },
                    labels: {
                        style: {
                            colors: '#64748b'
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: '#64748b'
                        },
                        formatter: (val) => "Rp " + (val / 1000000) + "Jt"
                    }
                },
                grid: {
                    borderColor: '#f1f5f9',
                    strokeDashArray: 4
                },
                tooltip: {
                    theme: 'light',
                    y: {
                        formatter: (val) => "Rp " + val.toLocaleString('id-ID')
                    }
                }
            };

            if (document.querySelector("#chartKomisi")) {
                new ApexCharts(document.querySelector("#chartKomisi"), komisiOptions).render();
            }


            // ------------------------------------------
            // 5. GRAFIK PENDAFTAR & PROPERTI BARU (LINE CHART)
            // ------------------------------------------
            const labelUser = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'];

            const userOptions = {
                series: [{
                        name: 'Pendaftar Baru',
                        data: dataPendaftar
                    },
                    {
                        name: 'Properti Baru',
                        data: dataPropBaru
                    }
                ],
                chart: {
                    type: 'line',
                    height: 300,
                    fontFamily: 'Inter, sans-serif',
                    toolbar: {
                        show: false
                    }
                },
                colors: ['#0d9488', '#000000'],
                stroke: {
                    curve: 'straight',
                    width: [3, 3]
                },
                markers: {
                    size: 5,
                    colors: ['#0d9488', '#000000'],
                    strokeColors: '#fff',
                    strokeWidth: 2,
                    hover: {
                        size: 7
                    }
                },
                dataLabels: {
                    enabled: false
                },
                xaxis: {
                    categories: chartLabels,
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    },
                    labels: {
                        style: {
                            colors: '#64748b'
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: '#64748b'
                        }
                    }
                },
                grid: {
                    borderColor: '#f1f5f9',
                    strokeDashArray: 4
                },
                legend: {
                    position: 'bottom',
                    markers: {
                        radius: 12
                    }
                }
            };

            if (document.querySelector("#chartUser")) {
                new ApexCharts(document.querySelector("#chartUser"), userOptions).render();
            }

        });
    </script>
@endsection
