<footer class=" w-full bg-[#111827] text-gray-400 pt-16 pb-6 mt-auto font-['Inter']">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12 mb-12">

            {{-- Brand Info --}}
            <div class="col-span-1">
                <h5 class="text-white text-xl font-bold mb-6 flex items-center gap-3 font-['Plus_Jakarta_Sans']">
                    <div class="bg-white p-1.5 rounded-lg">
                        <img src="{{ asset('frontside/img/icon/logo-green.svg') }}" alt="Logo" class="w-8 h-8" />
                    </div>
                    #tebaslahan
                </h5>
                <p class="leading-relaxed text-sm">
                    #tebaslahan adalah platform terpercaya untuk jual beli dan sewa properti. Kami mendampingi
                    Anda
                    menemukan hunian impian dengan proses yang transparan, aman, dan mudah.
                </p>
            </div>

            {{-- Quick Links --}}
            <div class="col-span-1">
                <h6 class="text-white text-lg font-bold mb-6 font-['Plus_Jakarta_Sans']">Tautan Cepat</h6>
                <div class="flex flex-col gap-3">
                    <a href="{{ url('/') }}"
                        class="text-gray-400 hover:text-white transition-colors no-underline text-sm flex items-center"><i
                            class="fas fa-chevron-right text-[10px] mr-2 text-[#0d9488]"></i> Beranda</a>
                    <a href="{{ url('shop') }}"
                        class="text-gray-400 hover:text-white transition-colors no-underline text-sm flex items-center"><i
                            class="fas fa-chevron-right text-[10px] mr-2 text-[#0d9488]"></i> Semua
                        Kategori</a>
                    <a href="{{ url('portfolio') }}"
                        class="text-gray-400 hover:text-white transition-colors no-underline text-sm flex items-center"><i
                            class="fas fa-chevron-right text-[10px] mr-2 text-[#0d9488]"></i> Portofolio
                        Kami</a>
                </div>
            </div>

            {{-- Contact Info --}}
            <div class="col-span-1">
                <h6 class="text-white text-lg font-bold mb-6 font-['Plus_Jakarta_Sans']">Hubungi Kami</h6>
                <p class="text-white font-semibold text-sm mb-2">Kantor Pusat Dabelyuland:</p>
                <div class="flex items-start gap-3 mb-4 text-sm">
                    <i class="fas fa-map-marker-alt text-[#0d9488] mt-1"></i>
                    <span>Tunggorono, Kecamatan Jombang,<br>Kabupaten Jombang, Jawa Timur 61419</span>
                </div>
                <div class="flex items-center gap-3 mb-3 text-sm">
                    <i class="fas fa-envelope text-[#0d9488]"></i>
                    <a href="mailto:info@dabelyuland.com"
                        class="text-gray-400 hover:text-white no-underline">info@dabelyuland.com</a>
                </div>
                <div class="flex items-center gap-3 text-sm">
                    <i class="fas fa-phone-alt text-[#0d9488]"></i>
                    <span>0821-2727-7747</span>
                </div>
            </div>
        </div>

        {{-- Copyright --}}
        <div class="pt-6 border-t border-gray-800 text-center">
            <p class="text-xs text-gray-500 m-0">
                &copy; {{ date('Y') }} <strong class="text-white">#tebaslahan</strong> by dabelyuland. All
                rights reserved.
            </p>
        </div>
    </div>
</footer>
