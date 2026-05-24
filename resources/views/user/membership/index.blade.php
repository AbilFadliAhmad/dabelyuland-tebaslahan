 @extends('layouts.user')

 @section('page_title', 'Membership & Tagihan')

 @section('content')
     <div class="font-['Inter'] pb-10">
         {{-- DUMMY DATA (Nanti diganti dari Controller) --}}
         @php
             use Carbon\Carbon;
             $membershipId = $currentMembership->membership_id;
             $membershipName = $currentMembership->membership->name;

             $expiredDate = Carbon::parse($currentMembership->expired_at);
             $sisaHari = Carbon::now()->diffInDays($expiredDate, false);
         @endphp

         {{-- ==========================================================
         1. KARTU STATUS MEMBERSHIP SAAT INI
         ========================================================== --}}
         <div
             class="mb-10 relative overflow-hidden rounded-3xl p-6 sm:p-8 border border-gray-100 shadow-[0_4px_25px_rgba(0,0,0,0.03)]
        {{ $membershipId == 3
            ? 'bg-gradient-to-br from-amber-500 to-amber-700 text-white'
            : ($membershipId == 2
                ? 'bg-gradient-to-br from-[#0d9488] to-teal-700 text-white'
                : 'bg-white text-gray-800') }}">

             {{-- Dekorasi Latar Belakang --}}
             <div class="absolute -right-10 -top-10 text-white/10 opacity-20 transform rotate-12 pointer-events-none">
                 <i class="fas fa-gem text-9xl"></i>
             </div>

             <div class="relative z-10 flex flex-col md:flex-row justify-between gap-6 md:items-center">
                 {{-- Info Kiri --}}
                 <div>
                     <p
                         class="text-xs font-bold uppercase tracking-wider mb-1 {{ $membershipId == 1 ? 'text-gray-500' : 'text-white/80' }}">
                         Status Paket Anda Saat Ini
                     </p>
                     <h3 class="text-3xl sm:text-4xl font-extrabold font-['Plus_Jakarta_Sans'] mb-2 flex items-center gap-3">
                         @if ($membershipId == 3)
                             <i class="fas fa-crown text-amber-200"></i>
                         @elseif($membershipId == 2)
                             <i class="fas fa-check-circle text-teal-200"></i>
                         @endif
                         {{ $membershipName }} {{ $membershipId == 1 ? '(Gratis)' : 'Premium' }}
                     </h3>
                     <p class="text-sm m-0 {{ $membershipId == 1 ? 'text-gray-500' : 'text-white/90' }}">
                         Masa Aktif: <span class="font-bold">{{ $membershipId == 1 ? '99' : $sisaHari }} Hari lagi</span>
                     </p>
                 </div>

             </div>
         </div>

         {{-- ==========================================================
         2. HEADER PENAWARAN UPGRADE
         ========================================================== --}}
         <div class="text-center mb-8 max-w-2xl mx-auto">
             <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 m-0 font-['Plus_Jakarta_Sans'] tracking-tight">
                 Pilih Paket yang Sesuai untuk Anda
             </h2>
             <p class="text-sm text-gray-500 mt-3 leading-relaxed">
                 Tingkatkan visibilitas properti Anda dan dapatkan lebih banyak calon pembeli dengan fitur premium
                 Dabelyuland.
             </p>
         </div>

         {{-- BONUS: PENDING TRANSACTION NOTIFICATION CARD --}}
         <div id="pendingTransactionContainer" class="max-w-6xl mx-auto">
             @if (isset($midtransDetail))
                 <div
                     class="mb-12 p-6 sm:p-8 bg-amber-50/60 border-2 border-amber-200 rounded-[2rem] flex flex-col md:flex-row items-center justify-between gap-6 shadow-sm backdrop-blur-sm">
                     <div class="flex items-center gap-5 text-center md:text-left flex-col md:flex-row">
                         <div
                             class="w-14 h-14 bg-amber-100 text-amber-600 rounded-2xl flex items-center justify-center text-2xl shrink-0 shadow-inner">
                             <i class="fas fa-hourglass-half animate-spin" style="animation-duration: 3s;"></i>
                         </div>
                         <div>
                             <h4 class="font-['Plus_Jakarta_Sans'] text-lg font-bold text-gray-900 mb-1">Pembayaran Menunggu
                                 Penyelesaian</h4>
                             <p class="text-xs sm:text-sm text-gray-600 leading-relaxed">
                                 Transaksi <span class="font-bold text-amber-700">{{ $midtransDetail->order_id }}</span>
                                 untuk paket
                                 <span
                                     class="font-bold text-gray-800">{{ $midtransDetail->metadata->membership_name ?? 'Premium' }}</span>
                                 sebesar <span class="font-bold text-teal-600">Rp
                                     {{ number_format($midtransDetail->gross_amount, 0, ',', '.') }}</span> via
                                 <span class="font-bold uppercase text-gray-700">{{ $midtransDetail->payment_type }}</span>.
                             </p>
                         </div>
                     </div>

                     <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                         <button type="button" onclick="cancelPendingPayment('{{ $midtransDetail->order_id }}')"
                             class="bg-white border-2 border-red-300 hover:bg-red-50 text-red-600 px-6 py-3.5 rounded-xl font-extrabold text-sm transition-all shrink-0">
                             Batalkan
                         </button>
                         <button type="button" onclick="resumePendingPayment()"
                             class="btn-shine relative overflow-hidden bg-gray-900 hover:bg-gray-600 text-white px-6 py-3.5 rounded-xl font-extrabold text-sm shadow-md transition-all shrink-0 transform active:scale-95">
                             Selesaikan Pembayaran
                         </button>
                     </div>
                 </div>
             @endif
         </div>

         {{-- ==========================================================
         3. PRICING CARDS GRID
         ========================================================== --}}
         <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8 max-w-6xl mx-auto">
             @foreach ($Memberships as $pkg)
                 @php
                     $isCurrent = strtolower($membershipName) == strtolower($pkg->name);
                     $name = strtolower($pkg->name);
                 @endphp

                 {{-- KARTU 1: BRONZE --}}
                 @if (str_contains($name, 'bronze'))
                     <div
                         class="bg-white rounded-3xl p-6 lg:p-8 border border-gray-200 shadow-sm flex flex-col relative opacity-90 transition-all hover:shadow-md">
                         <div class="mb-6">
                             @if ($pkg->badge_name)
                                 <span
                                     class="inline-block px-3 py-1 bg-gray-100 text-gray-600 text-[10px] font-bold uppercase tracking-wider rounded-lg mb-4">{{ $pkg->badge_name }}</span>
                             @endif
                             <h3 class="text-2xl font-extrabold text-gray-900 font-['Plus_Jakarta_Sans'] m-0">
                                 {{ $pkg->name }}</h3>
                             <div class="mt-4 flex items-baseline text-gray-900">
                                 <span class="text-3xl font-extrabold tracking-tight">Gratis</span>
                             </div>
                             <p class="mt-3 text-sm text-gray-500 leading-relaxed">{{ $pkg->description }}</p>
                         </div>

                         <ul class="flex-1 space-y-4 text-sm text-gray-600 mb-8 p-0 m-0">
                             <li class="flex items-start">
                                 <i class="fas fa-check text-green-500 mt-1 mr-3 shrink-0"></i>
                                 <span class="font-medium">Push Properti <b
                                         class="text-gray-900">{{ $pkg->push_quota }}x</b></span>
                             </li>
                             <li class="flex items-start">
                                 <i class="fas fa-check text-green-500 mt-1 mr-3 shrink-0"></i>
                                 <span class="font-medium">Rekomendasi <b
                                         class="text-gray-900">{{ $pkg->recommendation_quota }} Kuota</b></span>
                             </li>
                             <li class="flex items-start">
                                 <i class="fas fa-times text-gray-300 mt-1 mr-3 shrink-0"></i>
                                 <span class="text-gray-400 line-through">Kuota Highlight Beranda</span>
                             </li>
                             <li class="flex items-start">
                                 <i class="fas fa-times text-gray-300 mt-1 mr-3 shrink-0"></i>
                                 <span class="text-gray-400 line-through">Fitur Banner</span>
                             </li>
                         </ul>

                         @if ($isCurrent)
                             <button disabled
                                 class="w-full py-3 px-4 rounded-xl bg-gray-100 text-gray-400 font-bold text-sm cursor-not-allowed border border-gray-200">
                                 Paket Saat Ini
                             </button>
                         @else
                             <button disabled
                                 class="w-full py-3 px-4 rounded-xl bg-gray-50 text-gray-400 font-bold text-sm cursor-not-allowed">
                                 Hanya untuk akun baru
                             </button>
                         @endif
                     </div>

                     {{-- KARTU 2: SILVER (POPULER) --}}
                 @elseif(str_contains($name, 'silver'))
                     <div
                         class="bg-white rounded-3xl p-6 lg:p-8 border-2 border-[#0d9488] shadow-[0_15px_30px_rgba(13,148,136,0.15)] flex flex-col relative transform md:-translate-y-4 transition-all">
                         @if ($pkg->badge_name)
                             <div class="absolute top-0 left-1/2 -translate-x-1/2 -translate-y-1/2">
                                 <span
                                     class="bg-[#0d9488] text-white text-[10px] font-extrabold uppercase tracking-widest py-1.5 px-4 rounded-full shadow-md">{{ $pkg->badge_name }}</span>
                             </div>
                         @endif
                         <div class="mb-6 mt-2">
                             <h3 class="text-2xl font-extrabold text-[#0d9488] font-['Plus_Jakarta_Sans'] m-0">
                                 {{ $pkg->name }}</h3>
                             <div class="mt-4 flex items-baseline text-gray-900">
                                 <span class="text-4xl font-extrabold tracking-tight">Rp
                                     {{ number_format($pkg->price, 0, ',', '.') }}</span>
                                 <span class="text-sm text-gray-500 ml-1 font-medium">/ {{ $pkg->duration_days }} hr</span>
                             </div>
                             <p class="mt-3 text-sm text-gray-500 leading-relaxed">{{ $pkg->description }}</p>
                         </div>

                         <ul class="flex-1 space-y-4 text-sm text-gray-600 mb-8 p-0 m-0">
                             <li class="flex items-start">
                                 <i class="fas fa-check text-[#0d9488] mt-1 mr-3 shrink-0"></i>
                                 <span class="font-medium">Push Properti <b
                                         class="text-gray-900">{{ $pkg->push_quota }}x</b></span>
                             </li>
                             <li class="flex items-start">
                                 <i class="fas fa-check text-[#0d9488] mt-1 mr-3 shrink-0"></i>
                                 <span class="font-medium">Rekomendasi <b
                                         class="text-gray-900">{{ $pkg->recommendation_quota }} Kuota</b></span>
                             </li>
                             <li class="flex items-start">
                                 <i class="fas fa-check text-[#0d9488] mt-1 mr-3 shrink-0"></i>
                                 <span class="font-medium">Dapat <b class="text-gray-900">{{ $pkg->highlight_quota }}
                                         Kuota</b> Highlight</span>
                             </li>
                             <li class="flex items-start">
                                 <i class="fas fa-times text-gray-300 mt-1 mr-3 shrink-0"></i>
                                 <span class="text-gray-400 line-through">Fitur Banner</span>
                             </li>
                         </ul>

                         @if ($isCurrent || $membershipId > 2)
                             <button disabled
                                 class="w-full py-3 px-4 rounded-xl bg-teal-50 text-teal-700 font-bold text-sm cursor-not-allowed border border-teal-200">
                                 {{ $membershipId > 2 ? 'Tidak Tersedia' : 'Langganan Silver' }}
                             </button>
                         @else
                             {{-- Perbaikan: mengirim 3 parameter sesuai JS (ID, Nama, Harga) --}}
                             <button type="button"
                                 onclick="confirmUpgrade({{ $pkg->id }}, '{{ $pkg->name }}', {{ $pkg->price }})"
                                 class="buy-pkg-btn w-full py-3 px-4 rounded-xl bg-[#0d9488] hover:bg-teal-700 text-white font-bold text-sm shadow-lg shadow-teal-500/30 hover:-translate-y-0.5 transition-all">
                                 Langganan Silver
                             </button>
                         @endif
                     </div>

                     {{-- KARTU 3: GOLD (PREMIUM) --}}
                 @elseif(str_contains($name, 'gold'))
                     <div
                         class="bg-gradient-to-b from-amber-50 to-white rounded-3xl p-6 lg:p-8 border border-amber-200 shadow-sm hover:shadow-lg flex flex-col relative transition-all duration-300">
                         <div class="mb-6">
                             @if ($pkg->badge_name)
                                 <span
                                     class="inline-flex items-center px-3 py-1 bg-amber-100 text-amber-700 text-[10px] font-bold uppercase tracking-wider rounded-lg mb-4">
                                     <i class="fas fa-crown mr-1.5"></i> {{ $pkg->badge_name }}
                                 </span>
                             @endif
                             <h3 class="text-2xl font-extrabold text-amber-600 font-['Plus_Jakarta_Sans'] m-0">
                                 {{ $pkg->name }}</h3>
                             <div class="mt-4 flex items-baseline text-gray-900">
                                 <span class="text-3xl font-extrabold tracking-tight">Rp
                                     {{ number_format($pkg->price, 0, ',', '.') }}</span>
                                 <span class="text-sm text-gray-500 ml-1 font-medium">/ {{ $pkg->duration_days }} hr</span>
                             </div>
                             <p class="mt-3 text-sm text-gray-500 leading-relaxed">{{ $pkg->description }}</p>
                         </div>

                         <ul class="flex-1 space-y-4 text-sm text-gray-600 mb-8 p-0 m-0">
                             <li class="flex items-start">
                                 <i class="fas fa-check text-amber-500 mt-1 mr-3 shrink-0"></i>
                                 <span class="font-medium">Push Properti <b
                                         class="text-gray-900">{{ $pkg->push_quota }}x</b></span>
                             </li>
                             <li class="flex items-start">
                                 <i class="fas fa-check text-amber-500 mt-1 mr-3 shrink-0"></i>
                                 <span class="font-medium">Rekomendasi <b
                                         class="text-gray-900">{{ $pkg->recommendation_quota }} Kuota</b></span>
                             </li>
                             <li class="flex items-start">
                                 <i class="fas fa-check text-amber-500 mt-1 mr-3 shrink-0"></i>
                                 <span class="font-medium">Dapat <b class="text-gray-900">{{ $pkg->highlight_quota }}
                                         Kuota</b> Highlight</span>
                             </li>
                             <li class="flex items-start">
                                 <i class="fas fa-check text-amber-500 mt-1 mr-3 shrink-0"></i>
                                 <span class="font-medium">Dapat <b class="text-gray-900">{{ $pkg->banner_quota }}
                                         Kuota</b> Banner</span>
                             </li>
                         </ul>

                         @if ($isCurrent)
                             <button disabled
                                 class="w-full py-3 px-4 rounded-xl bg-amber-50 text-amber-700 font-bold text-sm cursor-not-allowed border border-amber-200">
                                 Paket Saat Ini
                             </button>
                         @else
                             {{-- Perbaikan: mengirim 3 parameter sesuai JS (ID, Nama, Harga) --}}
                             <button type="button"
                                 onclick="confirmUpgrade({{ $pkg->id }}, '{{ $pkg->name }}', {{ $pkg->price }})"
                                 class="buy-pkg-btn w-full py-3 px-4 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-sm shadow-lg shadow-amber-500/30 hover:-translate-y-0.5 transition-all">
                                 Langganan Gold
                             </button>
                         @endif
                     </div>
                 @endif
             @endforeach
         </div>

         {{-- ==========================================================
         4. FAQ / BANTUAN
         ========================================================== --}}
         <div
             class="mt-16 bg-white rounded-3xl p-6 md:p-8 border border-gray-100 shadow-sm max-w-4xl mx-auto flex flex-col md:flex-row gap-6 items-center">
             <div
                 class="w-16 h-16 shrink-0 bg-blue-50 text-blue-500 rounded-full flex items-center justify-center text-2xl">
                 <i class="fas fa-headset"></i>
             </div>
             <div>
                 <h4 class="text-lg font-bold text-gray-900 mb-1 font-['Plus_Jakarta_Sans']">Butuh Bantuan Memilih Paket?
                 </h4>
                 <p class="text-sm text-gray-500 m-0 leading-relaxed">Tim dukungan kami siap membantu Anda menjelaskan
                     detail fitur dan proses pembayaran. Silakan hubungi admin kami melalui WhatsApp.</p>
             </div>
             <a href="#"
                 class="w-full md:w-auto shrink-0 px-6 py-3 bg-[#25D366] hover:bg-[#20bd5a] text-white font-bold rounded-xl transition-colors shadow-sm flex items-center justify-center gap-2 no-underline">
                 <i class="fab fa-whatsapp text-lg"></i> Chat Admin
             </a>
         </div>

         <div id="paymentModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4 sm:p-6">
             <div onclick="closePaymentModal()" class="fixed inset-0 transition-opacity bg-gray-900/60"></div>
             <div
                 class="relative z-10 w-full max-w-md bg-white shadow-2xl rounded-[2.5rem] border border-gray-300 transform transition-all flex flex-col max-h-[90vh]">
                 <div class="relative p-8 pb-0 shrink-0">
                     <div class="flex items-center justify-between mb-6">
                         <img src="/frontside/img/logo-dabelyuland.png" class="h-8" alt="Logo">
                         <button onclick="closePaymentModal()"
                             class="text-gray-400 hover:text-gray-600 transition-colors"><i
                                 class="fas fa-times text-xl"></i></button>
                     </div>
                     <div class="text-center p-6 bg-teal-50 rounded-3xl border-2 border-teal-300/50">
                         <p class="text-xs font-bold text-teal-600 uppercase tracking-widest mb-1">Total Pembayaran</p>
                         <span id="modalOrderId"
                             class="text-[10px] text-gray-600 font-semibold uppercase tracking-wide mb-1"></span>
                         <h2 id="modalAmount" class="text-3xl font-extrabold text-gray-900 font-['Plus_Jakarta_Sans']">Rp
                             0</h2>
                     </div>
                 </div>

                 <div id="modalBody" class="p-8 overflow-y-auto custom-scrollbar">
                     <div id="stepSelectMethod">
                         <h3 class="text-sm font-bold text-gray-800 mb-4">Pilih Dompet Digital</h3>
                         <div class="space-y-3">
                             @foreach (['qris' => 'QRIS (Semua E-Wallet)', 'gopay' => 'GoPay', 'shopeepay' => 'ShopeePay'] as $code => $label)
                                 <label
                                     class="group relative flex items-center p-4 border-2 border-gray-300 rounded-2xl cursor-pointer hover:border-teal-500 hover:bg-teal-50/30 transition-all">
                                     <input type="radio" name="pay_method" value="{{ $code }}"
                                         class="hidden peer">
                                     <div
                                         class="w-10 h-10 flex items-center justify-center bg-white rounded-xl shadow-sm border border-gray-300 group-hover:scale-110 transition-transform">
                                         <img src="/assets/images/icons/{{ $code }}.svg"
                                             class="w-6 h-6 object-contain" alt="{{ $label }}">
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
                             class="w-full mt-8 py-4 bg-gray-900 text-white rounded-2xl font-bold text-sm shadow-xl hover:bg-teal-600 transition-all transform active:scale-95">Bayar
                             Sekarang</button>
                     </div>

                     <div id="stepInstruction" class="hidden text-center">
                         <div class="mb-6">
                             <div id="timer"
                                 class="inline-flex items-center gap-2 px-4 py-2 bg-red-50 text-red-600 rounded-full text-xs font-bold animate-pulse">
                                 <i class="far fa-clock"></i> <span>15:00</span>
                             </div>
                         </div>
                         <div id="qrContainer"
                             class="bg-white p-4 border-2 border-teal-300 rounded-3xl inline-block shadow-inner mb-6">
                         </div>
                     </div>
                 </div>
             </div>
         </div>
     @endsection

     @section('script')
         <script>
             let currentPackageId = null;
             let timerInterval = null;
             let pollingInterval = null;

             let activeMidtransData = @json($midtransDetail ?? null);

             document.addEventListener('DOMContentLoaded', () => {
                 if (activeMidtransData) disablePackageButtons();
             });

             // Format Rupiah
             const formatRupiah = (number) => {
                 return new Intl.NumberFormat('id-ID', {
                     style: 'currency',
                     currency: 'IDR',
                     minimumFractionDigits: 0
                 }).format(number);
             };

             function resumePendingPayment() {
                 if (activeMidtransData) {
                     document.getElementById('modalAmount').innerText =
                         `Rp ${new Intl.NumberFormat('id-ID').format(activeMidtransData.gross_amount)}`;
                     document.getElementById('paymentModal').classList.remove('hidden');
                     showInstruction(activeMidtransData);
                 }
             }

             function confirmUpgrade(packageId, packageName, price) {
                 currentPackageId = packageId;
                 document.getElementById('modalAmount').innerText = `Rp ${new Intl.NumberFormat('id-ID').format(price)}`;

                 Swal.fire({
                     title: `Upgrade ke ${packageName}?`,
                     html: `Anda akan diarahkan ke halaman pembayaran.<br><br>Total Tagihan: <b>Rp ${new Intl.NumberFormat('id-ID').format(price)}</b> / bulan`,
                     icon: 'question',
                     showCancelButton: true,
                     confirmButtonColor: '#0d9488',
                     confirmButtonText: 'Ya, Pilih Metode',
                     customClass: {
                         popup: 'rounded-3xl',
                         confirmButton: 'rounded-xl font-bold',
                         cancelButton: 'rounded-xl font-bold'
                     }
                 }).then((result) => {
                     if (result.isConfirmed) openPaymentModal();
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
                 clearInterval(pollingInterval);
             }

             async function proceedPayment() {
                 const btn = event.target;
                 const selectedMethod = document.querySelector('input[name="pay_method"]:checked');
                 if (!selectedMethod) return Swal.fire('Oops!', 'Pilih metode pembayaran dulu ya.', 'warning');

                 btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Memproses...';
                 btn.disabled = true;

                 try {
                     const response = await axios.post("{{ route('user.membership.initiate') }}", {
                         package_id: currentPackageId,
                         payment_method: selectedMethod.value
                     }, {
                         headers: {
                             "Content-Type": "application/json",
                             "X-CSRF-TOKEN": "{{ csrf_token() }}"
                         }
                     });

                     const data = response.data;
                     if (data.status === 'success') {
                         activeMidtransData = data.data;
                         const planName = data.data.metadata?.plan_name || 'Premium';

                         injectPendingBonusCard(data.data.order_id, planName, data.data.gross_amount, data.data
                             .payment_type);
                         disablePackageButtons();
                         showInstruction(data.data);
                     } else {
                         Swal.fire('Error', data.message, 'error');
                     }
                 } catch (error) {
                     const errorMsg = error.response?.data?.message || error.message;
                     Swal.fire('Error', errorMsg, 'error');
                 } finally {
                     btn.disabled = false;
                     btn.innerText = 'Bayar Sekarang';
                 }
             }

             // Fungsi Membatalkan Transaksi Pending
             async function cancelPendingPayment(orderId) {
                 Swal.fire({
                     title: 'Batalkan Transaksi?',
                     text: "Anda dapat memesan ulang paket lain setelah membatalkan tagihan ini.",
                     icon: 'warning',
                     showCancelButton: true,
                     confirmButtonColor: '#ef4444',
                     cancelButtonColor: '#9ca3af',
                     confirmButtonText: 'Ya, Batalkan!',
                     cancelButtonText: 'Kembali',
                     customClass: {
                         popup: 'rounded-3xl',
                         confirmButton: 'rounded-xl font-bold',
                         cancelButton: 'rounded-xl font-bold'
                     }
                 }).then(async (result) => {
                     if (result.isConfirmed) {
                         try {
                             const response = await axios.post("{{ route('user.membership.cancel') }}", {
                                 order_id: orderId
                             }, {
                                 headers: {
                                     "X-CSRF-TOKEN": "{{ csrf_token() }}"
                                 }
                             });

                             if (response.data.status === 'success') {
                                 Swal.fire({
                                     title: 'Dibatalkan!',
                                     text: 'Tagihan telah berhasil dibatalkan.',
                                     icon: 'success'
                                 }).then(() => location.reload());
                             }
                         } catch (error) {
                             Swal.fire('Error', error.response?.data?.message || 'Gagal membatalkan transaksi.',
                                 'error');
                         }
                     }
                 });
             }

             function injectPendingBonusCard(orderId, planName, grossAmount, paymentType) {
                 const container = document.getElementById('pendingTransactionContainer');
                 if (!container) return;

                 const formattedPrice = new Intl.NumberFormat('id-ID').format(grossAmount);
                 container.innerHTML = `
                <div class="mb-12 p-6 sm:p-8 bg-amber-50/60 border-2 border-amber-200 rounded-[2rem] flex flex-col md:flex-row items-center justify-between gap-6 shadow-sm backdrop-blur-sm">
                    <div class="flex items-center gap-5 text-center md:text-left flex-col md:flex-row">
                        <div class="w-14 h-14 bg-amber-100 text-amber-600 rounded-2xl flex items-center justify-center text-2xl shrink-0 shadow-inner">
                            <i class="fas fa-hourglass-half animate-spin" style="animation-duration: 3s;"></i>
                        </div>
                        <div>
                            <h4 class="font-['Plus_Jakarta_Sans'] text-lg font-bold text-gray-900 mb-1">Pembayaran Menunggu Penyelesaian</h4>
                            <p class="text-xs sm:text-sm text-gray-600 leading-relaxed">
                                Transaksi <span class="font-bold text-amber-700">${orderId}</span> untuk paket
                                <span class="font-bold text-gray-800">${planName}</span> sebesar
                                <span class="font-bold text-teal-600">Rp ${formattedPrice}</span> via
                                <span class="font-bold uppercase text-gray-700">${paymentType}</span>.
                            </p>
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                         <button type="button" onclick="cancelPendingPayment('${orderId}')"
                             class="bg-white border-2 border-red-300 hover:bg-red-50 text-red-600 px-6 py-3.5 rounded-xl font-extrabold text-sm transition-all shrink-0">
                             Batalkan
                         </button>
                         <button type="button" onclick="resumePendingPayment()"
                             class="btn-shine relative overflow-hidden bg-gray-900 hover:bg-gray-600 text-white px-6 py-3.5 rounded-xl font-extrabold text-sm shadow-md transition-all shrink-0 transform active:scale-95">
                             Selesaikan Pembayaran
                         </button>
                    </div>
                </div>
            `;
             }

             function disablePackageButtons() {
                 document.querySelectorAll('.buy-pkg-btn').forEach(btn => {
                     btn.disabled = true;
                     btn.className =
                         "w-full py-3 px-4 rounded-xl font-bold text-sm bg-gray-100 text-gray-400 cursor-not-allowed border border-gray-200";
                     btn.setAttribute('onclick',
                         "Swal.fire('Perhatian', 'Selesaikan atau batalkan tagihan pending Anda terlebih dahulu.', 'warning')"
                     );
                 });
             }

             function showInstruction(midtransData) {
                 document.getElementById('modalOrderId').innerText = `ORDER-ID: ${midtransData.order_id}`;
                 document.getElementById('stepSelectMethod').classList.add('hidden');
                 document.getElementById('stepInstruction').classList.remove('hidden');

                 const qrContainer = document.getElementById('qrContainer');
                 const paymentType = midtransData.payment_type;
                 const actions = midtransData.actions || [];

                 const deeplink = actions.find(a => a.name.includes('deeplink'));
                 const qrCode = actions.find(a => a.name.includes('qr-code'));

                 if (paymentType === 'qris' && qrCode) {
                     qrContainer.innerHTML = `
                    <div class="flex flex-col items-center">
                        <img src="${qrCode.url}" class="w-60 h-60 rounded-2xl border-4 border-gray-100 shadow-md mb-4">
                        <p class="text-[11px] text-gray-400 font-bold uppercase tracking-widest">Scan QRIS untuk membayar</p>
                    </div>`;
                 } else if (deeplink) {
                     qrContainer.innerHTML = `
                    <div class="text-center p-4">
                        <img src="/assets/images/icons/${midtransData.payment_type}.svg" class="mb-6 w-18 animate-bounce aspect-square mx-auto">
                        <p class="text-sm text-gray-600 mb-6 font-medium">Klik tombol di bawah untuk membuka aplikasi E-Wallet.</p>
                        <a href="${deeplink.url}" target="_blank" class="bg-teal-600 text-white px-8 py-4 rounded-2xl font-bold block shadow-lg hover:bg-teal-700 transition-all transform active:scale-95">
                            Bayar Sekarang
                        </a>
                    </div>`;
                 } else if (qrCode) {
                     qrContainer.innerHTML = `<img src="${qrCode.url}" class="w-56 h-56 rounded-2xl shadow-md mx-auto">`;
                 }

                 let durationSeconds = 900;
                 if (midtransData.expiry_time) {
                     const expiry = new Date(midtransData.expiry_time).getTime();
                     const now = new Date().getTime();
                     const diff = Math.floor((expiry - now) / 1000);
                     if (diff > 0) durationSeconds = diff;
                 }

                 startTimer(durationSeconds);
                 startPolling(midtransData.order_id);
             }

             function startPolling(orderId) {
                 if (pollingInterval) clearInterval(pollingInterval);

                 async function checkStatus() {
                     try {
                         let url = `{{ route('user.membership.status', 'orderId') }}`.replace('orderId', orderId);
                         const response = await axios.get(url);
                         const data = response.data;

                         if (data.status === 'settlement') {
                             clearInterval(pollingInterval);
                             clearInterval(timerInterval);
                             Swal.fire({
                                 title: 'Upgrade Berhasil!',
                                 text: 'Status Membership Anda telah diperbarui.',
                                 icon: 'success',
                                 confirmButtonColor: '#0d9488'
                             }).then(() => location.reload());
                         } else if (['expire', 'cancel', 'failure'].includes(data.status)) {
                             clearInterval(pollingInterval);
                             Swal.fire('Gagal', 'Transaksi berakhir atau dibatalkan.', 'error').then(() => location
                                 .reload());
                         }
                     } catch (e) {
                         console.error("Polling error:", e);
                     }
                 }

                 checkStatus();
                 pollingInterval = setInterval(checkStatus, 5000);
             }

             function startTimer(duration) {
                 let timer = duration,
                     minutes, seconds;
                 clearInterval(timerInterval);

                 function updateTimer() {
                     minutes = parseInt(timer / 60, 10);
                     seconds = parseInt(timer % 60, 10);
                     minutes = minutes < 10 ? "0" + minutes : minutes;
                     seconds = seconds < 10 ? "0" + seconds : seconds;

                     document.querySelector('#timer span').textContent = minutes + ":" + seconds;
                     if (--timer < 0) {
                         clearInterval(timerInterval);
                         clearInterval(pollingInterval);
                         Swal.fire('Waktu Habis', 'Sesi pembayaran telah berakhir.', 'warning').then(() => location.reload());
                     }
                 }

                 updateTimer();
                 timerInterval = setInterval(updateTimer, 1000);
             }
         </script>
     @endsection
