@extends('layouts.admin')

@section('style')
    {{-- Memastikan SweetAlert2 & Datatables tersedia --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* ==========================================================
                                                                                                                                                                                                                           STYLING KHUSUS UNTUK TABEL & TABS
                                                                                                                                                                                                                           ========================================================== */

        /* Header Tabel yang bersih dan modern */
        .table-custom th {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            color: #64748b;
            background-color: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
            padding: 1rem 1.25rem;
            vertical-align: middle;
        }

        .table-custom tbody tr {
            transition: all 0.2s ease;
            border-bottom: 1px solid #f1f5f9;
        }

        .table-custom tbody tr:hover {
            background-color: #f8fafc;
        }

        .table-custom td {
            padding: 1.25rem;
            vertical-align: middle;
        }

        /* Thumbnail Gambar Properti */
        .property-thumbnail {
            width: 120px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            border: 1px solid #f1f5f9;
            transition: transform 0.3s ease;
        }

        .table-custom tbody tr:hover .property-thumbnail {
            transform: scale(1.05);
        }

        /* Tab Navigation Styling */
        .tab-btn {
            position: relative;
            color: #64748b;
            transition: all 0.3s ease;
        }

        .tab-btn:hover {
            color: #0f172a;
        }

        .tab-btn.active {
            color: #0d9488;
            font-weight: 800;
        }

        .tab-btn::after {
            content: '';
            position: absolute;
            bottom: -2px;
            /* Menutupi border-bottom parent */
            left: 0;
            width: 0%;
            height: 3px;
            background-color: #0d9488;
            transition: width 0.3s ease;
            border-radius: 3px 3px 0 0;
        }

        .tab-btn.active::after {
            width: 100%;
        }

        /* Kustomisasi SweetAlert */
        .swal2-popup {
            border-radius: 1.5rem !important;
            font-family: 'Inter', sans-serif;
        }
    </style>
@endsection

@section('content')
    <div class="w-full min-h-screen p-4 sm:p-6 lg:p-8 font-['Inter'] bg-gray-50/50">

        {{-- ==========================================================
         1. HEADER AREA
         ========================================================== --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900 m-0 font-['Plus_Jakarta_Sans'] tracking-tight">
                    Highlight & Rekomendasi
                </h2>
                <p class="text-sm text-gray-500 m-0 mt-2 leading-relaxed max-w-2xl">
                    Kelola penempatan properti eksklusif di halaman beranda.
                </p>
            </div>

            <button type="button" onclick="handleAddManual()"
                class="inline-flex items-center px-6 py-3 bg-gray-900 hover:bg-black text-white text-sm font-bold rounded-xl transition-all shadow-md hover:-translate-y-0.5 no-underline shrink-0">
                <i class="fas fa-plus mr-2"></i>
                Tambah Manual
            </button>
        </div>

        {{-- ==========================================================
         2. TAB NAVIGATION
         ========================================================== --}}
        <div class="border-b border-gray-200 mb-6 flex gap-6 px-2">
            <button type="button" class="tab-btn active pb-3 text-sm font-semibold focus:outline-none"
                onclick="switchTab('rekomendasi')">
                <i class="fas fa-thumbs-up mr-1.5 text-blue-400"></i> Rekomendasi Properti
            </button>
            <button type="button" class="tab-btn pb-3 text-sm font-semibold focus:outline-none"
                onclick="switchTab('highlight')">
                <i class="fas fa-star mr-1.5 text-yellow-400"></i> Highlight Premium
            </button>
        </div>

        {{-- ==========================================================
        3. TAB CONTENT: REKOMENDASI (Pengajuan Normal)
        ========================================================== --}}
        <div id="tab-rekomendasi" class="tab-content block">
            <div class="bg-white rounded-3xl shadow-[0_4px_25px_rgba(0,0,0,0.03)] border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left align-middle border-collapse table-custom" id="rekomendasiTable">
                        <thead>
                            <tr class="bg-gray-50/50">
                                <th class="w-[30%] py-4 px-6 text-xs font-bold text-gray-400 uppercase tracking-wider">
                                    Informasi Properti</th>
                                <th class="w-[20%] py-4 px-6 text-xs font-bold text-gray-400 uppercase tracking-wider">
                                    Pemilik / Agent</th>
                                <th
                                    class="w-[15%] py-4 px-6 text-xs font-bold text-gray-400 uppercase tracking-wider text-center">
                                    Sisa Durasi</th>
                                <th
                                    class="w-[20%] py-4 px-6 text-xs font-bold text-gray-400 uppercase tracking-wider text-center">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($recommendations ?? [] as $item)
                                @php
                                    $mainImage = !empty($item->mainImage)
                                        ? asset('storage/' . $item->mainImage->image_path . '-image_low.webp')
                                        : asset('frontside/img/default-property.jpg');

                                    // Data Owner untuk Modal
                                    $ownerData = json_encode([
                                        'name' => $item->user->name ?? 'Fadli',
                                        'nowa' => $item->user->nowa ?? '-',
                                        'email' => $item->user->email ?? '-',
                                        'role' => $item->user->role ?? 'User',
                                        'join_date' => \Carbon\Carbon::parse($item->user->created_at)->format('d M Y'),
                                    ]);

                                    // Logika Sisa Durasi (30 Hari)
                                    $now = now();
                                    $pushedAt = \Carbon\Carbon::parse($item->pushed_at);
                                    $expiredAt = \Carbon\Carbon::parse($item->expired_at);

                                    $isExpired = $now->greaterThan($expiredAt);
                                    $diff = $isExpired ? 0 : $now->diffInDays($expiredAt);

                                    $totalDays = $pushedAt->diffInDays($expiredAt);
                                    $remainingDays = $now->diffInDays($expiredAt, false);

                                    $refundInfo = '';
                                    if ($item->amount_token > 0) {
                                        $refundInfo =
                                            '<strong>1 Slot Rekomendasi</strong> akan dikembalikan ke akun Anda.';
                                    } elseif ($item->amount_dabelyu_koin > 0 && $remainingDays > 0) {
                                        // Rumus Pro-rata: (Sisa Hari / Total Hari) * Harga Awal
                                        $estimate = floor(($remainingDays / $totalDays) * $item->amount_dabelyu_koin);
                                        $refundInfo = "Estimasi <strong>{$estimate} Koin</strong> akan dikembalikan ke dompet Anda.";
                                    } else {
                                        $refundInfo =
                                            'Tidak ada pengembalian koin untuk promosi yang sudah hampir berakhir.';
                                    }

                                    // Teks lengkap untuk SweetAlert
                                    $swalText = "Properti ini akan diturunkan dari daftar unggulan.<br><br><small class='text-gray-500'>{$refundInfo}</small>";
                                @endphp
                                <tr class="hover:bg-gray-50/80 transition-all duration-200">
                                    {{-- KOLOM PROPERTI --}}
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-4">
                                            {{-- Thumbnail tetap sama --}}
                                            <img src="{{ $mainImage }}" alt="Thumb"
                                                class="w-14 h-14 rounded-2xl object-cover border-2 border-white shadow-sm ring-1 ring-gray-100">

                                            <div class="min-w-0">
                                                <h6 class="font-bold text-gray-900 truncate text-sm mb-1">
                                                    {{ $item->property->judul }}
                                                </h6>

                                                <div class="flex items-center gap-2">
                                                    @php
                                                        // Logika: Top 3 dikasih warna spesial (Gold) agar user tahu mereka di posisi 'panas'
                                                        $isTopRank = $loop->iteration <= 3;
                                                        $bgClass = $isTopRank
                                                            ? 'bg-amber-50 border-amber-200 text-amber-700'
                                                            : 'bg-teal-50 border-teal-100 text-[#0d9488]';
                                                        $iconClass = $isTopRank
                                                            ? 'fas fa-trophy'
                                                            : 'fas fa-arrow-up-9-1';
                                                    @endphp

                                                    <span
                                                        class="inline-flex items-center px-2 py-0.5 rounded-md border {{ $bgClass }} text-[10px] font-extrabold uppercase tracking-widest shadow-sm">
                                                        <i class="{{ $iconClass }} mr-1.5"></i>
                                                        Urutan ke-{{ $loop->iteration }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- KOLOM PEMILIK (Clickable) --}}
                                    <td class="px-6 py-4">
                                        <button type="button" onclick='showOwnerDetail({!! $ownerData !!})'
                                            class="flex items-center group text-left focus:outline-none transition-transform active:scale-95">
                                            <div
                                                class="w-9 h-9 rounded-full overflow-hidden bg-teal-50 flex items-center justify-center mr-3 group-hover:bg-[#0d9488] transition-all duration-300">
                                                <img src="/assets/images/faces/2.jpg" alt="">
                                            </div>
                                            <div class="min-w-0">
                                                <p
                                                    class="text-sm font-bold text-gray-700 group-hover:text-[#0d9488] transition-colors truncate">
                                                    {{ $item->user->name ?? 'Admin' }}</p>
                                                <p class="text-[10px] text-gray-400 font-medium">Klik untuk profil</p>
                                            </div>
                                        </button>
                                    </td>

                                    {{-- KOLOM SISA DURASI --}}
                                    <td class="px-6 py-4 text-center">
                                        @if ($isExpired)
                                            <span
                                                class="px-3 py-1 bg-red-50 text-red-500 text-[11px] font-bold rounded-lg border border-red-100">Expired</span>
                                        @else
                                            <div class="inline-flex flex-col">
                                                <span
                                                    class="text-sm font-extrabold {{ $diff <= 5 ? 'text-rose-500 animate-pulse' : 'text-gray-800' }}">{{ $diff }}
                                                    Hari</span>
                                                <div class="w-16 h-1.5 bg-gray-100 rounded-full mt-1 overflow-hidden">
                                                    <div class="h-full bg-[#0d9488]"
                                                        style="width: {{ ($diff / 30) * 100 }}%"></div>
                                                </div>
                                            </div>
                                        @endif
                                    </td>

                                    {{-- KOLOM AKSI --}}
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex justify-center items-center gap-2">
                                            {{-- Tombol Sundul (Bump) --}}
                                            <button type="button"
                                                onclick="handleSundul('{{ $item->property_id }}', {{ $push_quota }}, {{ $dabelyu_koin }})"
                                                class="w-9 h-9 rounded-xl bg-teal-50 text-[#0d9488] flex items-center justify-center hover:bg-[#0d9488] hover:text-white transition-all duration-300 shadow-sm group"
                                                title="Sundul ke Atas">
                                                <i
                                                    class="fas fa-rocket text-xs group-hover:-translate-y-1 transition-transform"></i>
                                            </button>

                                            {{-- Tombol Delete --}}
                                            <button type="button"
                                                onclick="confirmAction(
                                                    '{{ route('account.highlight.destroy', ['type' => 'recommendation', 'property_id' => $item->property_id]) }}',
                                                    'Hapus {{ ucfirst('rekomendasi') }}', 
                                                    {{ json_encode($swalText) }}, 
                                                    'DELETE'
                                                )"
                                                class="w-9 h-9 rounded-xl bg-rose-50 text-rose-500 flex items-center justify-center hover:bg-rose-500 hover:text-white transition-all duration-300 shadow-sm">
                                                <i class="fas fa-trash-alt text-xs"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-20 text-center">
                                        <div class="flex flex-col items-center">
                                            <i class="fas fa-folder-open text-gray-200 text-5xl mb-4"></i>
                                            <p class="text-gray-400 text-sm font-medium">Belum ada pengajuan rekomendasi
                                                saat ini.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ==========================================================
         4. TAB CONTENT: HIGHLIGHT (Berbasis Durasi / Paket)
         ========================================================== --}}
        <div id="tab-highlight" class="tab-content hidden">
            <div class="bg-white rounded-3xl shadow-[0_4px_25px_rgba(0,0,0,0.03)] border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left align-middle border-collapse table-custom" id="rekomendasiTable">
                        <thead>
                            <tr class="bg-gray-50/50">
                                <th class="w-[30%] py-4 px-6 text-xs font-bold text-gray-400 uppercase tracking-wider">
                                    Informasi Properti</th>
                                <th class="w-[20%] py-4 px-6 text-xs font-bold text-gray-400 uppercase tracking-wider">
                                    Pemilik / Agent</th>
                                <th
                                    class="w-[15%] py-4 px-6 text-xs font-bold text-gray-400 uppercase tracking-wider text-center">
                                    Sisa Durasi</th>
                                <th
                                    class="w-[20%] py-4 px-6 text-xs font-bold text-gray-400 uppercase tracking-wider text-center">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($highlights ?? [] as $item)
                                @php
                                    $mainImage = !empty($item->mainImage)
                                        ? asset('storage/' . $item->mainImage->image_path . '-image_low.webp')
                                        : asset('frontside/img/default-property.jpg');

                                    // Data Owner untuk Modal
                                    $ownerData = json_encode([
                                        'name' => $item->user->name ?? 'Fadli',
                                        'nowa' => $item->user->nowa ?? '-',
                                        'email' => $item->user->email ?? '-',
                                        'role' => $item->user->role ?? 'User',
                                        'join_date' => \Carbon\Carbon::parse($item->user->created_at)->format('d M Y'),
                                    ]);

                                    // Logika Sisa Durasi (30 Hari)
                                    $now = now();
                                    $pushedAt = \Carbon\Carbon::parse($item->pushed_at);
                                    $expiredAt = \Carbon\Carbon::parse($item->expired_at);

                                    $isExpired = $now->greaterThan($expiredAt);
                                    $diff = $isExpired ? 0 : $now->diffInDays($expiredAt);

                                    $totalDays = $pushedAt->diffInDays($expiredAt);
                                    $remainingDays = $now->diffInDays($expiredAt, false);

                                    $refundInfo = '';
                                    if ($item->amount_token > 0) {
                                        $refundInfo =
                                            '<strong>1 Slot Highlight</strong> akan dikembalikan ke akun Anda.';
                                    } elseif ($item->amount_dabelyu_koin > 0 && $remainingDays > 0) {
                                        // Rumus Pro-rata: (Sisa Hari / Total Hari) * Harga Awal
                                        $estimate = floor(($remainingDays / $totalDays) * $item->amount_dabelyu_koin);
                                        $refundInfo = "Estimasi <strong>{$estimate} Koin</strong> akan dikembalikan ke dompet Anda.";
                                    } else {
                                        $refundInfo =
                                            'Tidak ada pengembalian koin untuk promosi yang sudah hampir berakhir.';
                                    }

                                    // Teks lengkap untuk SweetAlert
                                    $swalText = "Properti ini akan dihapus dari daftar highlight pada halaman utama.<br><br><small class='text-gray-500'>{$refundInfo}</small>";
                                @endphp
                                <tr class="hover:bg-gray-50/80 transition-all duration-200">
                                    {{-- KOLOM PROPERTI --}}
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-4">
                                            {{-- Thumbnail tetap sama --}}
                                            <img src="{{ $mainImage }}" alt="Thumb"
                                                class="w-14 h-14 rounded-2xl object-cover border-2 border-white shadow-sm ring-1 ring-gray-100">

                                            <div class="min-w-0">
                                                <h6 class="font-bold text-gray-900 truncate text-sm mb-1">
                                                    {{ $item->property->judul }}
                                                </h6>

                                                <div class="flex items-center gap-2">
                                                    <span
                                                        class="inline-flex items-center px-2 py-0.5 rounded-md border bg-teal-50 border-teal-100 text-[#0d9488] text-[10px] font-extrabold uppercase tracking-widest shadow-sm transition-all">
                                                        <i class="fas fa-map-marker-alt mr-1.5"></i>
                                                        {{ $item->property->kota }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- KOLOM PEMILIK (Clickable) --}}
                                    <td class="px-6 py-4">
                                        <button type="button" onclick='showOwnerDetail({!! $ownerData !!})'
                                            class="flex items-center group text-left focus:outline-none transition-transform active:scale-95">
                                            <div
                                                class="w-9 h-9 rounded-full overflow-hidden bg-teal-50 flex items-center justify-center mr-3 group-hover:bg-[#0d9488] transition-all duration-300">
                                                <img src="/assets/images/faces/2.jpg" alt="">
                                            </div>
                                            <div class="min-w-0">
                                                <p
                                                    class="text-sm font-bold text-gray-700 group-hover:text-[#0d9488] transition-colors truncate">
                                                    {{ $item->user->name ?? 'Admin' }}</p>
                                                <p class="text-[10px] text-gray-400 font-medium">Klik untuk profil</p>
                                            </div>
                                        </button>
                                    </td>

                                    {{-- KOLOM SISA DURASI --}}
                                    <td class="px-6 py-4 text-center">
                                        @if ($isExpired)
                                            <span
                                                class="px-3 py-1 bg-red-50 text-red-500 text-[11px] font-bold rounded-lg border border-red-100">Expired</span>
                                        @else
                                            <div class="inline-flex flex-col">
                                                <span
                                                    class="text-sm font-extrabold {{ $diff <= 5 ? 'text-rose-500 animate-pulse' : 'text-gray-800' }}">{{ $diff }}
                                                    Hari</span>
                                                <div class="w-16 h-1.5 bg-gray-100 rounded-full mt-1 overflow-hidden">
                                                    <div class="h-full bg-[#0d9488]"
                                                        style="width: {{ ($diff / 30) * 100 }}%"></div>
                                                </div>
                                            </div>
                                        @endif
                                    </td>

                                    {{-- KOLOM AKSI --}}
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex justify-center items-center gap-2">
                                            {{-- Tombol Delete --}}
                                            <button type="button"
                                                onclick="confirmAction(
                                                    '{{ route('account.highlight.destroy', ['type' => 'highlight', 'property_id' => $item->property_id]) }}',
                                                    'Hapus {{ ucfirst('highlight') }}', 
                                                    {{ json_encode($swalText) }}, 
                                                    'DELETE'
                                                )"
                                                class="w-9 h-9 rounded-xl bg-rose-50 text-rose-500 flex items-center justify-center hover:bg-rose-500 hover:text-white transition-all duration-300 shadow-sm">
                                                <i class="fas fa-trash-alt text-xs"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-20 text-center">
                                        <div class="flex flex-col items-center">
                                            <i class="fas fa-folder-open text-gray-200 text-5xl mb-4"></i>
                                            <p class="text-gray-400 text-sm font-medium">Belum ada pengajuan rekomendasi
                                                saat ini.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- MODAL DETAIL PEMILIK (Hidden by default) --}}
        <div id="ownerModal" class="fixed inset-0 z-50 flex items-center justify-center hidden">
            <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" onclick="closeOwnerModal()"></div>
            <div class="relative bg-white w-[90%] max-w-sm rounded-3xl shadow-2xl p-6 transform transition-all duration-300 scale-95 opacity-0"
                id="ownerModalContent">
                <div class="text-center">
                    <div
                        class="w-20 h-20 bg-teal-50 rounded-full flex items-center justify-center mx-auto mb-4 border-4 border-white shadow-md">
                        <i class="fas fa-user-tie text-3xl text-[#0d9488]"></i>
                    </div>
                    <h3 id="modalOwnerName" class="font-heading text-xl font-extrabold text-gray-900 mb-1">Nama Pemilik
                    </h3>
                    <p id="modalOwnerRole"
                        class="text-xs font-bold text-teal-600 bg-teal-50 px-3 py-1 rounded-full inline-block mb-6 uppercase tracking-widest">
                        Role</p>

                    <div class="space-y-3 text-left">
                        <div class="flex items-center p-3 bg-gray-50 rounded-2xl">
                            <i class="fab fa-whatsapp w-8 text-[#0d9488] text-lg"></i>
                            <div>
                                <p class="text-[10px] text-gray-400 font-bold uppercase">WhatsApp</p>
                                <p id="modalOwnerWa" class="text-sm font-bold text-gray-700">+62 812-3456-7890</p>
                            </div>
                        </div>
                        <div class="flex items-center p-3 bg-gray-50 rounded-2xl">
                            <i class="fas fa-envelope w-8 text-[#0d9488] text-lg"></i>
                            <div>
                                <p class="text-[10px] text-gray-400 font-bold uppercase">Email</p>
                                <p id="modalOwnerEmail" class="text-sm font-bold text-gray-700 truncate">owner@email.com
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center p-3 bg-gray-50 rounded-2xl">
                            <i class="fas fa-calendar-alt w-8 text-[#0d9488] text-lg"></i>
                            <div>
                                <p class="text-[10px] text-gray-400 font-bold uppercase">Bergabung Sejak</p>
                                <p id="modalOwnerJoin" class="text-sm font-bold text-gray-700">01 Jan 2026</p>
                            </div>
                        </div>
                    </div>

                    <button onclick="closeOwnerModal()"
                        class="w-full mt-8 py-3 bg-gray-900 text-white font-bold rounded-xl hover:bg-black transition-colors shadow-lg">
                        Tutup Detail
                    </button>
                </div>
            </div>
        </div>

        {{-- Form Master untuk Eksekusi Aksi (Hapus/Batal) --}}
        <form id="masterActionForm" method="POST" style="display: none;">
            @csrf
            <input type="hidden" name="_method" id="methodInput" value="POST">
        </form>

    </div>
