@extends('layouts.admin')

@section('style')
    <style>
        /* Standarisasi dengan list.blade.php */
        .form-container-focused {
            max-width: 1000px;
            margin: 0 auto;
        }

        /* Segmented Control Refined (Tipe) */
        .type-toggle input[value="highlight"]:checked+.toggle-card {
            background-color: #fffbeb;
            color: #b45309;
            border-color: #fbbf24;
            /* amber-400 */
            box-shadow: 0 10px 15px -3px rgba(251, 191, 36, 0.15);
        }

        .type-toggle input[value="rekomendasi"]:checked+.toggle-card {
            background-color: #eff6ff;
            color: #1d4ed8;
            border-color: #60a5fa;
            /* blue-400 */
            box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.15);
        }

        .toggle-card {
            background-color: #ffffff;
            color: #64748b;
            border: 2px solid #e2e8f0;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .toggle-card:hover {
            border-color: #cbd5e1;
            transform: translateY(-2px);
        }

        /* Segmented Control Refined (Durasi) */
        .duration-toggle input:checked+.duration-card {
            background-color: #f0fdfa;
            /* teal-50 */
            border-color: #0d9488;
            /* teal-600 */
            box-shadow: 0 4px 6px -1px rgba(13, 148, 136, 0.1);
        }

        .duration-toggle input:checked+.duration-card .icon-dur,
        .duration-toggle input:checked+.duration-card .label-dur {
            color: #0d9488;
        }

        .duration-toggle input:checked+.duration-card .radio-circle {
            border-color: #0d9488;
            background-color: #0d9488;
            box-shadow: inset 0 0 0 3px #f0fdfa;
        }

        .duration-card {
            border: 2px solid #e2e8f0;
            transition: all 0.2s ease-in-out;
        }

        .duration-card:hover {
            border-color: #cbd5e1;
            background-color: #f8fafc;
        }

        /* Custom Scrollbar untuk Modal */
        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 10px;
        }

        /* Kustomisasi SweetAlert agar senada */
        .swal2-popup {
            border-radius: 1.5rem !important;
            font-family: 'Inter', sans-serif;
        }
    </style>
@endsection

