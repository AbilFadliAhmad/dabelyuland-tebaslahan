 @extends('layouts.user')

 @section('page_title', 'Membership & Tagihan')

 @section('content')
     <div class="font-['Inter'] pb-10">
         {{-- DUMMY DATA (Nanti diganti dari Controller) --}}
         @php
             // Simulasi data user saat ini
             $currentPlan = 'Bronze'; // Bisa 'Bronze', 'Silver', 'Gold'
             $uploadCount = 8;
             $maxQuota = 10;
             $expiredDate = '-';

             // Hitung persentase kuota
             $quotaPercentage = min(100, ($uploadCount / $maxQuota) * 100);
             $progressColor = $quotaPercentage > 80 ? 'bg-rose-500' : 'bg-[#0d9488]';
         @endphp

         {{-- ==========================================================
         1. KARTU STATUS MEMBERSHIP SAAT INI
         ========================================================== --}}
         <div
             class="mb-10 relative overflow-hidden rounded-3xl p-6 sm:p-8 border border-gray-100 shadow-[0_4px_25px_rgba(0,0,0,0.03)]
        {{ $currentPlan == 'Gold'
            ? 'bg-gradient-to-br from-amber-500 to-amber-700 text-white'
            : ($currentPlan == 'Silver'
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
                         class="text-xs font-bold uppercase tracking-wider mb-1 {{ $currentPlan == 'Bronze' ? 'text-gray-500' : 'text-white/80' }}">
                         Status Paket Anda Saat Ini
                     </p>
                     <h3 class="text-3xl sm:text-4xl font-extrabold font-['Plus_Jakarta_Sans'] mb-2 flex items-center gap-3">
                         @if ($currentPlan == 'Gold')
                             <i class="fas fa-crown text-amber-200"></i>
                         @elseif($currentPlan == 'Silver')
                             <i class="fas fa-check-circle text-teal-200"></i>
                         @endif
                         {{ $currentPlan }} {{ $currentPlan == 'Bronze' ? '(Gratis)' : 'Premium' }}
                     </h3>
                     <p class="text-sm m-0 {{ $currentPlan == 'Bronze' ? 'text-gray-500' : 'text-white/90' }}">
                         Masa Aktif: <span class="font-bold">{{ $expiredDate }}</span>
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

         {{-- ==========================================================
         3. PRICING CARDS GRID
         ========================================================== --}}
         <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8 max-w-6xl mx-auto">

             {{-- KARTU 1: BRONZE --}}
             <div
                 class="bg-white rounded-3xl p-6 lg:p-8 border border-gray-200 shadow-sm flex flex-col relative opacity-90 transition-all hover:shadow-md">
                 <div class="mb-6">
                     <span
                         class="inline-block px-3 py-1 bg-gray-100 text-gray-600 text-[10px] font-bold uppercase tracking-wider rounded-lg mb-4">Pemula</span>
                     <h3 class="text-2xl font-extrabold text-gray-900 font-['Plus_Jakarta_Sans'] m-0">Bronze</h3>
                     <div class="mt-4 flex items-baseline text-gray-900">
                         <span class="text-3xl font-extrabold tracking-tight">Gratis</span>
                     </div>
                     <p class="mt-3 text-sm text-gray-500 leading-relaxed">Fasilitas dasar untuk memulai karir agen Anda.
                     </p>
                 </div>

                 <ul class="flex-1 space-y-4 text-sm text-gray-600 mb-8 p-0 m-0">
                     <li class="flex items-start">
                         <i class="fas fa-check text-green-500 mt-1 mr-3 shrink-0"></i>
                         <span class="font-medium">Maksimal upload <b class="text-gray-900">10 Properti</b></span>
                     </li>
                     <li class="flex items-start">
                         <i class="fas fa-times text-gray-300 mt-1 mr-3 shrink-0"></i>
                         <span class="text-gray-400 line-through">Kuota Highlight Beranda</span>
                     </li>
                     <li class="flex items-start">
                         <i class="fas fa-times text-gray-300 mt-1 mr-3 shrink-0"></i>
                         <span class="text-gray-400 line-through">Fitur Generate Banner AI</span>
                     </li>
                 </ul>

                 @if ($currentPlan == 'Bronze')
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
             <div
                 class="bg-white rounded-3xl p-6 lg:p-8 border-2 border-[#0d9488] shadow-[0_15px_30px_rgba(13,148,136,0.15)] flex flex-col relative transform md:-translate-y-4 transition-all">
                 <div class="absolute top-0 left-1/2 -translate-x-1/2 -translate-y-1/2">
                     <span
                         class="bg-[#0d9488] text-white text-[10px] font-extrabold uppercase tracking-widest py-1.5 px-4 rounded-full shadow-md">Paling
                         Laris</span>
                 </div>
                 <div class="mb-6 mt-2">
                     <h3 class="text-2xl font-extrabold text-[#0d9488] font-['Plus_Jakarta_Sans'] m-0">Silver Pro</h3>
                     <div class="mt-4 flex items-baseline text-gray-900">
                         <span class="text-4xl font-extrabold tracking-tight">Rp 49rb</span>
                         <span class="text-sm text-gray-500 ml-1 font-medium">/ bln</span>
                     </div>
                     <p class="mt-3 text-sm text-gray-500 leading-relaxed">Cocok untuk agen yang ingin menjangkau lebih
                         banyak klien.</p>
                 </div>

                 <ul class="flex-1 space-y-4 text-sm text-gray-600 mb-8 p-0 m-0">
                     <li class="flex items-start">
                         <i class="fas fa-check text-[#0d9488] mt-1 mr-3 shrink-0"></i>
                         <span class="font-medium">Maksimal upload <b class="text-gray-900">50 Properti</b></span>
                     </li>
                     <li class="flex items-start">
                         <i class="fas fa-check text-[#0d9488] mt-1 mr-3 shrink-0"></i>
                         <span class="font-medium">Dapat <b class="text-gray-900">2 Kuota</b> Highlight</span>
                     </li>
                     <li class="flex items-start">
                         <i class="fas fa-check text-[#0d9488] mt-1 mr-3 shrink-0"></i>
                         <span class="font-medium">Akses <b class="text-gray-900">Banner AI</b> (3x /hari)</span>
                     </li>
                 </ul>

                 @if ($currentPlan == 'Silver')
                     <button disabled
                         class="w-full py-3 px-4 rounded-xl bg-teal-50 text-teal-700 font-bold text-sm cursor-not-allowed border border-teal-200">
                         Paket Saat Ini
                     </button>
                 @else
                     <button onclick="confirmUpgrade('Silver Pro', 49000)"
                         class="w-full py-3 px-4 rounded-xl bg-[#0d9488] hover:bg-teal-700 text-white font-bold text-sm shadow-lg shadow-teal-500/30 hover:-translate-y-0.5 transition-all">
                         Langganan Silver
                     </button>
                 @endif
             </div>

             {{-- KARTU 3: GOLD (PREMIUM) --}}
             <div
                 class="bg-gradient-to-b from-amber-50 to-white rounded-3xl p-6 lg:p-8 border border-amber-200 shadow-sm hover:shadow-lg flex flex-col relative transition-all duration-300">
                 <div class="mb-6">
                     <span
                         class="inline-flex items-center px-3 py-1 bg-amber-100 text-amber-700 text-[10px] font-bold uppercase tracking-wider rounded-lg mb-4">
                         <i class="fas fa-crown mr-1.5"></i> Eksklusif
                     </span>
                     <h3 class="text-2xl font-extrabold text-amber-600 font-['Plus_Jakarta_Sans'] m-0">Gold Premium</h3>
                     <div class="mt-4 flex items-baseline text-gray-900">
                         <span class="text-3xl font-extrabold tracking-tight">Rp 149rb</span>
                         <span class="text-sm text-gray-500 ml-1 font-medium">/ bln</span>
                     </div>
                     <p class="mt-3 text-sm text-gray-500 leading-relaxed">Akses tanpa batas untuk dominasi pasar properti
                         digital.</p>
                 </div>

                 <ul class="flex-1 space-y-4 text-sm text-gray-600 mb-8 p-0 m-0">
                     <li class="flex items-start">
                         <i class="fas fa-check text-amber-500 mt-1 mr-3 shrink-0"></i>
                         <span class="font-medium">Upload Properti <b class="text-gray-900">Tanpa Batas</b></span>
                     </li>
                     <li class="flex items-start">
                         <i class="fas fa-check text-amber-500 mt-1 mr-3 shrink-0"></i>
                         <span class="font-medium">Dapat <b class="text-gray-900">10 Kuota</b> Highlight</span>
                     </li>
                     <li class="flex items-start">
                         <i class="fas fa-check text-amber-500 mt-1 mr-3 shrink-0"></i>
                         <span class="font-medium">Akses <b class="text-gray-900">Banner AI</b> Unlimited</span>
                     </li>
                     <li class="flex items-start">
                         <i class="fas fa-check text-amber-500 mt-1 mr-3 shrink-0"></i>
                         <span class="font-medium">Prioritas Pencarian Teratas</span>
                     </li>
                 </ul>

                 @if ($currentPlan == 'Gold')
                     <button disabled
                         class="w-full py-3 px-4 rounded-xl bg-amber-50 text-amber-700 font-bold text-sm cursor-not-allowed border border-amber-200">
                         Paket Saat Ini
                     </button>
                 @else
                     <button onclick="confirmUpgrade('Gold Premium', 149000)"
                         class="w-full py-3 px-4 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-sm shadow-lg shadow-amber-500/30 hover:-translate-y-0.5 transition-all">
                         Langganan Gold
                     </button>
                 @endif
             </div>

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

     </div>
 @endsection

 @section('script')
     <script>
         // Format Rupiah
         const formatRupiah = (number) => {
             return new Intl.NumberFormat('id-ID', {
                 style: 'currency',
                 currency: 'IDR',
                 minimumFractionDigits: 0
             }).format(number);
         };

         // Fungsi Simulasi Klik Tombol Upgrade
         function confirmUpgrade(packageName, price) {
             Swal.fire({
                 title: 'Upgrade ke ' + packageName + '?',
                 html: `Anda akan diarahkan ke halaman pembayaran.<br><br>Total Tagihan: <b>${formatRupiah(price)}</b> / bulan`,
                 icon: 'question',
                 showCancelButton: true,
                 confirmButtonColor: '#0d9488',
                 cancelButtonColor: '#f3f4f6',
                 cancelButtonText: '<span class="text-gray-700">Batal</span>',
                 confirmButtonText: 'Ya, Lanjutkan Pembayaran',
                 customClass: {
                     popup: 'rounded-3xl font-sans',
                     confirmButton: 'rounded-xl font-bold px-6',
                     cancelButton: 'rounded-xl font-bold px-6'
                 }
             }).then((result) => {
                 if (result.isConfirmed) {
                     // Di sistem nyata, arahkan ke Route Checkout / Payment Gateway
                     // window.location.href = "/user/checkout?paket=" + packageName;

                     Swal.fire({
                         title: 'Memproses...',
                         text: 'Menghubungkan ke gerbang pembayaran aman.',
                         icon: 'info',
                         showConfirmButton: false,
                         timer: 2000
                     }).then(() => {
                         Swal.fire({
                             title: 'Simulasi Berhasil!',
                             text: 'Ini adalah tampilan contoh. Integrasi pembayaran akan ditambahkan nanti.',
                             icon: 'success',
                             confirmButtonColor: '#0d9488',
                             customClass: {
                                 confirmButton: 'rounded-xl font-bold'
                             }
                         });
                     });
                 }
             });
         }
     </script>
 @endsection
