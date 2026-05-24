@extends('layouts.user')

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

            {{-- SECTION BARU: SALDO KOIN & STATUS MEMBERSHIP (MINIMALIST) --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6 mb-6">

                {{-- 1. Card Saldo Koin (Compact Dark Theme) --}}
                <div
                    class="bg-[#0f172a] rounded-xl shadow-md border border-slate-700/50 p-5 md:p-6 flex items-center justify-between relative overflow-hidden group">
                    {{-- Dekorasi Glow Halus --}}
                    <div
                        class="absolute -right-10 -bottom-10 w-24 h-24 bg-amber-500/10 blur-xl rounded-full pointer-events-none transition-transform group-hover:scale-110">
                    </div>

                    <div class="relative z-10 flex flex-col justify-center">
                        <h6 class="text-slate-400 font-bold text-[10px] uppercase tracking-widest mb-1.5">Saldo Koin Dabelyu
                        </h6>
                        <div class="flex items-center gap-2.5">
                            <i class="fas fa-coins text-amber-400 text-xl drop-shadow-sm"></i>
                            <h3 class="text-2xl font-black text-white tracking-tight">
                                {{ number_format($wallet->dabelyu_koin ?? 0) }}</h3>
                        </div>
                    </div>

                    <a href={{ route('user.topup.index') }}
                        class="relative z-10 w-10 h-10 rounded-full bg-amber-500 hover:bg-amber-400 text-slate-900 flex items-center justify-center text-sm transition-all shadow-[0_4px_10px_rgba(245,158,11,0.3)] hover:scale-105 active:scale-95">
                        <i class="fas fa-plus font-bold"></i>
                    </a>
                </div>

                {{-- 2. Card Status Membership (Compact & Dynamic Theme) --}}
                <div
                    class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 md:p-6 flex items-center justify-between">
                    <div class="flex items-center gap-4">

                        @php
                            $memName = $membership->name ?? 'Basic (Gratis)';
                            $nameLower = strtolower($memName);

                            // Default styling (Basic/Free)
                            $iconBg = 'bg-slate-50';
                            $iconBorder = 'border-slate-100';
                            $iconColor = 'text-slate-400';
                            $dotColor = 'bg-gray-300';
                            $dotShadow = '';

                            // Tema Dinamis Berdasarkan Tingkat Membership
                            if (str_contains($nameLower, 'bronze')) {
                                $iconBg = 'bg-[#fff7ed]';
                                $iconBorder = 'border-[#fed7aa]';
                                $iconColor = 'text-[#d97706]';
                                $dotColor = 'bg-[#d97706]';
                                $dotShadow = 'shadow-[0_0_5px_rgba(217,119,6,0.5)]';
                            } elseif (str_contains($nameLower, 'silver')) {
                                $iconBg = 'bg-[#f8fafc]';
                                $iconBorder = 'border-[#cbd5e1]';
                                $iconColor = 'text-[#64748b]';
                                $dotColor = 'bg-[#64748b]';
                                $dotShadow = 'shadow-[0_0_5px_rgba(100,116,139,0.5)]';
                            } elseif (str_contains($nameLower, 'gold') || str_contains($nameLower, 'premium')) {
                                $iconBg = 'bg-[#fefce8]';
                                $iconBorder = 'border-[#fef08a]';
                                $iconColor = 'text-[#eab308]';
                                $dotColor = 'bg-[#eab308]';
                                $dotShadow = 'shadow-[0_0_5px_rgba(234,179,8,0.5)]';
                            }
                        @endphp

                        <div
                            class="w-12 h-12 rounded-full {{ $iconBg }} border {{ $iconBorder }} flex items-center justify-center {{ $iconColor }} text-lg shadow-inner transition-colors">
                            <i class="fas fa-shield-alt"></i>
                        </div>

                        <div>
                            <h6 class="text-gray-400 font-bold text-[10px] uppercase tracking-widest mb-0.5">Status
                                Membership</h6>
                            <div class="flex items-center gap-2">
                                <h5 class="font-extrabold text-gray-900 text-lg tracking-tight">{{ $memName }}</h5>
                                <span class="w-2 h-2 rounded-full {{ $dotColor }} {{ $dotShadow }}"></span>
                            </div>
                        </div>
                    </div>

                    <a href="#"
                        class="px-4 py-2 bg-teal-50 hover:bg-teal-100 text-teal-700 text-xs font-bold rounded-lg transition-colors border border-teal-100">
                        Upgrade
                    </a>
                </div>

            </div>

            {{-- SECTION 1: KUOTA & MARKETING METRICS (Dipindah ke bawah Saldo & Membership) --}}
            <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-6">

                @php
                    // Set default jika wallet belum ada
                    $recQuota = $wallet->recommendation_quota ?? 0;
                    $maxRec = $membership ? $membership->recommendation_quota : 1;
                    $recPercent = min(100, ($recQuota / max(1, $maxRec)) * 100);

                    $highQuota = $wallet->highlight_quota ?? 0;
                    $maxHigh = $membership ? $membership->highlight_quota : 1;
                    $highPercent = min(100, ($highQuota / max(1, $maxHigh)) * 100);

                    $bannerQuota = $wallet->banner_quota ?? 0;
                    $maxBanner = $membership ? $membership->banner_quota : 1;
                    $bannerPercent = min(100, ($bannerQuota / max(1, $maxBanner)) * 100);

                    $pushQuota = $wallet->push_quota ?? 0;
                @endphp

                {{-- Slot Rekomendasi --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 md:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h6 class="text-gray-500 font-semibold text-xs uppercase tracking-wider">Slot Rekomendasi</h6>
                        <div class="icon-box icon-teal"><i class="fas fa-star"></i></div>
                    </div>
                    <h3 class="text-2xl font-extrabold text-gray-800">
                        {{ $recQuota }} <span class="text-gray-300 text-lg">/
                            {{ $membership->recommendation_quota ?? 0 }}</span>
                    </h3>
                    <div class="w-full bg-gray-100 h-1.5 rounded-full mt-3 overflow-hidden">
                        <div class="bg-teal-500 h-1.5 rounded-full transition-all duration-1000 ease-out"
                            style="width: {{ $recPercent }}%"></div>
                    </div>
                </div>

                {{-- Slot Highlight --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 md:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h6 class="text-gray-500 font-semibold text-xs uppercase tracking-wider">Slot Highlight</h6>
                        <div class="icon-box icon-amber"><i class="fas fa-crown"></i></div>
                    </div>
                    <h3 class="text-2xl font-extrabold text-gray-800">
                        {{ $highQuota }} <span class="text-gray-300 text-lg">/
                            {{ $membership->highlight_quota ?? 0 }}</span>
                    </h3>
                    <div class="w-full bg-gray-100 h-1.5 rounded-full mt-3 overflow-hidden">
                        <div class="bg-amber-500 h-1.5 rounded-full transition-all duration-1000 ease-out"
                            style="width: {{ $highPercent }}%"></div>
                    </div>
                </div>

                {{-- Kuota Banner --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 md:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h6 class="text-gray-500 font-semibold text-xs uppercase tracking-wider">Kuota Banner</h6>
                        <div class="icon-box icon-purple"><i class="fas fa-image"></i></div>
                    </div>
                    <h3 class="text-2xl font-extrabold text-gray-800">
                        {{ $bannerQuota }} <span class="text-gray-300 text-lg">/
                            {{ $membership->banner_quota ?? 0 }}</span>
                    </h3>
                    <div class="w-full bg-gray-100 h-1.5 rounded-full mt-3 overflow-hidden">
                        <div class="bg-purple-500 h-1.5 rounded-full transition-all duration-1000 ease-out"
                            style="width: {{ $bannerPercent }}%"></div>
                    </div>
                </div>

                {{-- Token Sundul (Push) --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 md:p-6 relative overflow-hidden group">
                    <div
                        class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform duration-500">
                        <i class="fas fa-rocket text-8xl text-blue-500"></i>
                    </div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            <h6 class="text-gray-500 font-semibold text-xs uppercase tracking-wider">Token Sundul</h6>
                            <div class="icon-box icon-blue"><i class="fas fa-rocket"></i></div>
                        </div>
                        <h3 class="text-2xl font-extrabold text-gray-800">{{ $pushQuota }}</h3>
                        <p class="text-[10px] text-blue-500 font-bold mt-2 uppercase">Gunakan untuk naik ke posisi atas</p>
                    </div>
                </div>
            </div>

            {{-- SECTION 2: GRAFIK & SUMBER TRAFFIC --}}
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-6">
                {{-- Area Chart (Sebelah Kiri) --}}
                <div class="xl:col-span-2 bg-white rounded-xl shadow-xs border border-gray-200/80 p-6 tracking-tight">
                    <!-- Header Konten -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                        <div>
                            <h6 class="text-base font-semibold text-gray-900 leading-none mb-1.5">
                                Tren Pengunjung Properti vs Klik WhatsApp (7 Hari)
                            </h6>
                            <p class="text-xs text-gray-500 leading-relaxed max-w-xl">
                                Analisis komparatif antara total kunjungan halaman properti dan jumlah klik tombol WhatsApp
                                untuk memantau efisiensi konversi mingguan.
                            </p>
                        </div>
                    </div>

                    <!-- Area Grafik -->
                    <div id="trendChart" class="min-h-[300px] w-full get-chart-ready"></div>
                </div>


                {{-- Kolom Kanan (CTA & Traffic Sources) --}}
                <div class="flex flex-col gap-6">

                    {{-- 3. CTA CARD (Luxurious Gold Gradient) --}}
                    <div
                        class="bg-gradient-to-br from-[#b8860b] via-[#d4af37] to-[#8b6508] rounded-xl shadow-md p-6 text-white relative overflow-hidden group">

                        {{-- Elemen Dekorasi (Efek Pantulan Emas) --}}
                        <div
                            class="absolute -right-4 -top-10 w-32 h-32 bg-white/20 rounded-full blur-xl transition-transform duration-700 group-hover:scale-150 group-hover:bg-white/30">
                        </div>
                        <div class="absolute -left-10 -bottom-10 w-32 h-32 bg-yellow-200/20 rounded-full blur-2xl"></div>

                        <div class="relative z-10">
                            <span
                                class="inline-block bg-white/20 backdrop-blur-md px-2.5 py-1 rounded shadow-sm border border-white/20 text-[10px] font-bold text-yellow-50 uppercase tracking-widest mb-3">
                                Lebih Cepat Laku
                            </span>
                            <h3 class="text-lg font-bold mb-2 leading-tight drop-shadow-sm text-white">Jadikan Properti Anda
                                Tampil Paling Atas!</h3>
                            <p class="text-xs text-yellow-50 mb-5 leading-relaxed opacity-90 drop-shadow-sm">
                                Gunakan Dabelyu Koin untuk fitur Highlight dan Rekomendasi. Tingkatkan peluang terjual
                                hingga 5x lipat.
                            </p>
                            <a href={{ route('user.topup.index') }}
                                class="block w-full py-2.5 bg-white text-[#b8860b] hover:bg-yellow-50 text-sm font-extrabold text-center rounded-xl transition-all shadow-[0_4px_15px_rgba(0,0,0,0.15)] hover:shadow-[0_4px_20px_rgba(212,175,55,0.4)]">
                                Top Up Koin Sekarang
                            </a>
                        </div>
                    </div>

                    {{-- Traffic Sources --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex-1">
                        <h6 class="font-bold text-gray-800 mb-6">Sumber Pengunjung Properti (Hari Ini)</h6>
                        <div class="space-y-5">
                            {{-- WhatsApp --}}
                            <div class="traffic-source-item">
                                <div class="flex justify-between items-center mb-2 text-xs font-bold">
                                    <span class="text-gray-600"><i class="fab fa-whatsapp text-green-500 mr-2"></i>
                                        WhatsApp</span>
                                    <span>{{ $trafficSources['wa'] }}%</span>
                                </div>
                                <div class="progress-thin">
                                    <div class="bg-green-500 h-full rounded-full"
                                        style="width: {{ $trafficSources['wa'] }}%"></div>
                                </div>
                            </div>
                            {{-- Facebook --}}
                            <div class="traffic-source-item">
                                <div class="flex justify-between items-center mb-2 text-xs font-bold">
                                    <span class="text-gray-600"><i class="fab fa-facebook text-blue-600 mr-2"></i>
                                        Facebook</span>
                                    <span>{{ $trafficSources['fb'] }}%</span>
                                </div>
                                <div class="progress-thin">
                                    <div class="bg-blue-600 h-full rounded-full"
                                        style="width: {{ $trafficSources['fb'] }}%"></div>
                                </div>
                            </div>
                            {{-- Instagram --}}
                            <div class="traffic-source-item">
                                <div class="flex justify-between items-center mb-2 text-xs font-bold">
                                    <span class="text-gray-600"><i class="fab fa-instagram text-pink-500 mr-2"></i>
                                        Instagram</span>
                                    <span>{{ $trafficSources['ig'] }}%</span>
                                </div>
                                <div class="progress-thin">
                                    <div class="bg-pink-500 h-full rounded-full"
                                        style="width: {{ $trafficSources['ig'] }}%"></div>
                                </div>
                            </div>
                            {{-- Twitter/X --}}
                            <div class="traffic-source-item">
                                <div class="flex justify-between items-center mb-2 text-xs font-bold">
                                    <span class="text-gray-600"><i class="fab fa-twitter text-gray-800 mr-2"></i> Twitter
                                        / X</span>
                                    <span>{{ $trafficSources['twitter'] }}%</span>
                                </div>
                                <div class="progress-thin">
                                    <div class="bg-gray-800 h-full rounded-full"
                                        style="width: {{ $trafficSources['twitter'] }}%"></div>
                                </div>
                            </div>
                            {{-- Direct --}}
                            <div class="traffic-source-item">
                                <div class="flex justify-between items-center mb-2 text-xs font-bold">
                                    <span class="text-gray-600"><i class="fas fa-link text-gray-400 mr-2"></i>
                                        Lainnya</span>
                                    <span>{{ $trafficSources['other'] }}%</span>
                                </div>
                                <div class="progress-thin">
                                    <div class="bg-gray-300 h-full rounded-full"
                                        style="width: {{ $trafficSources['other'] }}%"></div>
                                </div>
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
                            (Hari Ini)</h6>
                    </div>
                    <div class="p-6 space-y-4">
                        @forelse ($topProperties as $item)
                            <div class="flex items-center group">
                                <div class="w-12 h-12 bg-gray-100 rounded-lg mr-4 overflow-hidden shadow-sm">
                                    <img src="{{ $item->mainImage ? asset('storage/' . $item->mainImage->image_path . '-image_low.webp') : 'https://via.placeholder.com/60' }}"
                                        class="w-full h-full object-cover">
                                </div>
                                <div class="flex-1">
                                    <h6 class="text-sm font-bold text-gray-800 mb-1 line-clamp-1">{{ $item->judul }}</h6>
                                    <span class="text-[10px] bg-green-100 text-green-600 px-2 py-0.5 rounded font-bold">🔥
                                        HIGH
                                        ENGAGEMENT</span>
                                </div>
                                <div class="text-right">
                                    <span
                                        class="text-sm font-extrabold text-gray-800">{{ number_format($item->views_count) }}</span>
                                    <p class="text-[10px] text-gray-400 uppercase font-bold">Views</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-400 text-center py-4">Belum ada data pengunjung hari ini.</p>
                        @endforelse
                    </div>
                </div>

                {{-- Top 3 Worst --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-red-50/30">
                        <h6 class="mb-0 font-bold text-red-700"><i class="fas fa-chart-line fa-flip-vertical mr-2"></i> 3
                            Properti Kurang Diminati (Hari Ini)</h6>
                    </div>
                    <div class="p-6 space-y-4">
                        @forelse ($worstProperties as $item)
                            <div class="flex items-center group">
                                <div class="w-12 h-12 bg-gray-100 rounded-lg mr-4 overflow-hidden shadow-sm">
                                    <img src="{{ $item->mainImage ? asset('storage/' . $item->mainImage->image_path . '-image_low.webp') : 'https://via.placeholder.com/60' }}"
                                        class="w-full h-full object-cover">
                                </div>
                                <div class="flex-1">
                                    <h6 class="text-sm font-bold text-gray-800 mb-1 line-clamp-1">{{ $item->judul }}</h6>
                                    <button class="text-[10px] text-blue-600 font-bold uppercase hover:underline">Saran:
                                        rekomendasikan properti</button>
                                </div>
                                <div class="text-right">
                                    <span
                                        class="text-sm font-extrabold text-red-500">{{ number_format($item->views_count) }}</span>
                                    <p class="text-[10px] text-gray-400 uppercase font-bold">Views</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-400 text-center py-4">Belum ada data pengunjung hari ini.</p>
                        @endforelse
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

            const labels = @json($chartLabels);
            const viewsData = @json($chartViews);
            const waData = @json($chartWa);

            var options = {
                series: [{
                    name: 'Pengunjung (Views)',
                    data: viewsData
                }, {
                    name: 'Calon Pembeli (WA)',
                    data: waData
                }],
                chart: {
                    height: 450,
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
                    categories: labels, // Label hari dinamis dari Controller
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
