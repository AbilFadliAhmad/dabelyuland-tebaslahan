@extends('layouts.user')

@section('style')
    <style>
        /* Animasi Koin Mengambang untuk efek dinamis */
        @keyframes float {
            0% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-8px);
            }

            100% {
                transform: translateY(0px);
            }
        }

        .animate-float {
            animation: float 3s ease-in-out infinite;
        }

        /* Animasi kilau (shine) pada tombol premium */
        @keyframes shine {
            100% {
                left: 125%;
            }
        }

        .btn-shine::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 50%;
            height: 100%;
            background: linear-gradient(to right, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 0.4) 50%, rgba(255, 255, 255, 0) 100%);
            transform: skewX(-20deg);
            animation: shine 3s infinite;
        }

        @keyframes rotate-slow {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .gold-aura {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 150%;
            height: 150%;
            background: radial-gradient(circle, rgba(251, 191, 36, 0.4) 0%, rgba(251, 191, 36, 0) 70%);
            z-index: -1;
            animation: rotate-slow 10s linear infinite;
        }
    </style>
@endsection

@section('content')
    <div class="p-4 sm:p-6 lg:p-8 font-['Inter'] bg-[#f8fafc] min-h-screen">
        <div class="max-w-[90rem] mx-auto">

            {{-- ==========================================================
             1. HEADER / SALDO CARD (Premium Look)
             ========================================================== --}}
            <div
                class="relative bg-gradient-to-br from-gray-900 via-[#111827] to-[#0f172a] rounded-[2rem] p-8 sm:p-10 mb-12 overflow-hidden shadow-2xl border border-gray-800">
                {{-- Elemen Dekorasi Background --}}
                <div
                    class="absolute top-0 right-0 -mt-10 -mr-10 w-64 h-64 bg-[#0d9488] rounded-full mix-blend-multiply filter blur-[80px] opacity-40 animate-pulse">
                </div>
                <div
                    class="absolute bottom-0 left-0 -mb-10 -ml-10 w-64 h-64 bg-amber-500 rounded-full mix-blend-multiply filter blur-[80px] opacity-20">
                </div>

                <div class="relative z-10 flex flex-col lg:flex-row items-center justify-between gap-8">
                    <div class="text-center lg:text-left text-white max-w-2xl">
                        <span
                            class="inline-block py-1 px-3 rounded-full bg-white/10 border border-white/20 text-amber-400 text-[10px] font-bold uppercase tracking-widest mb-4">
                            Pusat Layanan Promosi
                        </span>
                        <h2 class="font-['Plus_Jakarta_Sans'] text-3xl md:text-5xl font-extrabold mb-3 tracking-tight">
                            Toko Dabelyu Koin
                        </h2>
                        <p class="text-gray-400 text-sm md:text-base leading-relaxed">
                            Tingkatkan visibilitas properti Anda ke level maksimal. Pilih paket koin yang sesuai dengan
                            kebutuhan promosi Anda. Beli lebih banyak, hemat lebih banyak!
                        </p>
                    </div>

                    {{-- Digital Wallet Card --}}
                    <div
                        class="bg-white/10 border border-white/20 p-6 rounded-3xl flex items-center gap-5 sm:min-w-[320px] shadow-[0_8px_32px_rgba(0,0,0,0.25)]">
                        <div class="w-25 h-25 rounded-full p-2.5 shrink-0">
                            <img src="/assets/images/icons/koin.svg" class="w-full h-full object-contain" alt="Koin">
                        </div>
                        <div>
                            <p class="text-gray-300 text-xs font-bold uppercase tracking-widest mb-1">Saldo Koin Anda</p>
                            <div class="flex items-end gap-1">
                                <p
                                    class="text-4xl md:text-5xl font-extrabold text-white font-['Plus_Jakarta_Sans'] leading-none">
                                    {{ number_format($dabelyuKoin ?? 0, 0, ',', '.') }}
                                </p>
                                <span class="text-amber-400 font-bold mb-1">Koin</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ==========================================================
             2. PRICING CARDS GRID (Dynamic from Database)
             ========================================================== --}}

            @if ($packages->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6 xl:gap-5 items-end">

                    @foreach ($packages as $pkg)
                        @php
                            // Logika Styling Berdasarkan Tema (Hierarchy UI)
                            $bgClass = 'bg-white border-gray-200';
                            $textKoin = 'text-gray-900';
                            $textPrice = 'text-[#0d9488]';
                            $textDesc = 'text-gray-500';
                            $btnClass =
                                'bg-gray-50 text-gray-700 hover:bg-[#0d9488] hover:text-white border border-gray-200';
                            $badgeClass = 'bg-gray-800 text-white';
                            $cardTransform = 'hover:-translate-y-2 hover:shadow-[0_20px_40px_rgba(0,0,0,0.06)]';
                            $glowEffect = '';

                            // PERUBAHAN: Memanggil data object dengan panah ->
                            if ($pkg->theme == 'popular') {
                                $bgClass =
                                    'bg-white border-[#0d9488]/30 shadow-[0_10px_30px_rgba(13,148,136,0.1)] ring-1 ring-[#0d9488]/10';
                                $btnClass = 'bg-[#0d9488] text-white hover:bg-teal-700 shadow-md shadow-teal-700/20';
                                $badgeClass = 'bg-[#0d9488] text-white';
                            } elseif ($pkg->theme == 'dark') {
                                $bgClass = 'bg-gray-900 border-gray-800 shadow-[0_20px_40px_rgba(0,0,0,0.15)]';
                                $textKoin = 'text-white';
                                $textPrice = 'text-amber-400';
                                $textDesc = 'text-gray-400';
                                $btnClass =
                                    'bg-gray-800 text-white hover:bg-amber-400 hover:text-gray-900 border border-gray-700';
                            } elseif ($pkg->theme == 'gold') {
                                $bgClass =
                                    'bg-gradient-to-b from-amber-400 to-amber-500 border-amber-300 shadow-[0_20px_50px_rgba(245,158,11,0.3)] xl:-mt-6 xl:mb-2 z-10';
                                $textKoin = 'text-amber-950';
                                $textPrice = 'text-amber-900';
                                $textDesc = 'text-amber-800/80';
                                $btnClass =
                                    'bg-amber-900 text-amber-400 hover:bg-gray-900 shadow-lg shadow-amber-900/30 relative overflow-hidden';
                                $badgeClass = 'bg-red-600 text-white animate-pulse shadow-lg';
                                $cardTransform = 'hover:-translate-y-3';
                                $glowEffect =
                                    'before:absolute before:inset-0 before:-z-10 before:bg-amber-500 before:blur-2xl before:opacity-0 hover:before:opacity-50 before:transition-opacity before:duration-500';
                            }
                        @endphp

                        <div
                            class="relative group flex flex-col h-full rounded-[2rem] border {{ $bgClass }} p-6 md:p-8 transition-all duration-500 {{ $cardTransform }} {{ $glowEffect }}">

                            {{-- @if ($pkg->theme == 'gold')
                        <div class="gold-aura"></div>
                    @endif --}}

                            {{-- Badge Promo --}}
                            @if ($pkg->badge)
                                <div class="absolute -top-3 left-1/2 -translate-x-1/2 w-max z-20">
                                    <span
                                        class="px-4 py-1.5 text-[10px] sm:text-xs font-extrabold uppercase tracking-wider rounded-full shadow-sm {{ $badgeClass }}">
                                        {{ $pkg->badge }}
                                    </span>
                                </div>
                            @endif

                            {{-- Konten Atas: Ikon & Jumlah Koin --}}
                            <div
                                class="text-center mb-6 flex-col flex items-center justify-start flex-grow z-10 min-h-[160px]">

                                {{-- CONTAINER GAMBAR MENGGUNAKAN FOLDER STORAGE --}}
                                <div
                                    class="w-24 h-24 mb-5 flex items-center justify-center shrink-0 {{ $pkg->theme == 'gold' ? 'animate-float drop-shadow-[0_10px_15px_rgba(120,53,15,0.3)]' : 'group-hover:scale-110 transition-transform duration-500' }}">
                                    {{-- Membaca dari folder storage/koin/ --}}
                                    <img src="/assets/images/icon/koin.svg"
                                        onerror="this.outerHTML='<i class=\'fas fa-coins text-5xl text-amber-400 drop-shadow-md\'></i>'"
                                        class="w-full h-full object-contain drop-shadow-md" alt="Koin {{ $pkg->koin }}">
                                </div>

                                <div class="flex items-baseline justify-center gap-1.5 mb-1 h-[40px]">
                                    <h3
                                        class="font-['Plus_Jakarta_Sans'] text-4xl font-extrabold {{ $textKoin }} tracking-tight m-0">
                                        {{ $pkg->koin }}
                                    </h3>
                                    <span
                                        class="text-sm font-bold uppercase tracking-widest {{ $textKoin }} opacity-70">Koin</span>
                                </div>

                                <p class="text-xs font-medium {{ $textDesc }} m-0 line-clamp-2 h-[32px]">
                                    {{ $pkg->desc }}</p>
                            </div>

                            {{-- Garis Pemisah --}}
                            <div
                                class="w-full h-px shrink-0 {{ $pkg->theme == 'gold' ? 'bg-amber-300/50' : ($pkg->theme == 'dark' ? 'bg-gray-800' : 'bg-gray-100') }} mb-6 z-10">
                            </div>

                            {{-- Konten Bawah: Harga & Tombol --}}
                            <div class="w-full text-center mt-auto z-10 shrink-0">

                                {{-- Teks Hemat --}}
                                <div class="mb-2 h-[24px] flex items-center justify-center">
                                    @if ($pkg->saving)
                                        <span
                                            class="inline-block px-3 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider {{ $pkg->theme == 'gold' ? 'bg-amber-900/10 text-amber-900' : ($pkg->theme == 'dark' ? 'bg-gray-800 text-gray-400' : 'bg-gray-50 text-gray-500') }}">
                                            {{ $pkg->saving }}
                                        </span>
                                    @endif
                                </div>

                                {{-- Ganti tag <form> dengan div atau modifikasi button --}}
                                <div class="w-full text-center mt-auto z-10 shrink-0">
                                    <p
                                        class="font-extrabold text-2xl md:text-3xl mb-5 {{ $textPrice }} font-['Plus_Jakarta_Sans'] shrink-0">
                                        Rp {{ number_format($pkg->harga, 0, ',', '.') }}
                                    </p>

                                    <button type="button"
                                        onclick="confirmPurchase('{{ $pkg->id }}', '{{ $pkg->koin }}', '{{ $pkg->harga }}')"
                                        class="w-full py-3.5 rounded-xl font-extrabold text-sm transition-all duration-300 {{ $btnClass }}">
                                        Beli Sekarang
                                    </button>
                                </div>
                            </div>

                        </div>
                    @endforeach

                </div>
            @else
                {{-- Tampilan Jika Admin Belum Menambahkan Paket Sama Sekali --}}
                <div class="bg-white rounded-3xl border border-gray-200 p-12 text-center shadow-sm">
                    <i class="fas fa-box-open text-gray-300 text-6xl mb-4"></i>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Toko Koin Sedang Kosong</h3>
                    <p class="text-gray-500 text-sm">Saat ini belum ada paket koin yang tersedia. Silakan hubungi admin.</p>
                </div>
            @endif

            {{-- ==========================================================
             3. FAQ / INFO TAMBAHAN
             ========================================================== --}}
            <div
                class="mt-20 bg-white rounded-3xl p-8 border border-gray-200 shadow-sm flex flex-col md:flex-row items-center gap-8">
                <div class="w-full md:w-1/3 text-center md:text-left">
                    <div
                        class="w-16 h-16 bg-teal-50 text-[#0d9488] rounded-2xl flex items-center justify-center text-2xl mx-auto md:mx-0 mb-4">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h4 class="font-['Plus_Jakarta_Sans'] text-xl font-bold text-gray-900 mb-2">Transaksi Aman & Instan</h4>
                    <p class="text-sm text-gray-500">Koin akan langsung masuk ke akun Anda setelah pembayaran berhasil
                        dikonfirmasi.</p>
                </div>
                <div class="w-full md:w-2/3 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="p-4 rounded-2xl bg-gray-50 border border-gray-300">
                        <h5 class="font-bold text-gray-800 text-sm mb-1"><i class="fas fa-star text-amber-500 mr-2"></i>
                            Highlight Properti</h5>
                        <p class="text-xs text-gray-500">Gunakan koin untuk membuat properti Anda tampil di urutan teratas
                            pencarian.</p>
                    </div>
                    <div class="p-4 rounded-2xl bg-gray-50 border border-gray-300">
                        <h5 class="font-bold text-gray-800 text-sm mb-1"><i
                                class="fas fa-thumbs-up text-[#0d9488] mr-2"></i> Rekomendasi Utama</h5>
                        <p class="text-xs text-gray-500">Gunakan koin untuk memasang badge "Rekomendasi" agar lebih dilirik
                            pembeli.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- Custom Snap Modal -->
    <div id="paymentModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4 sm:p-6">
        <!-- Overlay: Klik di sini untuk menutup modal -->
        <div onclick="closePaymentModal()" class="fixed inset-0 transition-opacity bg-gray-900/60"></div>

        <!-- Modal Dialog -->
        <div
            class="relative z-10 w-full max-w-md bg-white shadow-2xl rounded-[2.5rem] border border-gray-300 transform transition-all flex flex-col max-h-[90vh]">

            <!-- Header: Tetap di atas -->
            <div class="relative p-8 pb-0 shrink-0">
                <div class="flex items-center justify-between mb-6">
                    <img src="/frontside/img/logo-dabelyuland.png" class="h-8" alt="Logo">
                    <button onclick="closePaymentModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <div class="text-center p-6 bg-teal-50 rounded-3xl border-2 border-teal-300/50">
                    <p class="text-xs font-bold text-teal-600 uppercase tracking-widest mb-1">Total Pembayaran</p>
                    <span id="modalOrderId"
                        class="text-[10px] text-gray-600 font-semibold uppercase tracking-wide mb-1"></span>
                    <h2 id="modalAmount" class="text-3xl font-extrabold text-gray-900 font-['Plus_Jakarta_Sans']">Rp 0</h2>
                </div>
            </div>

            <!-- Body: Bisa di-scroll jika konten terlalu panjang (Laptop friendly) -->
            <div id="modalBody" class="p-8 overflow-y-auto custom-scrollbar">
                <!-- Step 1: Pilih Metode -->
                <div id="stepSelectMethod">
                    <h3 class="text-sm font-bold text-gray-800 mb-4">Pilih Dompet Digital</h3>
                    <div class="space-y-3">
                        @foreach (['qris' => 'QRIS (Semua E-Wallet)', 'gopay' => 'GoPay', 'shopeepay' => 'ShopeePay'] as $code => $label)
                            <label
                                class="group relative flex items-center p-4 border-2 border-gray-300 rounded-2xl cursor-pointer hover:border-teal-500 hover:bg-teal-50/30 transition-all">
                                <input type="radio" name="pay_method" value="{{ $code }}" class="hidden peer">
                                <div
                                    class="w-10 h-10 flex items-center justify-center bg-white rounded-xl shadow-sm border border-gray-300 group-hover:scale-110 transition-transform">
                                    <img src="/assets/images/icons/{{ $code }}.svg"
                                        class="{{ $code === 'dana' ? 'w-8 h-8' : 'w-6 h-6' }} object-contain"
                                        alt="{{ $label }}">
                                </div>
                                <span class="ml-4 font-bold text-gray-700 text-sm">{{ $label }}</span>
                                <div
                                    class="ml-auto w-5 h-5 rounded-full border-2 border-gray-200 peer-checked:border-teal-500 peer-checked:bg-teal-500 transition-all flex items-center justify-center">
                                    <i class="fas fa-check text-[10px] text-white"></i>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    <button type="button" onclick="proceedPayment()"
                        class="w-full mt-8 py-4 bg-gray-900 text-white rounded-2xl font-bold text-sm shadow-xl hover:bg-teal-600 transition-all transform active:scale-95">
                        Bayar Sekarang
                    </button>
                </div>

                <!-- Step 2: QR / Instruksi -->
                <div id="stepInstruction" class="hidden text-center">
                    <div class="mb-6">
                        <div id="timer"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-red-50 text-red-600 rounded-full text-xs font-bold animate-pulse">
                            <i class="far fa-clock"></i> <span>15:00</span>
                        </div>
                    </div>

                    <div id="qrContainer"
                        class="bg-white p-4 border-2 border-teal-300 rounded-3xl inline-block shadow-inner mb-6">
                        <!-- QR Code Image -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Tambahan agar scrollbar di dalam modal terlihat lebih clean */
        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #e5e7eb;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #d1d5db;
        }
    </style>
