@extends('layouts.admin')

@section('style')
    <style>
        {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            transition: transform 0.3s ease;
        }

        .stat-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            /* Tambahkan transisi halus */
        }

        /* Efek hover hanya bekerja pada elemen dengan class .stat-card */
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
            /* Tambahkan shadow agar lebih cantik */
        }

        .icon-box {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .icon-primary {
            background: rgba(13, 148, 136, 0.1);
            color: #0d9488;
        }

        .icon-warning {
            background: rgba(245, 158, 11, 0.1);
            color: #f59e0b;
        }

        .icon-danger {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }

        .icon-info {
            background: rgba(59, 130, 246, 0.1);
            color: #3b82f6;
        }

        .icon-success {
            background: rgba(34, 197, 94, 0.1);
            color: #22c55e;
        }

        /* Custom Header untuk Dashboard */
        .dashboard-header {
            margin-bottom: 2rem;
        }

        .dashboard-header h3 {
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 0.25rem;
        }

        .dashboard-header p {
            color: #6b7280;
            font-size: 0.95rem;
        }

        /* Style untuk Progress Bar Sumber Traffic */
        .traffic-source-item {
            margin-bottom: 1.25rem;
        }

        .traffic-source-item:last-child {
            margin-bottom: 0;
        }

        .progress-thin {
            height: 6px;
            border-radius: 10px;
            background-color: #f3f4f6;
        }
    </style>
    {{-- Memastikan ApexCharts terload --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.css">
@endsection

@section('content')
    <div id="main" class="p-4 md:p-6 lg:p-8 bg-gray-50 min-h-screen">
        <div class="mb-8">
            <h3 class="text-2xl font-bold text-gray-800">Ringkasan Harian</h3>
            <p class="text-gray-500 text-sm mt-1">Monitoring performa dan aktivitas Dabelyuland hari ini.</p>
        </div>

        <div class="page-content">

            {{-- ========================================= --}}
            {{-- SECTION 1: METRIK UTAMA & MARKETING       --}}
            {{-- ========================================= --}}
            <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-6">
                {{-- Card 1: Total Leads Baru (Diubah menjadi Calon Pembeli) --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="p-5 md:p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h6 class="text-gray-500 font-semibold text-sm">Calon Pembeli (WA)</h6>
                            <div class="w-10 h-10 flex items-center justify-center rounded-lg bg-green-50 text-green-500">
                                <i class="fab fa-whatsapp text-lg"></i>
                            </div>
                        </div>
                        <h3 class="text-2xl md:text-3xl font-extrabold text-gray-800 mb-0">124</h3>
                        <p class="text-sm text-green-500 font-medium mb-0 mt-2">
                            <i class="fas fa-arrow-up mr-1"></i> +12% dari kemarin
                        </p>
                    </div>
                </div>

                {{-- Card 2: Properti Baru --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="p-5 md:p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h6 class="text-gray-500 font-semibold text-sm">Listing Diinput</h6>
                            <div class="w-10 h-10 flex items-center justify-center rounded-lg bg-blue-50 text-blue-500">
                                <i class="fas fa-building text-lg"></i>
                            </div>
                        </div>
                        <h3 class="text-2xl md:text-3xl font-extrabold text-gray-800 mb-0">8</h3>
                        <p class="text-sm text-gray-400 mb-0 mt-2">
                            Properti ditambahkan hari ini
                        </p>
                    </div>
                </div>

                {{-- Card 3: Pendaftaran User --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="p-5 md:p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h6 class="text-gray-500 font-semibold text-sm">User Baru</h6>
                            <div class="w-10 h-10 flex items-center justify-center rounded-lg bg-cyan-50 text-cyan-500">
                                <i class="fas fa-users text-lg"></i>
                            </div>
                        </div>
                        <h3 class="text-2xl md:text-3xl font-extrabold text-gray-800 mb-0">45</h3>
                        <p class="text-sm text-green-500 font-medium mb-0 mt-2">
                            <i class="fas fa-arrow-up mr-1"></i> +5% dari kemarin
                        </p>
                    </div>
                </div>

                {{-- Card 4: Status Banner --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="p-5 md:p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h6 class="text-gray-500 font-semibold text-sm">Status Banner</h6>
                            <div class="w-10 h-10 flex items-center justify-center rounded-lg bg-yellow-50 text-yellow-500">
                                <i class="fas fa-image text-lg"></i>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-2xl md:text-3xl font-extrabold text-gray-800 mb-0">5</h3>
                            <span class="text-xs text-gray-400 font-medium">Aktif</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                {{-- Card 1: Pendapatan Layanan (Real Cash In) --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="p-5 md:p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h6 class="text-gray-500 font-semibold text-xs uppercase tracking-wider">Pendapatan Hari Ini
                                </h6>
                                <p class="text-[10px] text-gray-400 font-medium italic">Membership & Top-up Koin</p>
                            </div>
                            <div class="icon-box icon-success">
                                <i class="fas fa-wallet"></i>
                            </div>
                        </div>
                        <div class="flex items-baseline gap-2">
                            <h3 class="text-xl md:text-2xl font-extrabold text-gray-800 mb-1">Rp 1.250.000</h3>
                            <span class="text-xs text-green-500 font-bold px-2 py-0.5 bg-green-50 rounded-md">+5.4%</span>
                        </div>
                        <p class="text-sm text-gray-400 mt-2">
                            Total uang masuk dari 12 transaksi hari ini.
                        </p>
                    </div>
                </div>

                {{-- Card 2: GMV / Perputaran Aset (Market Health) --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="p-5 md:p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h6 class="text-gray-500 font-semibold text-xs uppercase tracking-wider">Total Nilai
                                    Transaksi (GMV)</h6>
                                <p class="text-[10px] text-gray-400 font-medium italic">Akumulasi Harga Properti Terjual</p>
                            </div>
                            <div class="icon-box icon-primary">
                                <i class="fas fa-chart-line"></i>
                            </div>
                        </div>
                        <div class="flex items-baseline gap-2">
                            <h3 class="text-xl md:text-2xl font-extrabold text-gray-800 mb-1">Rp 1.450.000.000</h3>
                        </div>
                        <p class="text-sm text-gray-400 mt-2">
                            <span class="text-blue-600 font-bold">3 Unit</span> properti berhasil berganti status menjadi
                            "Sold" hari ini.
                        </p>
                    </div>
                </div>
            </div>

            {{-- ========================================= --}}
            {{-- SECTION 2: KEUANGAN, TREN & SUMBER TRAFFIC --}}
            {{-- ========================================= --}}
            {{-- SECTION 2: GRAFIK & SUMBER TRAFFIC --}}
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-6">
                {{-- Area Chart --}}
                <div class="xl:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h6 class="font-bold text-gray-800">Tren Pengunjung vs Interaksi (7 Hari)</h6>
                    </div>
                    <div id="trendChart"></div>
                </div>

                {{-- Traffic Sources --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h6 class="font-bold text-gray-800 mb-6">Sumber Pengunjung</h6>
                    <div class="space-y-5">
                        {{-- WhatsApp --}}
                        <div class="traffic-source-item">
                            <div class="flex justify-between items-center mb-2 text-xs font-bold">
                                <span class="text-gray-600"><i class="fab fa-whatsapp text-green-500 mr-2"></i>
                                    WhatsApp</span>
                                <span>45%</span>
                            </div>
                            <div class="progress-thin">
                                <div class="bg-green-500 h-full rounded-full" style="width: 45%"></div>
                            </div>
                        </div>
                        {{-- Facebook --}}
                        <div class="traffic-source-item">
                            <div class="flex justify-between items-center mb-2 text-xs font-bold">
                                <span class="text-gray-600"><i class="fab fa-facebook text-blue-600 mr-2"></i>
                                    Facebook</span>
                                <span>25%</span>
                            </div>
                            <div class="progress-thin">
                                <div class="bg-blue-600 h-full rounded-full" style="width: 25%"></div>
                            </div>
                        </div>
                        {{-- Instagram --}}
                        <div class="traffic-source-item">
                            <div class="flex justify-between items-center mb-2 text-xs font-bold">
                                <span class="text-gray-600"><i class="fab fa-instagram text-pink-500 mr-2"></i>
                                    Instagram</span>
                                <span>15%</span>
                            </div>
                            <div class="progress-thin">
                                <div class="bg-pink-500 h-full rounded-full" style="width: 15%"></div>
                            </div>
                        </div>
                        {{-- Twitter/X --}}
                        <div class="traffic-source-item">
                            <div class="flex justify-between items-center mb-2 text-xs font-bold">
                                <span class="text-gray-600"><i class="fab fa-twitter text-gray-800 mr-2"></i> Twitter /
                                    X</span>
                                <span>10%</span>
                            </div>
                            <div class="progress-thin">
                                <div class="bg-gray-800 h-full rounded-full" style="width: 10%"></div>
                            </div>
                        </div>
                        {{-- Direct --}}
                        <div class="traffic-source-item">
                            <div class="flex justify-between items-center mb-2 text-xs font-bold">
                                <span class="text-gray-600"><i class="fas fa-link text-gray-400 mr-2"></i> Lainnya</span>
                                <span>5%</span>
                            </div>
                            <div class="progress-thin">
                                <div class="bg-gray-300 h-full rounded-full" style="width: 5%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SECTION 3: TOP 3 BEST VS WORST --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Top 3 Best --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-green-50/30">
                        <h6 class="mb-0 font-bold text-green-700"><i class="fas fa-fire mr-2"></i> 3 Properti Paling
                            Populer
                        </h6>
                    </div>
                    <div class="p-6 space-y-4">
                        @foreach ([1, 2, 3] as $item)
                            <div class="flex items-center group">
                                <div class="w-12 h-12 bg-gray-100 rounded-lg mr-4 overflow-hidden shadow-sm">
                                    {{-- <img src="https://via.placeholder.com/60" class="w-full h-full object-cover"> --}}
                                </div>
                                <div class="flex-1">
                                    <h6 class="text-sm font-bold text-gray-800 mb-1">Rumah Eksklusif Kav.
                                        {{ $item }}</h6>
                                    <span class="text-[10px] bg-green-100 text-green-600 px-2 py-0.5 rounded font-bold">🔥
                                        HIGH ENGAGEMENT</span>
                                </div>
                                <div class="text-right">
                                    <span class="text-sm font-extrabold text-gray-800">{{ 500 - $item * 50 }}</span>
                                    <p class="text-[10px] text-gray-400 uppercase font-bold">Views</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Top 3 Worst --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-red-50/30">
                        <h6 class="mb-0 font-bold text-red-700"><i class="fas fa-chart-line fa-flip-vertical mr-2"></i> 3
                            Properti Kurang Diminati</h6>
                    </div>
                    <div class="p-6 space-y-4">
                        @foreach ([1, 2, 3] as $item)
                            <div class="flex items-center group">
                                <div class="w-12 h-12 bg-gray-100 rounded-lg mr-4 overflow-hidden shadow-sm">
                                    {{-- <img src="https://via.placeholder.com/60" class="w-full h-full object-cover"> --}}
                                </div>
                                <div class="flex-1">
                                    <h6 class="text-sm font-bold text-gray-800 mb-1">Tanah Pinggir Jalan
                                        B-{{ $item }}</h6>
                                    <button class="text-[10px] text-blue-600 font-bold uppercase hover:underline">Saran:
                                        rekomendasikan properti</button>
                                </div>
                                <div class="text-right">
                                    <span class="text-sm font-extrabold text-red-500">{{ 10 + $item * 2 }}</span>
                                    <p class="text-[10px] text-gray-400 uppercase font-bold">Views</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>

        <footer class="mt-10">
            <div class="text-sm text-gray-400 text-left">
                <p>2025 &copy; Dabelyuland Indonesia</p>
            </div>
        </footer>
    </div>
@endsection

@section('script')
    {{-- Library ApexCharts --}}
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            // Data Dummy untuk Grafik Tren (7 Hari)
            var options = {
                series: [{
                    name: 'Pengunjung (Views)',
                    data: [120, 150, 130, 210, 180, 250, 310]
                }, {
                    name: 'Calon Pembeli (WA)',
                    data: [30, 45, 35, 60, 50, 80, 124]
                }],
                chart: {
                    height: 450, // Ditinggikan sedikit agar proporsional dengan kolom kiri yang memanjang
                    type: 'area',
                    fontFamily: 'Inter, sans-serif',
                    toolbar: {
                        show: false
                    }
                },
                colors: ['#3b82f6', '#22c55e'],
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                xaxis: {
                    categories: ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Hari Ini'],
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    },
                    labels: {
                        style: {
                            colors: '#6b7280'
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: '#6b7280'
                        }
                    }
                },
                grid: {
                    borderColor: '#f3f4f6',
                    strokeDashArray: 4,
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'right'
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.4,
                        opacityTo: 0.05,
                        stops: [0, 90, 100]
                    }
                },
                tooltip: {
                    theme: 'light'
                }
            };

            // Render Chart
            var chart = new ApexCharts(document.getElementById("trendChart"), options);
            chart.render();
        });
    </script>
@endsection
