@extends('layouts.admin')

@section('style')
    <style>
        .metric-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            transition: transform 0.3s ease;
        }

        .metric-card:hover {
            transform: translateY(-3px);
        }

        .icon-box {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .icon-teal {
            background: rgba(13, 148, 136, 0.1);
            color: #0d9488;
        }

        .icon-amber {
            background: rgba(245, 158, 11, 0.1);
            color: #f59e0b;
        }

        .icon-blue {
            background: rgba(59, 130, 246, 0.1);
            color: #3b82f6;
        }

        .icon-purple {
            background: rgba(139, 92, 246, 0.1);
            color: #8b5cf6;
        }

        .icon-pink {
            background: rgba(236, 72, 153, 0.1);
            color: #ec4899;
        }

        .icon-green {
            background: rgba(34, 197, 94, 0.1);
            color: #22c55e;
        }

        .icon-red {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }

        .progress-thin {
            height: 6px;
            border-radius: 10px;
            background-color: #f3f4f6;
        }
    </style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.css">
@endsection

@section('content')
    <div id="main" class="p-4 md:p-6 lg:p-8 bg-gray-50 min-h-screen font-['Plus_Jakarta_Sans']">
        {{-- Header --}}
        <div class="mb-8">
            <h3 class="text-2xl font-bold text-gray-800">Dashboard Performa Agen</h3>
            <p class="text-gray-500 text-sm mt-1">Kelola visibilitas properti Anda dan pantau interaksi calon pembeli.</p>
        </div>

        <div class="page-content">
            {{-- SECTION 1: KUOTA & MARKETING METRICS --}}
            <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-6">
                {{-- Slot Rekomendasi --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 md:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h6 class="text-gray-500 font-semibold text-xs uppercase tracking-wider">Slot Rekomendasi</h6>
                        <div class="icon-box icon-teal"><i class="fas fa-star"></i></div>
                    </div>
                    <h3 class="text-2xl font-extrabold text-gray-800">8 <span class="text-gray-300 text-lg">/ 10</span></h3>
                    <div class="w-full bg-gray-100 h-1.5 rounded-full mt-3">
                        <div class="bg-teal-500 h-1.5 rounded-full" style="width: 80%"></div>
                    </div>
                </div>

                {{-- Slot Highlight --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 md:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h6 class="text-gray-500 font-semibold text-xs uppercase tracking-wider">Slot Highlight</h6>
                        <div class="icon-box icon-amber"><i class="fas fa-crown"></i></div>
                    </div>
                    <h3 class="text-2xl font-extrabold text-gray-800">1 <span class="text-gray-300 text-lg">/ 2</span></h3>
                    <div class="w-full bg-gray-100 h-1.5 rounded-full mt-3">
                        <div class="bg-amber-500 h-1.5 rounded-full" style="width: 50%"></div>
                    </div>
                </div>

                {{-- Kuota Banner (Updated dari Leads WA) --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 md:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h6 class="text-gray-500 font-semibold text-xs uppercase tracking-wider">Kuota Banner</h6>
                        <div class="icon-box icon-purple"><i class="fas fa-image"></i></div>
                    </div>
                    {{-- Data Dummy: Misal sudah dipakai 2 dari total 5 --}}
                    <h3 class="text-2xl font-extrabold text-gray-800">2 <span class="text-gray-300 text-lg">/ 5</span></h3>
                    <div class="w-full bg-gray-100 h-1.5 rounded-full mt-3">
                        <div class="bg-purple-500 h-1.5 rounded-full" style="width: 40%"></div>
                    </div>
                </div>

                {{-- Token Sundul (Push) --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 md:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h6 class="text-gray-500 font-semibold text-xs uppercase tracking-wider">Token Sundul</h6>
                        <div class="icon-box icon-blue"><i class="fas fa-rocket"></i></div>
                    </div>
                    <h3 class="text-2xl font-extrabold text-gray-800">15</h3>
                    <p class="text-[10px] text-blue-500 font-bold mt-2 uppercase">Gunakan untuk naik ke posisi atas</p>
                </div>
            </div>

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
                        <h6 class="mb-0 font-bold text-green-700"><i class="fas fa-fire mr-2"></i> 3 Properti Paling Populer
                        </h6>
                    </div>
                    <div class="p-6 space-y-4">
                        @foreach ([1, 2, 3] as $item)
                            <div class="flex items-center group">
                                <div class="w-12 h-12 bg-gray-100 rounded-lg mr-4 overflow-hidden shadow-sm">
                                    <img src="https://via.placeholder.com/60" class="w-full h-full object-cover">
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
                                    <img src="https://via.placeholder.com/60" class="w-full h-full object-cover">
                                </div>
                                <div class="flex-1">
                                    <h6 class="text-sm font-bold text-gray-800 mb-1">Tanah Pinggir Jalan
                                        B-{{ $item }}</h6>
                                    <button class="text-[10px] text-blue-600 font-bold uppercase hover:underline">Saran:
                                        Sundul Sekarang</button>
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

        <footer class="mt-10 pb-4 text-center md:text-left">
            <p class="text-xs text-gray-400">2026 &copy; TebasLahan Management System</p>
        </footer>
    </div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var options = {
                series: [{
                    name: 'Pengunjung',
                    data: [400, 520, 480, 700, 600, 850, 1024]
                }, {
                    name: 'Interaksi WA',
                    data: [20, 35, 30, 55, 45, 70, 95]
                }],
                chart: {
                    height: 350,
                    type: 'area',
                    fontFamily: 'Plus Jakarta Sans, sans-serif',
                    toolbar: {
                        show: false
                    }
                },
                colors: ['#3b82f6', '#22c55e'],
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.3,
                        opacityTo: 0.05,
                        stops: [0, 90, 100]
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 2
                },
                xaxis: {
                    categories: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    }
                },
                grid: {
                    borderColor: '#f3f4f6',
                    strokeDashArray: 4
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'right'
                }
            };
            var chart = new ApexCharts(document.querySelector("#trendChart"), options);
            chart.render();
        });
    </script>
@endsection
