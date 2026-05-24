@extends('layouts.user')

@section('content')
    <div class="p-6 md:p-8 font-['Inter'] bg-[#FAFAFA] min-h-screen">
        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 font-['Plus_Jakarta_Sans'] tracking-tight">Misi & Hadiah
            </h1>
            <p class="text-sm text-gray-500 mt-2">Selesaikan misi di bawah ini untuk mendapatkan Dabelyu Koin secara gratis!
            </p>
        </div>

        {{-- Alert Notifikasi --}}
        @if (session('success'))
            <div
                class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm font-semibold flex items-center gap-3 shadow-sm">
                <i class="fas fa-check-circle text-lg"></i> {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div
                class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm font-semibold flex items-center gap-3 shadow-sm">
                <i class="fas fa-exclamation-circle text-lg"></i> {{ session('error') }}
            </div>
        @endif

        {{-- Grid Misi Dinamis --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @forelse($quests as $quest)
                @php
                    // Logika Status:
                    // isReadyToClaim = Progress mencapai target tapi belum di-klaim
                    $isReadyToClaim = $quest->current_progress >= $quest->current_target && !$quest->is_completed;

                    // Hitung persentase progres dinamis untuk progress bar (maksimal 100%)
                    $percentage =
                        $quest->current_target > 0
                            ? min(100, ($quest->current_progress / $quest->current_target) * 100)
                            : 0;

                    // Default theme & URL
                    $icon = 'fa-star';
                    $bgLight = 'bg-blue-100';
                    $textDark = 'text-blue-600';
                    $bgFill = 'bg-blue-500';
                    $targetUrl = '#'; // URL Default

                    // Tema & Tautan Berdasarkan Kode Misi
                    if ($quest->code == 'share_sosmed') {
                        $icon = 'fa-share-alt';
                        $bgLight = 'bg-teal-100';
                        $textDark = 'text-teal-600';
                        $bgFill = 'bg-teal-500';
                        $targetUrl = url('/search/');
                    } elseif ($quest->code == 'upload_property') {
                        $icon = 'fa-home';
                        $bgLight = 'bg-indigo-100';
                        $textDark = 'text-indigo-600';
                        $bgFill = 'bg-indigo-500';
                        $targetUrl = url('user/property/buildings');
                    } elseif ($quest->code == 'daily_login') {
                        $icon = 'fa-calendar-check';
                        $bgLight = 'bg-amber-100';
                        $textDark = 'text-amber-600';
                        $bgFill = 'bg-amber-500';
                        $targetUrl = url()->current(); // Memuat ulang halaman saat ini
                    } elseif ($quest->code == 'visitor_referral') {
                        $icon = 'fa-users';
                        $bgLight = 'bg-purple-100';
                        $textDark = 'text-purple-600';
                        $bgFill = 'bg-purple-500';
                        $targetUrl = url('/search/');
                    }

                    // Override UI jika sudah selesai (Hijau) atau Siap Klaim (Emas)
                    if ($quest->is_completed) {
                        $icon = 'fa-check-circle';
                        $bgLight = 'bg-green-100';
                        $textDark = 'text-green-600';
                        $bgFill = 'bg-green-500';
                    } elseif ($isReadyToClaim) {
                        $icon = 'fa-gift';
                        $bgLight = 'bg-yellow-100';
                        $textDark = 'text-yellow-600';
                        $bgFill = 'bg-yellow-400';
                    }
                @endphp

                <div
                    class="bg-white rounded-2xl p-6 border {{ $quest->is_completed ? 'border-green-100' : ($isReadyToClaim ? 'border-yellow-300 ring-2 ring-yellow-100' : 'border-gray-100') }} shadow-sm relative overflow-hidden group hover:shadow-md transition-all">

                    {{-- Efek Latar --}}
                    @if ($quest->is_completed)
                        <div class="absolute inset-0 bg-green-50/40"></div>
                    @elseif($isReadyToClaim)
                        <div class="absolute inset-0 bg-yellow-50/30"></div>
                    @else
                        <div
                            class="absolute -right-4 -top-4 w-20 h-20 {{ $bgLight }} rounded-full opacity-50 group-hover:scale-110 transition-transform">
                        </div>
                    @endif

                    <div class="flex justify-between items-start mb-4 relative z-10">
                        <div
                            class="w-12 h-12 rounded-xl {{ $bgLight }} {{ $textDark }} flex items-center justify-center text-xl">
                            <i class="fas {{ $icon }}"></i>
                        </div>

                        @if ($quest->is_completed)
                            <span
                                class="px-3 py-1 bg-green-100 text-green-700 font-bold rounded-full text-xs shadow-sm border border-green-200">
                                Selesai
                            </span>
                        @else
                            <span
                                class="px-3 py-1 bg-amber-100 text-amber-700 font-bold rounded-full text-xs flex items-center gap-1 shadow-sm border border-amber-200">
                                <i class="fas fa-coins text-amber-500"></i> +{{ $quest->base_reward_coins }} Koin
                            </span>
                        @endif
                    </div>

                    <h3 class="font-bold text-gray-900 text-lg mb-1 relative z-10">{{ $quest->title }}</h3>
                    <p class="text-xs text-gray-500 mb-5 relative z-10 line-clamp-2">Misi:
                        {{ ucwords(str_replace('_', ' ', $quest->code)) }}</p>

                    {{-- Progress Bar --}}
                    <div class="mb-5 relative z-10">
                        <div class="flex justify-between text-xs font-bold mb-1.5">
                            <span class="{{ $textDark }}">{{ $quest->is_completed ? 'Selesai' : 'Progres' }}</span>
                            <span class="{{ $quest->is_completed ? 'text-green-700' : 'text-gray-700' }}">
                                {{ $quest->current_progress }} / {{ $quest->current_target }}
                            </span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2.5">
                            <div class="{{ $bgFill }} h-2.5 rounded-full transition-all duration-500"
                                style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>

                    {{-- Tombol Aksi (3 Kondisi) --}}
                    <div class="relative z-10">
                        @if ($quest->is_completed)
                            {{-- Kondisi 1: Sudah Diklaim --}}
                            <button disabled
                                class="w-full py-2.5 bg-gray-100 text-gray-400 text-sm font-bold text-center rounded-xl cursor-not-allowed">
                                <i class="fas fa-check mr-1"></i> Hadiah Diterima
                            </button>
                        @elseif ($isReadyToClaim)
                            {{-- Kondisi 2: Siap Diklaim --}}
                            <form action="{{ route('user.quest.claim', $quest->user_quest_id) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="w-full py-2.5 bg-yellow-400 hover:bg-yellow-500 text-yellow-900 shadow-md shadow-yellow-200/50 text-sm font-bold text-center rounded-xl transition-all transform hover:-translate-y-0.5 flex justify-center items-center gap-2">
                                    <i class="fas fa-coins"></i> Klaim Hadiah
                                </button>
                            </form>
                        @else
                            {{-- Kondisi 3: Masih Dikerjakan --}}
                            <a href="{{ $targetUrl }}"
                                class="block w-full py-2.5 bg-gray-900 hover:bg-gray-800 text-white text-sm font-bold text-center rounded-xl transition-colors no-underline">
                                Mulai Kerjakan
                            </a>
                        @endif
                    </div>
                </div>

            @empty
                <div class="col-span-full bg-white p-8 rounded-2xl border border-gray-100 text-center shadow-sm">
                    <div
                        class="w-16 h-16 bg-gray-50 text-gray-300 rounded-full flex items-center justify-center text-2xl mx-auto mb-3">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <h3 class="font-bold text-gray-900 text-lg">Belum Ada Misi</h3>
                    <p class="text-sm text-gray-500 mt-1">Saat ini belum ada misi yang aktif. Silakan kembali lagi nanti!
                    </p>
                </div>
            @endforelse
        </div>
    </div>
@endsection
