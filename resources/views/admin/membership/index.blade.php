@extends('layouts.admin')

@section('style')
    <style>
        #editModal {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
    </style>
    {{-- Include SweetAlert --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection

@section('content')
    <div class="w-full min-h-screen p-4 sm:p-6 lg:p-8 font-['Inter'] bg-gray-50/50">

        {{-- HEADER AREA --}}
        <div class="mb-10 text-center sm:text-left">
            <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900 m-0 font-['Plus_Jakarta_Sans'] tracking-tight">
                Paket & Harga Membership
            </h2>
            <p class="text-sm text-gray-500 m-0 mt-2 leading-relaxed max-w-2xl mx-auto sm:mx-0">
                Atur spesifikasi, kuota, dan harga jual untuk masing-masing tingkatan akun agen.
            </p>
        </div>

        {{-- PRICING CARDS GRID (DATA DINAMIS) --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8 max-w-6xl mx-auto sm:mx-0">
            @foreach ($memberships as $pkg)
                @php
                    // Identifikasi Tipe Paket untuk Styling
                    $isFreePackage = $pkg->price == 0;
                    $isBestSeller = stripos(strtolower($pkg->badge_name), 'laris') !== false;

                    if ($isFreePackage) {
                        $cardStyle = 'bg-white border-gray-200 opacity-90 hover:opacity-100';
                        $buttonStyle = 'bg-gray-100 text-gray-400 cursor-not-allowed';
                        $titleColor = 'text-gray-900';
                        $checkIconColor = 'text-green-500';
                    } elseif ($isBestSeller) {
                        $cardStyle =
                            'bg-white border-2 border-teal-600 shadow-[0_10px_30px_rgba(13,148,136,0.15)] transform md:-translate-y-4';
                        $buttonStyle = 'bg-teal-600 hover:bg-teal-700 text-white shadow-lg shadow-teal-500/30';
                        $titleColor = 'text-teal-600';
                        $checkIconColor = 'text-teal-600';
                    } else {
                        $cardStyle = 'bg-gradient-to-b from-amber-50 to-white border-amber-200 hover:shadow-lg';
                        $buttonStyle = 'bg-amber-500 hover:bg-amber-600 text-white shadow-lg shadow-amber-500/30';
                        $titleColor = 'text-amber-600';
                        $checkIconColor = 'text-amber-500';
                    }
                @endphp

                <div class="rounded-3xl p-6 lg:p-8 flex flex-col relative transition-all duration-300 {{ $cardStyle }}"
                    id="package-{{ $pkg->id }}">

                    {{-- Badge Status --}}
                    @if ($isBestSeller)
                        <div class="absolute top-0 left-1/2 -translate-x-1/2 -translate-y-1/2 z-10">
                            <span
                                class="bg-teal-600 text-white text-[10px] font-extrabold uppercase tracking-widest py-1.5 px-4 rounded-full shadow-md">
                                {{ $pkg->badge_name }}
                            </span>
                        </div>
                    @elseif($pkg->badge_name)
                        <div class="mb-4">
                            <span
                                class="inline-flex items-center px-3 py-1 {{ $isFreePackage ? 'bg-gray-100 text-gray-600' : 'bg-amber-100 text-amber-700' }} text-[10px] font-bold uppercase tracking-wider rounded-lg">
                                @if (!$isFreePackage)
                                    <i class="fas fa-crown mr-1.5"></i>
                                @endif {{ $pkg->badge_name }}
                            </span>
                        </div>
                    @endif

                    <div class="mb-6 mt-2">
                        <h3 class="text-2xl font-extrabold {{ $titleColor }} font-['Plus_Jakarta_Sans'] m-0">
                            {{ $pkg->name }}</h3>
                        <div class="mt-4 flex items-baseline text-gray-900">
                            <span class="text-3xl lg:text-4xl font-extrabold tracking-tight">Rp
                                {{ number_format($pkg->price, 0, ',', '.') }}</span>
                            <span class="text-sm text-gray-500 ml-1 font-medium">/ {{ $pkg->duration_days }} hari</span>
                        </div>
                        <p class="mt-2 text-xs text-gray-500 italic">{{ $pkg->description }}</p>
                    </div>

                    {{-- List Benefit --}}
                    <ul class="flex-1 space-y-4 text-sm text-gray-600 mb-8 p-0 m-0">
                        {{-- Kuota Upload --}}
                        <li class="flex items-start">
                            <i class="fas fa-check {{ $checkIconColor }} mt-1 mr-3 shrink-0"></i>
                            <span class="font-medium">
                                @if ($pkg->max_properties >= 9999)
                                    Upload Properti <b class="text-gray-900">Tanpa Batas</b>
                                @else
                                    Maksimal upload <b class="text-gray-900">{{ $pkg->max_properties }} Properti</b>
                                @endif
                            </span>
                        </li>

                        {{-- Kuota Rekomendasi --}}
                        <li class="flex items-start">
                            <i class="fas fa-check {{ $checkIconColor }} mt-1 mr-3 shrink-0"></i>
                            <span class="font-medium">Slot Rekomendasi: <b
                                    class="text-gray-900">{{ $pkg->recommendation_quota }} Properti</b></span>
                        </li>

                        {{-- Token Sundul (Push) --}}
                        <li class="flex items-start">
                            <i class="fas fa-bullhorn {{ $checkIconColor }} mt-1 mr-3 shrink-0"></i>
                            <span class="font-medium">Token Sundul: <b class="text-gray-900">{{ $pkg->push_quota }}x /
                                    bulan</b></span>
                        </li>

                        {{-- Kuota Highlight --}}
                        <li class="flex items-start">
                            @if ($pkg->highlight_quota > 0)
                                <i class="fas fa-check {{ $checkIconColor }} mt-1 mr-3 shrink-0"></i>
                                <span class="font-medium">Slot Highlight: <b
                                        class="text-gray-900">{{ $pkg->highlight_quota }} Properti</b></span>
                            @else
                                <i class="fas fa-times text-gray-300 mt-1 mr-3 shrink-0"></i>
                                <span class="text-gray-400 line-through">Highlight Beranda Utama</span>
                            @endif
                        </li>

                    </ul>

                    {{-- Action Button --}}
                    @if ($isFreePackage)
                        <button disabled
                            class="w-full py-3 px-4 rounded-xl font-bold text-sm transition-all {{ $buttonStyle }}">
                            Paket Dasar (Bawaan)
                        </button>
                    @else
                        {{-- Pastikan semua variabel dikirim ke function openEditModal --}}
                        <button
                            onclick="openEditModal({{ $pkg->id }}, '{{ $pkg->name }}', {{ $pkg->price }}, {{ $pkg->max_properties }}, {{ $pkg->recommendation_quota }}, {{ $pkg->push_quota }}, {{ $pkg->highlight_quota }})"
                            class="w-full py-3 px-4 rounded-xl font-bold text-sm hover:-translate-y-0.5 transition-all {{ $buttonStyle }}">
                            <i class="fas fa-edit mr-2"></i> Edit Konfigurasi
                        </button>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- ==========================================================
     MODAL EDIT PAKET (AJAX READY)
     ========================================================== --}}
    <div id="editModal" class="fixed inset-0 z-[100] hidden items-center justify-center">
        <div id="modalBackdrop"
            class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm opacity-0 transition-opacity duration-300"
            onclick="closeEditModal()"></div>

        <div id="modalBox"
            class="bg-white rounded-3xl shadow-2xl w-full max-w-md mx-4 relative z-10 opacity-0 scale-95 transition-all duration-300 transform font-['Inter'] overflow-hidden">

            <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <h3 class="font-['Plus_Jakarta_Sans'] font-extrabold text-gray-900 text-lg m-0 flex items-center">
                    <i class="fas fa-cog text-[#0d9488] mr-2"></i> Edit <span id="modalTitleName"
                        class="ml-1 text-[#0d9488]">Paket</span>
                </h3>
                <button type="button" onclick="closeEditModal()"
                    class="text-gray-400 hover:text-red-500 bg-white hover:bg-red-50 p-2 rounded-lg transition-colors focus:outline-none shadow-sm border border-gray-100">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="formEditPaket" onsubmit="savePackageData(event)">
                <input type="hidden" id="inputId">

                <div class="p-6 space-y-5">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Harga Paket (Rp) / Bulan</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-bold">Rp</span>
                            <input type="number" id="inputPrice" min="0"
                                class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-bold text-gray-900 focus:outline-none focus:border-[#0d9488] focus:bg-white transition-colors"
                                required>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Batas Upload</label>
                            <input type="number" id="inputQuota" min="1"
                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-bold text-gray-900 focus:outline-none focus:border-[#0d9488] focus:bg-white transition-colors"
                                placeholder="9999 = Unlimited" required>
                            <span class="text-[10px] text-gray-400 mt-1 block">Isi 9999 jika Tanpa Batas</span>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Kuota Highlight</label>
                            <input type="number" id="inputHighlight" min="0"
                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-bold text-gray-900 focus:outline-none focus:border-[#0d9488] focus:bg-white transition-colors"
                                required>
                        </div>
                    </div>

                    <div
                        class="p-3 bg-blue-50 border border-blue-100 rounded-lg text-xs text-blue-700 leading-relaxed flex gap-2">
                        <i class="fas fa-info-circle mt-0.5"></i>
                        Perubahan ini akan langsung disimpan ke database dan berlaku untuk pembelian paket berikutnya.
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50 flex justify-end gap-3">
                    <button type="button" onclick="closeEditModal()"
                        class="px-5 py-2.5 rounded-xl border border-gray-200 text-gray-600 font-bold text-sm hover:bg-gray-100 transition-colors">
                        Batal
                    </button>
                    <button type="submit" id="btnSave"
                        class="px-6 py-2.5 rounded-xl bg-[#0d9488] hover:bg-teal-700 text-white font-bold text-sm shadow-md hover:-translate-y-0.5 transition-all flex items-center">
                        Simpan Perubahan
                    </button>
                </div>
            </form>

        </div>
    </div>
@endsection

@section('script')
    <script>
        const modal = document.getElementById('editModal');
        const backdrop = document.getElementById('modalBackdrop');
        const modalBox = document.getElementById('modalBox');

        // Input Fields
        const titleName = document.getElementById('modalTitleName');
        const inpId = document.getElementById('inputId');
        const inpPrice = document.getElementById('inputPrice');
        const inpQuota = document.getElementById('inputQuota');
        const inpHighlight = document.getElementById('inputHighlight');

        const formatRupiah = (number) => {
            return new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 0
            }).format(number);
        };

        function openEditModal(id, name, price, quota, highlight) {
            inpId.value = id;
            titleName.innerText = name;
            inpPrice.value = price;
            inpQuota.value = quota;
            inpHighlight.value = highlight;

            modal.classList.remove('hidden');
            modal.classList.add('flex');

            setTimeout(() => {
                backdrop.classList.remove('opacity-0');
                modalBox.classList.remove('opacity-0', 'scale-95');
                modalBox.classList.add('opacity-100', 'scale-100');
            }, 10);

            document.body.style.overflow = 'hidden';
        }

        function closeEditModal() {
            backdrop.classList.add('opacity-0');
            modalBox.classList.remove('opacity-100', 'scale-100');
            modalBox.classList.add('opacity-0', 'scale-95');

            setTimeout(() => {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }, 300);
        }

        // Fungsi AJAX Update ke Database Nyata
        async function savePackageData(e) {
            e.preventDefault();
            const btn = document.getElementById('btnSave');
            const originalText = btn.innerHTML;

            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Menyimpan...';
            btn.disabled = true;

            const packageId = inpId.value;
            const formData = new FormData();
            formData.append('price', inpPrice.value);
            formData.append('max_properties', inpQuota.value);
            formData.append('highlight_quota', inpHighlight.value);

            // Karena route kita POST di web.php tapi sebenarnya tindakan ini adalah UPDATE
            // Bisa disesuaikan jika menggunakan REST API standard

            try {
                const response = await fetch(`/admin/membership/${packageId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const result = await response.json();

                if (response.ok) {
                    // Update UI Real-time tanpa refresh
                    document.getElementById(`text-price-${packageId}`).innerText = 'Rp ' + formatRupiah(inpPrice.value);
                    document.getElementById(`text-quota-${packageId}`).innerText = inpQuota.value >= 9999 ?
                        'Tanpa Batas' : `${inpQuota.value} Properti`;
                    document.getElementById(`text-highlight-${packageId}`).innerText = `${inpHighlight.value} Kuota`;

                    closeEditModal();

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Paket membership berhasil diupdate.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire('Error', result.message || 'Terjadi kesalahan sistem.', 'error');
                }
            } catch (error) {
                console.error(error);
                Swal.fire('Koneksi Gagal', 'Pastikan server database Anda berjalan.', 'error');
            } finally {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        }
    </script>
@endsection