@section('content')
    @php
        $user = Auth::user();
        $routeAction = $user->role == 'admin' ? route('admin.highlight.store') : route('user.highlight.store');
    @endphp

    <div class="w-full min-h-screen p-4 sm:p-6 lg:p-8 font-['Inter'] bg-gray-50/50">

        <div class="form-container-focused">

            {{-- ==========================================================
                 1. HEADER AREA
                 ========================================================== --}}
            <div class="flex items-center gap-4 mb-8">
                <a href="{{ route('admin.highlight.index') }}"
                    class="w-12 h-12 rounded-2xl bg-white border border-gray-200 flex items-center justify-center text-gray-400 hover:text-[#0d9488] transition-all shadow-sm hover:shadow-md">
                    <i class="fas fa-chevron-left"></i>
                </a>
                <div>
                    <h2
                        class="text-2xl md:text-3xl font-extrabold text-gray-900 m-0 font-['Plus_Jakarta_Sans'] tracking-tight">
                        Promosikan Properti
                    </h2>
                    <p class="text-sm text-gray-500 m-0 mt-1">Konfigurasi penempatan properti eksklusif untuk visibilitas
                        maksimal.</p>
                </div>
            </div>

            <form action="{{ $routeAction }}" method="POST" id="highlightForm" class="space-y-6">
                @csrf

                {{-- ==========================================================
                     2. PENGATURAN PROMOSI (TIPE & DURASI)
                     ========================================================== --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                    {{-- KIRI: TIPE PENAYANGAN --}}
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 sm:p-8">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 bg-teal-50 rounded-xl flex items-center justify-center text-[#0d9488]">
                                <i class="fas fa-layer-group"></i>
                            </div>
                            <h4 class="font-['Plus_Jakarta_Sans'] text-lg font-bold text-gray-900 m-0">Tipe Penayangan</h4>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <label class="type-toggle cursor-pointer group h-full">
                                <input type="radio" name="type" value="rekomendasi" class="hidden"
                                    {{ old('type', $highlight->type ?? 'rekomendasi') == 'rekomendasi' ? 'checked' : '' }}>
                                <div
                                    class="toggle-card p-5 rounded-[1.5rem] flex flex-col items-center text-center gap-3 h-full justify-center">
                                    <div
                                        class="w-12 h-12 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center">
                                        <i class="fas fa-thumbs-up text-xl"></i>
                                    </div>
                                    <div>
                                        <span
                                            class="block font-extrabold text-sm uppercase tracking-wide mb-1">Rekomendasi</span>
                                        <span class="text-xs opacity-80 block leading-tight">Tampil di list halaman
                                            utama</span>
                                    </div>
                                </div>
                            </label>

                            <label class="type-toggle cursor-pointer group h-full">
                                <input type="radio" name="type" value="highlight" class="hidden"
                                    {{ old('type', $highlight->type ?? '') == 'highlight' ? 'checked' : '' }}>
                                <div
                                    class="toggle-card p-5 rounded-[1.5rem] flex flex-col items-center text-center gap-3 h-full justify-center">
                                    <div
                                        class="w-12 h-12 rounded-full bg-amber-50 text-amber-500 flex items-center justify-center">
                                        <i class="fas fa-star text-xl"></i>
                                    </div>
                                    <div>
                                        <span
                                            class="block font-extrabold text-sm uppercase tracking-wide mb-1">Highlight</span>
                                        <span class="text-xs opacity-80 block leading-tight">Prioritas posisi paling
                                            atas</span>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- KANAN: DURASI --}}
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 sm:p-8">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 bg-teal-50 rounded-xl flex items-center justify-center text-[#0d9488]">
                                <i class="fas fa-clock"></i>
                            </div>
                            <h4 class="font-['Plus_Jakarta_Sans'] text-lg font-bold text-gray-900 m-0">Durasi Promosi</h4>
                        </div>

                        <div class="flex flex-col gap-3">
                            {{-- Pilihan 1 Minggu --}}
                            <label class="duration-toggle cursor-pointer">
                                <input type="radio" name="duration" value="1_minggu" class="hidden" checked>
                                <div class="duration-card p-4 rounded-2xl flex items-center justify-between bg-white">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center icon-dur text-gray-400 transition-colors">
                                            <i class="fas fa-calendar-day"></i>
                                        </div>
                                        <div>
                                            <span
                                                class="block font-bold text-gray-700 label-dur transition-colors text-sm">1
                                                Minggu</span>
                                            <span class="text-[11px] text-gray-400">Masa tayang 7 Hari</span>
                                        </div>
                                    </div>
                                    <div class="w-5 h-5 rounded-full border-2 border-gray-300 radio-circle transition-all">
                                    </div>
                                </div>
                            </label>

                            {{-- Pilihan 2 Minggu --}}
                            <label class="duration-toggle cursor-pointer">
                                <input type="radio" name="duration" value="2_minggu" class="hidden">
                                <div class="duration-card p-4 rounded-2xl flex items-center justify-between bg-white">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center icon-dur text-gray-400 transition-colors">
                                            <i class="fas fa-calendar-week"></i>
                                        </div>
                                        <div>
                                            <span
                                                class="block font-bold text-gray-700 label-dur transition-colors text-sm">2
                                                Minggu</span>
                                            <span class="text-[11px] text-gray-400">Masa tayang 14 Hari</span>
                                        </div>
                                    </div>
                                    <div class="w-5 h-5 rounded-full border-2 border-gray-300 radio-circle transition-all">
                                    </div>
                                </div>
                            </label>

                            {{-- Pilihan 1 Bulan --}}
                            <label class="duration-toggle cursor-pointer">
                                <input type="radio" name="duration" value="1_bulan" class="hidden">
                                <div class="duration-card p-4 rounded-2xl flex items-center justify-between bg-white">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center icon-dur text-gray-400 transition-colors">
                                            <i class="fas fa-calendar-alt"></i>
                                        </div>
                                        <div>
                                            <span
                                                class="block font-bold text-gray-700 label-dur transition-colors text-sm">1
                                                Bulan</span>
                                            <span class="text-[11px] text-gray-400">Masa tayang 30 Hari</span>
                                        </div>
                                    </div>
                                    <div class="w-5 h-5 rounded-full border-2 border-gray-300 radio-circle transition-all">
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                </div>

                {{-- ==========================================================
                     3. TARGET PROPERTI (AGENT & UNIT)
                     ========================================================== --}}
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 sm:p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-teal-50 rounded-xl flex items-center justify-center text-[#0d9488]">
                            <i class="fas fa-bullseye"></i>
                        </div>
                        <h4 class="font-['Plus_Jakarta_Sans'] text-lg font-bold text-gray-900 m-0">Target Promosi</h4>
                    </div>

                    <div class="grid grid-cols-1 {{ $user->role == 'admin' ? 'md:grid-cols-2' : '' }} gap-6">

                        {{-- KIRI: PICKER AGENT (HANYA ADMIN) --}}
                        @if ($user->role == 'admin')
                            <div class="flex flex-col">
                                <label
                                    class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-3 flex items-center">
                                    Penanggung Jawab (Agen)
                                </label>
                                <input type="hidden" name="user_id" id="selected_agent_id" required>

                                {{-- Placeholder --}}
                                <div id="agent_placeholder" onclick="openModal('modalAgent')"
                                    class="flex-1 border-2 border-dashed border-gray-300 bg-gray-50 rounded-[1.5rem] p-6 flex flex-col items-center justify-center cursor-pointer hover:border-[#0d9488] hover:bg-teal-50/50 transition-all min-h-[160px] group text-center">
                                    <div
                                        class="w-12 h-12 bg-white shadow-sm rounded-full flex items-center justify-center text-gray-400 group-hover:text-[#0d9488] transition-all mb-3">
                                        <i class="fas fa-user-plus text-lg"></i>
                                    </div>
                                    <p class="text-sm font-bold text-gray-500 group-hover:text-[#0d9488] m-0">Ketuk untuk
                                        Pilih Agen</p>
                                </div>

                                {{-- Preview --}}
                                <div id="agent_preview"
                                    class="hidden flex-1 bg-teal-50/30 rounded-[1.5rem] p-6 border-2 border-[#0d9488]/20 flex flex-col items-center text-center justify-center min-h-[160px] relative">

                                    <button type="button" onclick="openModal('modalAgent')"
                                        class="absolute top-4 right-4 w-8 h-8 rounded-full bg-white shadow-sm text-gray-400 hover:text-[#0d9488] flex items-center justify-center transition-all">
                                        <i class="fas fa-pencil-alt text-xs"></i>
                                    </button>

                                    <div id="preview_agent_initials"
                                        class="w-16 h-16 rounded-full flex items-center justify-center shadow-md mb-3 bg-[#0d9488] text-white font-extrabold text-xl uppercase tracking-widest">
                                    </div>
                                    <h4 id="preview_agent_name" class="font-bold text-gray-900 text-sm m-0"></h4>
                                    <p id="preview_agent_role"
                                        class="text-[10px] font-bold text-[#0d9488] uppercase tracking-widest mt-1 m-0 hidden">
                                    </p>
                                </div>
                            </div>
                        @endif

                        {{-- KANAN: PICKER PROPERTI --}}
                        <div class="flex flex-col">
                            <label
                                class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-3 flex items-center">
                                Unit Properti
                            </label>
                            <input type="hidden" name="property_id" id="selected_property_id" required>

                            {{-- Placeholder --}}
                            <div id="property_placeholder" onclick="openModal('modalProperty')"
                                class="flex-1 border-2 border-dashed border-gray-300 bg-gray-50 rounded-[1.5rem] p-6 flex flex-col items-center justify-center cursor-pointer hover:border-[#0d9488] hover:bg-teal-50/50 transition-all min-h-[160px] group text-center">
                                <div
                                    class="w-12 h-12 bg-white shadow-sm rounded-full flex items-center justify-center text-gray-400 group-hover:text-[#0d9488] transition-all mb-3">
                                    <i class="fas fa-search-plus text-lg"></i>
                                </div>
                                <p class="text-sm font-bold text-gray-500 group-hover:text-[#0d9488] m-0">Cari Unit
                                    Properti</p>
                            </div>

                            {{-- Preview --}}
                            <div id="property_preview"
                                class="hidden flex-1 bg-teal-50/30 rounded-[1.5rem] p-4 border-2 border-[#0d9488]/20 flex flex-col min-h-[160px] relative">

                                <button type="button" onclick="openModal('modalProperty')"
                                    class="absolute top-6 right-6 z-10 w-8 h-8 rounded-full bg-white shadow-md text-gray-400 hover:text-[#0d9488] flex items-center justify-center transition-all">
                                    <i class="fas fa-pencil-alt text-xs"></i>
                                </button>

                                <div
                                    class="relative w-full h-32 rounded-xl overflow-hidden mb-3 shadow-sm border border-gray-200">
                                    <img id="preview_prop_img" src="" class="w-full h-full object-cover">
                                    <div class="absolute inset-0 bg-gradient-to-t from-gray-900/80 to-transparent"></div>
                                    <span id="preview_prop_price"
                                        class="absolute bottom-2 left-3 text-white font-extrabold text-sm font-['Plus_Jakarta_Sans']"></span>
                                </div>
                                <h4 id="preview_prop_title"
                                    class="font-bold text-gray-900 text-sm line-clamp-2 px-1 leading-snug"></h4>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- ==========================================================
                     4. ACTION BAR 
                     ========================================================== --}}
                <div
                    class="flex flex-col sm:flex-row items-center justify-between bg-gray-900 rounded-3xl p-4 sm:p-5 shadow-xl gap-4">
                    <div class="flex items-center gap-3 px-2">
                        <div class="w-8 h-8 rounded-full bg-gray-800 flex items-center justify-center text-amber-400">
                            <i class="fas fa-info-circle text-sm"></i>
                        </div>
                        <p class="text-gray-300 text-xs font-medium m-0 leading-tight">
                            Pastikan data benar sebelum <br class="hidden sm:block lg:hidden"> dipublikasikan.
                        </p>
                    </div>
                    <div class="flex gap-3 w-full sm:w-auto">
                        <a href="{{ route('admin.highlight.index') }}"
                            class="flex-1 sm:flex-none px-6 py-3.5 text-center text-gray-400 hover:text-white bg-gray-800 hover:bg-gray-700 rounded-2xl font-bold text-sm transition-colors no-underline">
                            Batal
                        </a>
                        <button type="submit"
                            class="flex-1 sm:flex-none px-8 py-3.5 bg-[#0d9488] hover:bg-[#0f766e] text-white font-bold text-sm rounded-2xl shadow-[0_4px_15px_rgba(13,148,136,0.4)] transition-all active:scale-95 flex items-center justify-center">
                            <i class="fas fa-rocket mr-2"></i> Aktifkan Promosi
                        </button>
                    </div>
                </div>

            </form>
        </div>

        {{-- ==========================================================
             MODALS (Tidak Diubah Struktur Logikanya)
             ========================================================== --}}

        {{-- MODAL AGENT --}}
        <div id="modalAgent" onclick="closeModal('modalAgent')"
            class="fixed inset-0 z-[999] hidden items-center justify-center bg-gray-900/60 backdrop-blur-sm p-4 transition-all">
            <div onclick="event.stopPropagation()"
                class="bg-white w-full max-w-xl rounded-[2rem] shadow-2xl flex flex-col max-h-[85vh] overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <h3 class="font-extrabold text-gray-900 flex items-center font-['Plus_Jakarta_Sans'] m-0 text-lg">
                        <i class="fas fa-user-tie mr-3 text-[#0d9488]"></i> Pilih Penanggung Jawab
                    </h3>
                    <button type="button" onclick="closeModal('modalAgent')"
                        class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 hover:bg-red-50 text-gray-500 hover:text-red-500 transition-all">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>
                <div class="p-4 border-b border-gray-100">
                    <div class="relative">
                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text" id="searchAgentInput"
                            onkeyup="filterList('searchAgentInput', 'agentList', 'agent')" placeholder="Cari nama agen..."
                            class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#0d9488]/20 focus:border-[#0d9488] text-sm font-medium transition-all outline-none">
                    </div>
                </div>
                <div class="relative flex-1 overflow-hidden flex flex-col bg-white">
                    <div id="loading-agent"
                        class="hidden absolute inset-0 bg-white/90 z-10 flex-col items-center justify-center">
                        <div class="w-8 h-8 border-4 border-teal-100 border-t-[#0d9488] rounded-full animate-spin mb-3">
                        </div>
                        <p class="text-xs font-bold text-gray-500">Mencari data...</p>
                    </div>
                    <div id="empty-agent" class="hidden py-12 flex-col items-center justify-center text-center px-6">
                        <i class="fas fa-user-slash text-gray-300 text-4xl mb-3"></i>
                        <h5 class="text-sm font-bold text-gray-800 m-0">Agen Tidak Ditemukan</h5>
                    </div>
                    <div id="agentList" class="overflow-y-auto p-4 space-y-2 custom-scrollbar flex-1"></div>
                </div>
            </div>
        </div>

        {{-- MODAL PROPERTI --}}
        <div id="modalProperty" onclick="closeModal('modalProperty')"
            class="fixed inset-0 z-[999] hidden items-center justify-center bg-gray-900/60 backdrop-blur-sm p-4 transition-all">
            <div onclick="event.stopPropagation()"
                class="bg-white w-full max-w-2xl rounded-[2rem] shadow-2xl flex flex-col max-h-[85vh] overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <h3 class="font-extrabold text-gray-900 flex items-center font-['Plus_Jakarta_Sans'] m-0 text-lg">
                        <i class="fas fa-building mr-3 text-[#0d9488]"></i> Pilih Unit Properti
                    </h3>
                    <button type="button" onclick="closeModal('modalProperty')"
                        class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 hover:bg-red-50 text-gray-500 hover:text-red-500 transition-all">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>
                <div class="p-4 border-b border-gray-100">
                    <div class="relative">
                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text" id="searchPropInput"
                            onkeyup="filterList('searchPropInput', 'propList', 'prop')"
                            placeholder="Ketik judul properti..."
                            class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#0d9488]/20 focus:border-[#0d9488] text-sm font-medium transition-all outline-none">
                    </div>
                </div>
                <div class="relative flex-1 overflow-hidden flex flex-col bg-white">
                    <div id="loading-prop"
                        class="hidden absolute inset-0 bg-white/90 z-10 flex-col items-center justify-center">
                        <div class="w-8 h-8 border-4 border-teal-100 border-t-[#0d9488] rounded-full animate-spin mb-3">
                        </div>
                        <p class="text-xs font-bold text-gray-500">Mencari properti...</p>
                    </div>
                    <div id="empty-prop" class="hidden py-12 flex-col items-center justify-center text-center px-6">
                        <i class="fas fa-search-location text-gray-300 text-4xl mb-3"></i>
                        <h5 class="text-sm font-bold text-gray-800 m-0">Properti Tidak Ditemukan</h5>
                    </div>
                    <div id="propList" class="overflow-y-auto p-4 space-y-2 custom-scrollbar flex-1"></div>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('script')
    {{-- JS SAMA SEPERTI SEBELUMNYA, TIDAK DIUBAH --}}
    <script>
        let searchTimer;

        function openModal(id) {
            const modal = document.getElementById(id);
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeModal(id) {
            const modal = document.getElementById(id);
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function selectProperty(data) {
            document.getElementById('selected_property_id').value = data.id;
            document.getElementById('preview_prop_img').src = data.img;
            document.getElementById('preview_prop_title').innerText = data.title;
            document.getElementById('preview_prop_price').innerText = data.price;

            document.getElementById('property_placeholder').classList.add('hidden');
            document.getElementById('property_preview').classList.remove('hidden');
            closeModal('modalProperty');
        }

        function selectAgent(data) {
            document.getElementById('selected_agent_id').value = data.id;
            const name = data.name || '??';
            const initials = name.split(' ').map(word => word[0]).join('').substring(0, 2).toUpperCase();

            document.getElementById('preview_agent_initials').innerText = initials;
            document.getElementById('preview_agent_name').innerText = data.name;

            const roleEl = document.getElementById('preview_agent_role');
            if (roleEl) roleEl.innerText = data.role;

            document.getElementById('agent_placeholder').classList.add('hidden');
            document.getElementById('agent_preview').classList.remove('hidden');
            closeModal('modalAgent');
        }

        async function filterList(inputId, listId, type) {
            const query = document.getElementById(inputId).value;
            const listContainer = document.getElementById(listId);
            const loadingEl = document.getElementById(`loading-${type}`);
            const emptyEl = document.getElementById(`empty-${type}`);

            loadingEl.classList.remove('hidden');
            loadingEl.classList.add('flex');
            emptyEl.classList.add('hidden');
            emptyEl.classList.remove('flex');
            listContainer.style.opacity = '0.3';

            clearTimeout(searchTimer);

            searchTimer = setTimeout(async () => {
                try {
                    const agentId = document.getElementById('selected_agent_id')?.value || '';

                    const url = type === 'agent' ?
                        `{{ route('admin.highlight.search.agents') }}?q=${query}` :
                        `{{ route('user.highlight.search.properties') }}?q=${query}&user_id=${agentId}`;

                    const response = await fetch(url);
                    const data = await response.json();

                    listContainer.innerHTML = '';

                    if (data.length === 0) {
                        listContainer.classList.add('hidden');
                        emptyEl.classList.remove('hidden');
                        emptyEl.classList.add('flex');
                    } else {
                        listContainer.classList.remove('hidden');
                        emptyEl.classList.add('hidden');
                        emptyEl.classList.remove('flex');

                        data.forEach(item => {
                            const html = type === 'agent' ? renderAgentItem(item) :
                                renderPropertyItem(item);
                            listContainer.insertAdjacentHTML('beforeend', html);
                        });
                    }
                } catch (error) {
                    console.error('Error fetching data:', error);
                } finally {
                    loadingEl.classList.add('hidden');
                    loadingEl.classList.remove('flex');
                    listContainer.style.opacity = '1';
                }
            }, 600);
        }

        function renderAgentItem(agent) {
            return `
                <div onclick="selectAgent({id: '${agent.id}', name: '${agent.name}'})" 
                    class="flex items-center gap-4 p-3 rounded-xl hover:bg-teal-50 cursor-pointer transition-all border border-transparent hover:border-teal-100 group mb-2">
                    <div class="w-10 h-10 rounded-full bg-teal-100 text-[#0d9488] flex items-center justify-center font-bold text-xs uppercase group-hover:bg-[#0d9488] group-hover:text-white transition-colors">
                        ${getInitials(agent.name)}
                    </div>
                    <div class="flex-1">
                        <h5 class="text-sm font-bold text-gray-900 m-0">${agent.name}</h5>
                    </div>
                    <i class="fas fa-check-circle text-teal-500 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                </div>
            `;
        }

        function renderPropertyItem(prop) {
            return `
                <div onclick="selectProperty({id: '${prop.id}', title: '${prop.title.replace(/'/g, "\\'")}', price: '${prop.price_formatted}', img: '${prop.img}'})" 
                    class="flex items-center gap-4 p-3 rounded-xl hover:bg-teal-50 cursor-pointer transition-all border border-transparent hover:border-teal-100 group mb-2">
                    <div class="w-16 h-12 rounded-lg overflow-hidden shrink-0">
                        <img src="${prop.img}" class="w-full h-full object-cover">
                    </div>
                    <div class="flex-1 min-w-0">
                        <h5 class="text-sm font-bold text-gray-900 m-0 truncate">${prop.title}</h5>
                        <p class="text-xs text-[#0d9488] font-bold m-0 mt-0.5">${prop.price_formatted}</p>
                    </div>
                    <i class="fas fa-check-circle text-teal-500 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                </div>
            `;
        }

        function getInitials(name) {
            if (!name) return '??';
            const words = name.split(' ');
            let initials = words[0].substring(0, 1);
            if (words.length > 1) {
                initials += words[words.length - 1].substring(0, 1);
            }
            return initials.toUpperCase();
        }
    </script>
@endsection