@endsection

@section('script')
    <script>
        /**
         * FUNGSI: MENAMPILKAN MODAL PEMILIK
         */
        function showOwnerDetail(data) {
            const modal = document.getElementById('ownerModal');
            const content = document.getElementById('ownerModalContent');

            // Isi data ke modal
            document.getElementById('modalOwnerName').innerText = data.name;
            document.getElementById('modalOwnerRole').innerText = data.role;
            document.getElementById('modalOwnerWa').innerText = data.nowa;
            document.getElementById('modalOwnerEmail').innerText = data.email;
            document.getElementById('modalOwnerJoin').innerText = data.join_date;

            // Animasi Muncul
            modal.classList.remove('hidden');
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closeOwnerModal() {
            const modal = document.getElementById('ownerModal');
            const content = document.getElementById('ownerModalContent');

            content.classList.replace('scale-100', 'opacity-100', 'scale-95', 'opacity-0');
            setTimeout(() => modal.classList.add('hidden'), 10);
        }
    </script>
    <script>
        const userWallet = {
            highlight: {{ $highlight_quota ?? 0 }},
            rekomendasi: {{ $recommendation_quota ?? 0 }},
            koin: {{ $dabelyu_koin ?? 0 }}
        };

        let currentTab = 'rekomendasi';

        // ==========================================
        // FUNGSI NAVIGASI TAB
        // ==========================================
        function switchTab(tabName) {
            currentTab = tabName;
            // Sembunyikan semua konten tab
            document.querySelectorAll('.tab-content').forEach(el => {
                el.classList.remove('block');
                el.classList.add('hidden');
            });

            // Hapus kelas aktif dari semua tombol
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });

            // Tampilkan tab yang dipilih
            document.getElementById('tab-' + tabName).classList.remove('hidden');
            document.getElementById('tab-' + tabName).classList.add('block');

            // Aktifkan tombol yang diklik
            event.currentTarget.classList.add('active');
        }

        // Fungsi pemicu Dialog jika Kuota Habis
        function handleAddManual() {
            // 1. Tentukan kuota & harga berdasarkan tab aktif
            const isHighlight = (currentTab === 'highlight');
            const quotaKey = isHighlight ? 'highlight' : 'rekomendasi';
            const currentQuota = userWallet[quotaKey];

            // Gunakan harga yang kita set kemarin (Highlight 120, Rekomendasi 50)
            const price = isHighlight ? 120 : 50;

            // 2. LOGIKA UTAMA

            // Kasus A: Kuota masih ada -> Langsung Gass ke Page Create
            if (currentQuota > 0) {
                window.location.href = "{{ route('account.highlight.create') }}";
            }
            // Kasus B: Kuota Habis, tapi Koin Cukup -> Munculkan Konfirmasi Penggunaan Koin
            else if (userWallet.koin >= price) {
                Swal.fire({
                    title: `Slot ${currentTab.charAt(0).toUpperCase() + currentTab.slice(1)} Habis`,
                    html: `Kuota gratis Anda sudah habis. Anda akan menggunakan <strong>Dabelyu Koin</strong> untuk aktivasi ini. Lanjutkan?`,
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonColor: '#111827',
                    cancelButtonColor: '#9ca3af',
                    confirmButtonText: 'Ya, Gunakan Koin',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    customClass: {
                        popup: 'rounded-2xl font-["Inter"]'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "{{ route('account.highlight.create') }}";
                    }
                });
            }
            // Kasus C: Kuota Habis & Koin Tidak Cukup -> Peringatan Gagal
            else {
                Swal.fire({
                    title: 'Saldo Tidak Cukup',
                    html: `Kuota <strong>${currentTab}</strong> habis dan saldo Dabelyu Koin Anda (${userWallet.koin}) tidak cukup untuk membayar biaya <strong>${price} koin</strong>.`,
                    icon: 'error',
                    confirmButtonColor: '#ef4444',
                    confirmButtonText: 'Tutup',
                    customClass: {
                        popup: 'rounded-2xl font-["Inter"]'
                    }
                });
            }
        }

        function handleSundul(id, tokenCount, coinCount, biayaKoin = 50) {
            const hasToken = tokenCount > 0;

            // Konfigurasi Konten Modal (Tetap sesuai permintaanmu)
            const modalConfig = hasToken ? {
                title: 'Sundul Properti?',
                html: `
                    <div class="p-2">
                        <p class="text-sm text-gray-500 mb-4">Gunakan 1 Token untuk menaikkan properti ini ke urutan teratas.</p>
                        <div class="inline-flex items-center px-4 py-2 bg-teal-50 rounded-2xl border border-teal-100">
                            <i class="fas fa-ticket-alt text-[#0d9488] mr-2"></i>
                            <span class="text-sm font-bold text-gray-700">Sisa Token: <span class="text-[#0d9488]">${tokenCount}</span></span>
                        </div>
                    </div>
                `,
                confirmButtonText: 'Gunakan Token',
            } : {
                title: 'Token Habis!',
                html: `
                    <div class="p-2">
                        <p class="text-sm text-gray-500 mb-4">Token Sundul Anda sudah habis. Gunakan dabelyu koin untuk tetap bisa menyundul.</p>
                        <div class="flex flex-col items-center gap-3">
                            <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center border-4 border-white shadow-sm mb-2">
                                <i class="fas fa-coins text-amber-500 text-2xl"></i>
                            </div>
                            <div class="flex gap-4">
                                <div class="px-3 py-1 bg-gray-100 rounded-lg text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                                    Biaya: ${biayaKoin} Koin
                                </div>
                                <div class="px-3 py-1 bg-amber-50 rounded-lg text-[11px] font-bold text-amber-600 uppercase tracking-wider">
                                    Saldo: ${coinCount} Koin
                                </div>
                            </div>
                        </div>
                    </div>
                `,
                confirmButtonText: 'Bayar dengan Koin',
            };

            // Eksekusi SweetAlert2
            Swal.fire({
                title: modalConfig.title,
                html: modalConfig.html,
                icon: hasToken ? 'question' : 'info',
                showCancelButton: true,
                confirmButtonColor: hasToken ? '#0d9488' : '#f59e0b',
                cancelButtonColor: '#9ca3af',
                confirmButtonText: modalConfig.confirmButtonText,
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-2xl font-["Inter"]'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // 1. Validasi Koin di Sisi Client
                    if (!hasToken && coinCount < biayaKoin) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Koin Tidak Cukup',
                            text: 'Silakan isi ulang koin Dabelyuland Anda.',
                            confirmButtonColor: '#0d9488'
                        });
                        return;
                    }

                    // 2. Loading State saat Tembak API
                    Swal.fire({
                        title: 'Memproses...',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });

                    // 3. Tembak API Asli
                    fetch("{{ route('account.highlight.sundul') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}' // Penting untuk keamanan Laravel
                            },
                            body: JSON.stringify({
                                property_id: id,
                                type: currentTab, // Mengikuti state tab aktif (highlight/recommendation)
                                method: hasToken ? 'token' : 'coin'
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: data.message,
                                    icon: 'success',
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    // Refresh halaman agar urutan dan saldo terupdate[cite: 1]
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Gagal', data.message, 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire('Error', 'Terjadi kesalahan pada jaringan.', 'error');
                        });
                }
            });
        }

        // ==========================================
        // FUNGSI KONFIRMASI AKSI (SWEETALERT2)
        // ==========================================
        function confirmAction(url, titleText, htmlText, methodType = 'POST') {
            Swal.fire({
                title: titleText,
                html: htmlText, // Tetap gunakan html agar format Blade tadi muncul
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#9ca3af',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-2xl font-["Inter"]'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('masterActionForm');
                    const methodInput = document.getElementById('methodInput');

                    form.action = url;
                    methodInput.value = methodType;

                    Swal.fire({
                        title: 'Memproses...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    form.submit();
                }
            });
        }
    </script>
@endsection
