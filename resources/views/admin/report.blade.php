@extends('layouts.admin')

@section('style')
    {{-- Plugin untuk Grafik (ApexCharts) dan DatePicker (Flatpickr) --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        /* Transisi Tab & Tabel */
        .tab-content {
            display: none;
            animation: fadeIn 0.4s ease;
        }

        .tab-content.active {
            display: block;
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

        /* Custom Table Styling */
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

        /* Scrollbar Tersembunyi untuk Tab Mobile */
        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
@endsection

@section('content')
    <div id="main" class="min-h-screen bg-gray-50/50 p-4 sm:p-6 lg:p-8 font-['Inter']">

        {{-- ==========================================================
         HEADER & FILTER AREA
         ========================================================== --}}
        <div class="bg-white rounded-3xl shadow-[0_4px_25px_rgba(0,0,0,0.03)] border border-gray-100 p-6 mb-6">
            <div class="flex flex-col lg:flex-row justify-between lg:items-center gap-6">

                {{-- Judul Halaman --}}
                <div>
                    <h2 class="text-2xl font-extrabold text-gray-900 m-0 font-['Plus_Jakarta_Sans'] tracking-tight">Laporan &
                        Analitik</h2>
                    <p class="text-sm text-gray-500 m-0 mt-1">Pantau performa website, traffic, dan perkembangan leads
                        properti.</p>
                </div>

                {{-- Filter & Action Buttons --}}
                <div class="flex flex-col sm:flex-row items-center gap-3">

                    {{-- Form Filter Periode Waktu --}}
                    <form action="{{ route('report') }}" method="GET"
                        class="flex flex-wrap sm:flex-nowrap items-center gap-3 w-full sm:w-auto" id="filterForm">
                        <select name="periode" id="periode"
                            class="w-full sm:w-auto px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold text-gray-700 focus:ring-2 focus:ring-[#0d9488]/20 focus:border-[#0d9488] transition-all cursor-pointer">
                            <option value="today">Hari Ini</option>
                            <option value="this_week">Minggu Ini</option>
                            <option value="this_month" selected>Bulan Ini</option>
                            <option value="this_year">Tahun Ini</option>
                            <option value="custom">Custom Date Range</option>
                        </select>

                        {{-- Input Custom Date (Tersembunyi secara default, muncul jika pilih 'custom') --}}
                        <input type="text" name="custom_date" id="custom_date"
                            class="hidden px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm w-full sm:w-64"
                            placeholder="Pilih Rentang Tanggal...">

                        <button type="submit" class="hidden" id="btnFilterSubmit">Terapkan</button>
                    </form>

                    <div class="w-px h-8 bg-gray-200 hidden sm:block"></div>

                    {{-- Tombol Export --}}
                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <button type="button"
                            class="flex-1 sm:flex-none inline-flex justify-center items-center px-4 py-2.5 bg-emerald-50 hover:bg-emerald-500 text-emerald-600 hover:text-white text-sm font-bold rounded-xl transition-colors border border-emerald-100">
                            <i class="bi bi-file-earmark-excel mr-2"></i> Excel
                        </button>
                        <button type="button"
                            class="flex-1 sm:flex-none inline-flex justify-center items-center px-4 py-2.5 bg-rose-50 hover:bg-rose-500 text-rose-600 hover:text-white text-sm font-bold rounded-xl transition-colors border border-rose-100">
                            <i class="bi bi-file-earmark-pdf mr-2"></i> PDF
                        </button>
                    </div>
                </div>

            </div>
        </div>

        {{-- ==========================================================
         TAB NAVIGATION (STRUKTUR MENU REPORT)
         ========================================================== --}}
        <div class="mb-6 border-b border-gray-200 overflow-x-auto hide-scrollbar">
            <ul class="flex whitespace-nowrap gap-2 pb-2" id="reportTabs">
                <li><button
                        class="tab-btn active px-4 py-2.5 rounded-xl text-sm font-bold transition-all bg-[#0d9488] text-white shadow-md shadow-teal-500/20"
                        data-target="tab-pengunjung">Pengunjung Web</button></li>
                <li><button
                        class="tab-btn px-4 py-2.5 rounded-xl text-sm font-bold text-gray-500 hover:bg-gray-100 transition-all"
                        data-target="tab-traffic">Sumber Traffic</button></li>
                <li><button
                        class="tab-btn px-4 py-2.5 rounded-xl text-sm font-bold text-gray-500 hover:bg-gray-100 transition-all"
                        data-target="tab-leads">Leads / Inquiry</button></li>
                <li><button
                        class="tab-btn px-4 py-2.5 rounded-xl text-sm font-bold text-gray-500 hover:bg-gray-100 transition-all"
                        data-target="tab-properti">Performa Properti</button></li>
                <li><button
                        class="tab-btn px-4 py-2.5 rounded-xl text-sm font-bold text-gray-500 hover:bg-gray-100 transition-all"
                        data-target="tab-user">Data User</button></li>
                <li><button
                        class="tab-btn px-4 py-2.5 rounded-xl text-sm font-bold text-gray-500 hover:bg-gray-100 transition-all"
                        data-target="tab-wa">WhatsApp Leads</button></li>
            </ul>
        </div>

        {{-- ==========================================================
         ISI KONTEN LAPORAN (TAB CONTENTS)
         ========================================================== --}}

        {{-- TAB 1: PENGUNJUNG WEBSITE --}}
        <div id="tab-pengunjung" class="tab-content active">
            {{-- Summary Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-xl"><i
                            class="bi bi-people-fill"></i></div>
                    <div>
                        <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">Total Pengunjung</p>
                        <h3 class="text-2xl font-black text-gray-800 m-0">12,450</h3>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
                    <div
                        class="w-12 h-12 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl">
                        <i class="bi bi-person-check-fill"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">Unique Visitor</p>
                        <h3 class="text-2xl font-black text-gray-800 m-0">8,230</h3>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
                    <div
                        class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                        <i class="bi bi-eye-fill"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">Page Views</p>
                        <h3 class="text-2xl font-black text-gray-800 m-0">45,120</h3>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center text-xl"><i
                            class="bi bi-graph-down-arrow"></i></div>
                    <div>
                        <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">Bounce Rate</p>
                        <h3 class="text-2xl font-black text-gray-800 m-0">42.5%</h3>
                    </div>
                </div>
            </div>

            {{-- Grafik Area --}}
            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm mb-6">
                <h5 class="text-lg font-bold text-gray-800 mb-4">Grafik Tren Pengunjung</h5>
                <div id="chartPengunjung" class="w-full h-[300px]"></div>
            </div>
        </div>

        {{-- TAB 2: SUMBER TRAFFIC --}}
        <div id="tab-traffic" class="tab-content">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Grafik Donut --}}
                <div class="lg:col-span-1 bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
                    <h5 class="text-lg font-bold text-gray-800 mb-4">Distribusi Traffic</h5>
                    <div id="chartTraffic" class="w-full flex justify-center h-[300px]"></div>
                </div>

                {{-- Tabel Data Detail --}}
                <div class="lg:col-span-2 bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
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
                                        <div class="flex items-center"><i class="bi bi-google text-red-500 mr-2"></i> Google
                                            Search</div>
                                    </td>
                                    <td class="font-bold">5,200</td>
                                    <td>45%</td>
                                    <td><span
                                            class="px-2 py-1 bg-green-100 text-green-700 rounded-md text-xs font-bold">Teratas</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="flex items-center"><i class="bi bi-instagram text-pink-500 mr-2"></i>
                                            Instagram</div>
                                    </td>
                                    <td class="font-bold">3,100</td>
                                    <td>25%</td>
                                    <td>-</td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="flex items-center"><i class="bi bi-facebook text-blue-600 mr-2"></i>
                                            Facebook</div>
                                    </td>
                                    <td class="font-bold">1,800</td>
                                    <td>15%</td>
                                    <td>-</td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="flex items-center"><i class="bi bi-link-45deg text-gray-500 mr-2"></i>
                                            Direct Access</div>
                                    </td>
                                    <td class="font-bold">1,200</td>
                                    <td>10%</td>
                                    <td>-</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- TAB 3: LEADS / INQUIRY --}}
        <div id="tab-leads" class="tab-content">
            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm mb-6">
                <h5 class="text-lg font-bold text-gray-800 mb-4">Grafik Pertumbuhan Leads Masuk</h5>
                <div id="chartLeads" class="w-full h-[350px]"></div>
            </div>
        </div>

        {{-- TAB 4: PERFORMA PROPERTI --}}
        <div id="tab-properti" class="tab-content">
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <h5 class="text-lg font-bold text-gray-800 m-0">Ranking Properti Terpopuler</h5>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left report-table">
                        <thead>
                            <tr>
                                <th>Peringkat</th>
                                <th>Nama Properti</th>
                                <th>Total Dilihat</th>
                                <th>Klik WhatsApp</th>
                                <th>Konversi (%)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span
                                        class="w-8 h-8 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center font-bold">1</span>
                                </td>
                                <td class="font-bold text-[#0d9488]">Rumah Minimalis Modern Sidoarjo</td>
                                <td>4,500 <span class="text-xs text-gray-400">views</span></td>
                                <td>320 <span class="text-xs text-gray-400">klik</span></td>
                                <td><span class="text-green-600 font-bold">7.1%</span></td>
                            </tr>
                            <tr>
                                <td><span
                                        class="w-8 h-8 rounded-full bg-gray-100 text-gray-600 flex items-center justify-center font-bold">2</span>
                                </td>
                                <td class="font-bold text-[#0d9488]">Kavling Siap Bangun Menganti</td>
                                <td>3,200 <span class="text-xs text-gray-400">views</span></td>
                                <td>150 <span class="text-xs text-gray-400">klik</span></td>
                                <td><span class="text-green-600 font-bold">4.6%</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- TAB 5: DATA USER --}}
        <div id="tab-user" class="tab-content">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-[#0d9488] text-white p-6 rounded-2xl shadow-sm">
                    <p class="text-teal-100 text-sm font-bold uppercase mb-1">Total Pendaftar Baru</p>
                    <h3 class="text-3xl font-black m-0">1,250</h3>
                </div>
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                    <p class="text-gray-500 text-sm font-bold uppercase mb-1">User Terverifikasi</p>
                    <h3 class="text-3xl font-black text-gray-800 m-0">980</h3>
                </div>
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                    <p class="text-gray-500 text-sm font-bold uppercase mb-1">User Aktif (Bulan Ini)</p>
                    <h3 class="text-3xl font-black text-gray-800 m-0">450</h3>
                </div>
            </div>
            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
                <h5 class="text-lg font-bold text-gray-800 mb-4">Grafik Pertumbuhan Pendaftar</h5>
                <div id="chartUser" class="w-full h-[300px]"></div>
            </div>
        </div>

        {{-- TAB 7: WHATSAPP LEADS --}}
        <div id="tab-wa" class="tab-content">
            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm mb-6">
                <h5 class="text-lg font-bold text-gray-800 mb-4">Grafik Klik Tombol WhatsApp per Hari</h5>
                <div id="chartWa" class="w-full h-[300px]"></div>
            </div>
        </div>

    </div>
@endsection

@section('script')
    {{-- Library JavaScript untuk Grafik dan Datepicker --}}
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/id.js"></script> {{-- Bahasa Indonesia --}}

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // ==========================================
            // LOGIKA FILTER TANGGAL (FLATPICKR)
            // ==========================================
            const selectPeriode = document.getElementById('periode');
            const inputCustomDate = document.getElementById('custom_date');

            // Inisialisasi Flatpickr Range
            const fp = flatpickr(inputCustomDate, {
                mode: "range",
                dateFormat: "Y-m-d",
                locale: "id",
                onChange: function(selectedDates, dateStr, instance) {
                    if (selectedDates.length === 2) {
                        // Jika sudah pilih 2 tanggal, otomatis submit form
                        document.getElementById('filterForm').submit();
                    }
                }
            });

            selectPeriode.addEventListener('change', function() {
                if (this.value === 'custom') {
                    inputCustomDate.classList.remove('hidden');
                    fp.open(); // Otomatis buka kalender
                } else {
                    inputCustomDate.classList.add('hidden');
                    document.getElementById('filterForm').submit(); // Otomatis submit jika pilih opsi lain
                }
            });

            // ==========================================
            // LOGIKA TAB NAVIGATION (SPA STYLE)
            // ==========================================
            const tabBtns = document.querySelectorAll('.tab-btn');
            const tabContents = document.querySelectorAll('.tab-content');

            tabBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    // Reset semua tab
                    tabBtns.forEach(b => {
                        b.classList.remove('bg-[#0d9488]', 'text-white', 'shadow-md',
                            'shadow-teal-500/20');
                        b.classList.add('text-gray-500', 'hover:bg-gray-100');
                    });
                    tabContents.forEach(c => c.classList.remove('active'));

                    // Aktifkan tab yang diklik
                    btn.classList.remove('text-gray-500', 'hover:bg-gray-100');
                    btn.classList.add('bg-[#0d9488]', 'text-white', 'shadow-md',
                        'shadow-teal-500/20');

                    const targetId = btn.getAttribute('data-target');
                    document.getElementById(targetId).classList.add('active');

                    // Fix bug ApexCharts ukuran tidak sesuai saat ditaruh di dalam Tab yang di-hide
                    window.dispatchEvent(new Event('resize'));
                });
            });

            // ==========================================
            // RENDER GRAFIK DENGAN APEXCHARTS
            // Catatan: Data ini dummy, silakan ganti dengan variabel dari Controller Laravel (misal: json_encode($dataArray))
            // ==========================================

            // 1. Grafik Pengunjung (Area/Line)
            new ApexCharts(document.querySelector("#chartPengunjung"), {
                series: [{
                    name: 'Total Pengunjung',
                    data: [31, 40, 28, 51, 42, 109, 100]
                }, {
                    name: 'Unique Visitor',
                    data: [11, 32, 45, 32, 34, 52, 41]
                }],
                chart: {
                    height: 300,
                    type: 'area',
                    toolbar: {
                        show: false
                    },
                    fontFamily: 'Inter, sans-serif'
                },
                colors: ['#0d9488', '#3b82f6'],
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 2
                },
                xaxis: {
                    categories: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min']
                },
            }).render();

            // 2. Grafik Traffic (Donut)
            new ApexCharts(document.querySelector("#chartTraffic"), {
                series: [45, 25, 15, 10, 5],
                chart: {
                    type: 'donut',
                    height: 320,
                    fontFamily: 'Inter, sans-serif'
                },
                labels: ['Google', 'Instagram', 'Facebook', 'Direct', 'Referral'],
                colors: ['#ef4444', '#ec4899', '#3b82f6', '#64748b', '#f59e0b'],
                plotOptions: {
                    pie: {
                        donut: {
                            size: '70%'
                        }
                    }
                },
                legend: {
                    position: 'bottom'
                }
            }).render();

            // 3. Grafik Leads (Bar)
            new ApexCharts(document.querySelector("#chartLeads"), {
                series: [{
                    name: 'WhatsApp',
                    data: [44, 55, 41, 67, 22, 43]
                }, {
                    name: 'Form Kontak',
                    data: [13, 23, 20, 8, 13, 27]
                }],
                chart: {
                    type: 'bar',
                    height: 350,
                    stacked: true,
                    toolbar: {
                        show: false
                    },
                    fontFamily: 'Inter, sans-serif'
                },
                colors: ['#22c55e', '#3b82f6'],
                xaxis: {
                    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun']
                },
                plotOptions: {
                    bar: {
                        borderRadius: 4,
                        columnWidth: '40%'
                    }
                }
            }).render();

            // 4. Grafik Pendaftar Baru (Line)
            new ApexCharts(document.querySelector("#chartUser"), {
                series: [{
                    name: 'Pendaftar Baru',
                    data: [10, 25, 15, 40, 35, 60]
                }],
                chart: {
                    height: 300,
                    type: 'line',
                    toolbar: {
                        show: false
                    },
                    fontFamily: 'Inter, sans-serif'
                },
                colors: ['#0d9488'],
                stroke: {
                    curve: 'straight',
                    width: 3
                },
                markers: {
                    size: 5
                },
                xaxis: {
                    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun']
                },
            }).render();

            // 5. Grafik Klik WhatsApp per Hari (Column)
            new ApexCharts(document.querySelector("#chartWa"), {
                series: [{
                    name: 'Klik WhatsApp',
                    data: [15, 22, 38, 45, 19, 52, 60]
                }],
                chart: {
                    type: 'bar',
                    height: 300,
                    toolbar: {
                        show: false
                    },
                    fontFamily: 'Inter, sans-serif'
                },
                colors: ['#10b981'],
                plotOptions: {
                    bar: {
                        borderRadius: 4,
                        columnWidth: '30%'
                    }
                },
                xaxis: {
                    categories: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min']
                },
            }).render();

        });
    </script>
@endsection