@endsection

@section('script')
    <script>
        let currentPackageId = null;
        let timerInterval = null;
        let pollingInterval = null; // Tambahkan variabel global untuk polling

        function confirmPurchase(packageId, koinAmount, price) {
            currentPackageId = packageId;
            document.getElementById('modalAmount').innerText = `Rp ${new Intl.NumberFormat('id-ID').format(price)}`;

            Swal.fire({
                title: 'Konfirmasi Top Up',
                text: `Beli ${koinAmount} Koin?`,
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#0d9488',
                confirmButtonText: 'Ya, Pilih Metode'
            }).then((result) => {
                if (result.isConfirmed) {
                    openPaymentModal();
                }
            });
        }

        function openPaymentModal() {
            document.getElementById('paymentModal').classList.remove('hidden');
            document.getElementById('stepSelectMethod').classList.remove('hidden');
            document.getElementById('stepInstruction').classList.add('hidden');
        }

        function closePaymentModal() {
            document.getElementById('paymentModal').classList.add('hidden');
            document.getElementById('modalOrderId').innerText = '';
            clearInterval(timerInterval);
            clearInterval(pollingInterval); // Pastikan polling berhenti saat modal tutup
        }

        async function proceedPayment() {
            const btn = event.target; // Ambil elemen tombol            
            const selectedMethod = document.querySelector('input[name="pay_method"]:checked');
            if (!selectedMethod) return Swal.fire('Oops!', 'Pilih metode pembayaran dulu ya.', 'warning');

            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Memproses...';
            btn.disabled = true;

            try {
                const response = await fetch("{{ route('user.topup.initiate') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        package_id: currentPackageId,
                        payment_method: selectedMethod.value
                    })
                });
                const data = await response.json();
                console.log('data: ', data);

                if (data.status === 'success') showInstruction(data.data);
                else Swal.fire('Error', data.message, 'error');

            } catch (error) {
                Swal.fire('Error', error.message, 'error');
            } finally {
                btn.disabled = false;
                btn.innerText = 'Bayar Sekarang';
            }
        }

        function showInstruction(midtransData) {
            document.getElementById('modalOrderId').innerText = `ORDER-ID: ${midtransData.order_id}`;
            // 1. Pindah Tampilan ke Instruksi
            document.getElementById('stepSelectMethod').classList.add('hidden');
            document.getElementById('stepInstruction').classList.remove('hidden');

            const qrContainer = document.getElementById('qrContainer');
            const paymentType = midtransData.payment_type;

            // 2. Cari link berdasarkan isi data actions dari log kamu
            const deeplink = midtransData.actions.find(a => a.name.includes('deeplink'));
            const qrCode = midtransData.actions.find(a => a.name.includes('qr-code'));

            // 3. Logika Tampilan Dinamis
            if (paymentType === 'qris' && qrCode) {
                // Khusus QRIS: Fokus ke Gambar QR
                qrContainer.innerHTML = `
                    <div class="flex flex-col items-center">
                        <img src="${qrCode.url}" class="w-60 h-60 rounded-2xl border-4 border-gray-100 shadow-md mb-4">
                        <p class="text-[11px] text-gray-400 font-bold uppercase tracking-widest">Scan QRIS untuk membayar</p>
                    </div>
                `;
            } else if (deeplink) {
                // GoPay & ShopeePay: Berikan tombol Deeplink
                qrContainer.innerHTML = `
                    <div class="text-center p-4">
                        <img src="/assets/images/icons/${midtransData.payment_type}.svg" class="mb-6 w-18 animate-bounce aspect-square mx-auto">
                        <p class="text-sm text-gray-600 mb-6 font-medium">Klik tombol di bawah untuk membuka aplikasi atau simulator pembayaran.</p>
                        <a href="${deeplink.url}" target="_blank" class="bg-teal-600 text-white px-8 py-4 rounded-2xl font-bold block shadow-lg hover:bg-teal-700 transition-all transform active:scale-95">
                            Bayar Sekarang
                        </a>
                    </div>
                `;

                // Opsional: Jika user di mobile, bisa langsung diarahkan otomatis
                // window.location.href = deeplink.url;
            } else {
                // Fallback jika tidak ada deeplink tapi ada QR (seperti GoPay di desktop)
                qrContainer.innerHTML = `<img src="${qrCode.url}" class="w-56 h-56 rounded-2xl shadow-md mx-auto">`;
            }

            // 4. Mulai Timer 15 Menit (900 detik)
            startTimer(900);
        }

        function startTimer(duration) {
            let timer = duration,
                minutes, seconds;
            clearInterval(timerInterval);
            timerInterval = setInterval(() => {
                minutes = parseInt(timer / 60, 10);
                seconds = parseInt(timer % 60, 10);
                minutes = minutes < 10 ? "0" + minutes : minutes;
                seconds = seconds < 10 ? "0" + seconds : seconds;

                document.querySelector('#timer span').textContent = minutes + ":" + seconds;
                if (--timer < 0) {
                    clearInterval(timerInterval);
                    Swal.fire('Waktu Habis', 'Sesi pembayaran telah berakhir.', 'warning').then(() => location
                        .reload());
                }
            }, 1000);
        }
    </script>
@endsection
