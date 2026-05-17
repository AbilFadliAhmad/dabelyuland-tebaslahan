@extends('layouts.index')

@section('content')
    <div class="bg-[#FAFAFA] min-h-screen font-['Inter'] pt-24 md:pt-32 pb-20">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- ==========================================
             1. HEADER SECTION
             ========================================== --}}
            <div class="text-center mb-8 md:mb-10">
                <div
                    class="inline-flex items-center justify-center w-14 h-14 md:w-16 md:h-16 rounded-full bg-teal-50 text-[#0d9488] mb-4">
                    <i class="fas fa-bell text-xl md:text-2xl"></i>
                </div>
                <h1
                    class="font-['Plus_Jakarta_Sans'] text-2xl md:text-4xl font-extrabold text-gray-900 tracking-tight mb-2 md:mb-3">
                    Atur Notifikasi Properti
                </h1>
                <p class="text-gray-500 text-sm md:text-base px-2">Dapatkan notifikasi otomatis saat ada properti baru di
                    kota incaranmu.</p>
            </div>

            {{-- ==========================================
             2. MASTER TOGGLE NOTIFIKASI
             ========================================== --}}
            <div class="flex items-center justify-between bg-teal-50/50 p-4 md:p-5 rounded-2xl border border-teal-100 mb-6 md:mb-8 transition-colors duration-300 shadow-sm"
                id="toggleContainer">
                <div class="pr-4">
                    <h3 class="text-sm md:text-base font-bold text-gray-900 mb-0.5">Status Notifikasi</h3>
                    <p class="text-[11px] md:text-xs text-gray-500 transition-colors leading-tight" id="notifStatusText">
                        Pemberitahuan properti baru aktif.</p>
                </div>

                {{-- Toggle Switch buatan Tailwind --}}
                <label class="relative inline-flex items-center cursor-pointer shrink-0">
                    <input type="checkbox" id="masterToggle" class="sr-only peer" checked>
                    <div
                        class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#0d9488]">
                    </div>

                    <div class="flex items-center ml-2 md:ml-3 min-w-[50px]">
                        <span class="text-sm font-bold text-[#0d9488]" id="notifLabelText">Aktif</span>

                        <div id="notifLoading" class="hidden ml-2">
                            <svg class="animate-spin h-4 w-4 text-[#0d9488]" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                        </div>
                    </div>
                </label>
            </div>

            {{-- ==========================================
             3. MAIN CARD (KONTEN PENGATURAN LOKASI)
             ========================================== --}}
            <div id="mainCard" class="bg-white rounded-3xl md:rounded-4xl shadow-sm border border-gray-100 p-5 md:p-8">

                {{-- Bagian A: Search Bar Tambah Lokasi --}}
                <div class="relative mb-6 md:mb-8">
                    <label class="block text-sm md:text-base font-bold text-gray-700 mb-2.5">Tambah Kota / Area</label>
                    <div class="relative w-full">
                        <div class="relative flex items-center">
                            <i class="fas fa-search absolute left-4 text-gray-400"></i>
                            <input type="text" id="searchCity"
                                class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-[#0d9488] focus:border-[#0d9488] block pl-11 pr-24 py-3.5 transition outline-none"
                                placeholder="Ketik nama kota..." autocomplete="off">

                            <button id="addCityBtn" disabled
                                class="absolute right-2 px-4 py-2 bg-gray-400 text-white text-xs md:text-sm font-bold rounded-lg transition cursor-not-allowed">
                                Tambah
                            </button>
                        </div>

                        <ul id="suggestionBox"
                            class="absolute z-50 w-full mt-2 bg-white border border-gray-200 rounded-xl shadow-lg hidden max-h-60 overflow-y-auto">
                        </ul>
                    </div>
                </div>

                <hr class="border-gray-100 mb-6 md:mb-8">

                {{-- Bagian B: Daftar Lokasi Aktif (Pill Badges) --}}
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base md:text-lg font-bold text-gray-900 font-['Plus_Jakarta_Sans']">Lokasi Terpantau
                        </h3>
                        <span
                            class="bg-teal-50 text-[#0d9488] text-[10px] md:text-xs font-bold px-3 py-1.5 md:py-1 rounded-full"><span
                                id="countActiveCity">0</span> Aktif</span>
                    </div>

                    <div class="flex flex-wrap gap-2.5 md:gap-3" id="activeLocations">

                    </div>

                    {{-- Empty State (Tampil jika tidak ada lokasi) --}}
                    <div id="emptyState" class="hidden text-center py-8">
                        <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-map-marked-alt text-gray-300 text-lg"></i>
                        </div>
                        <p class="text-gray-400 text-xs md:text-sm">Belum ada lokasi yang dipantau.</p>
                    </div>
                </div>

                {{-- Bagian C: Tombol Simpan --}}
                <div class="mt-8 md:mt-10">
                    <button onclick="saveSettings()" type="button" id="saveBtn"
                        class="w-full py-3.5 bg-gray-900 hover:bg-gray-800 text-white text-sm font-bold rounded-xl transition shadow-md active:scale-[0.98] flex items-center justify-center">
                        Simpan Pengaturan
                    </button>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        /**
         * KONFIGURASI & STATE GLOBAL
         */
        const UI_CLASSES = {
            ACTIVE_TEXT: 'text-[#0d9488]',
            INACTIVE_TEXT: 'text-gray-500',
            BTN_ENABLED: ['bg-[#0d9488]', 'hover:bg-teal-700', 'active:scale-95'],
            BTN_DISABLED: ['bg-gray-400', 'cursor-not-allowed'],
            INPUT_DISABLED: ['opacity-50', 'cursor-not-allowed']
        };

        // State Aplikasi
        let activeLocations = JSON.parse(localStorage.getItem('activeLocations')) || [];
        let topSuggestion = null;
        let searchTimeout = null;

        /**
         * SELEKTOR ELEMEN
         * Menggunakan getter agar elemen dicari saat DOM sudah siap
         */
        const getElements = () => ({
            masterToggle: document.getElementById('masterToggle'),
            statusLabel: document.getElementById('notifLabelText'),
            statusDesc: document.getElementById('notifStatusText'),
            container: document.getElementById('toggleContainer'),
            cityInput: document.getElementById('searchCity'),
            addBtn: document.getElementById('addCityBtn'),
            suggestionBox: document.getElementById('suggestionBox'),
            activeLocationsList: document.getElementById('activeLocations'),
            counter: document.getElementById('countActiveCity')
        });

        // ==========================================
        // FUNGSI CORE (LOGIKA DATA)
        // ==========================================

        /**
         * Menyimpan Lokasi Aktif ke Firebase.
         */
        async function saveSettings() {
            try {
                // Tampilkan Loading State
                Swal.fire({
                    title: 'Sinkronisasi...',
                    text: 'Mendaftarkan lokasi ke sistem notifikasi',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                // STEP 1: Izin Notifikasi
                const permission = await Notification.requestPermission();
                if (permission !== 'granted') throw new Error("Izin notifikasi ditolak.");

                // STEP 2: Registrasi Manual (Sangat Disarankan)
                const registration = await navigator.serviceWorker.register('/firebase-messaging-sw.js', {
                    scope: '/'
                });
                await navigator.serviceWorker.ready;

                // STEP 3: Ambil Token dengan menyertakan registration
                const token = await window.getFirebaseToken(window.firebaseMessaging, {
                    serviceWorkerRegistration: registration, // Sertakan ini agar lebih stabil
                    vapidKey: 'BO98ZwFeBDp7k0VX0UpOrkHzmgQjwERBEI1Fu5nO-31TBcrQ9FoNcMWhyyhajgxbbNCYlFlMdNEnfhypMEeJngg'
                });

                if (!token) throw new Error("Gagal mendapatkan token. Cek konfigurasi Firebase.");

                // STEP 4: Kirim ke Laravel
                const topicList = activeLocations.map(loc => loc.name);
                const response = await fetch("{{ route('subscribe-topics') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                    },
                    body: JSON.stringify({
                        fcm_token: token,
                        old_topics: localStorage.getItem('activeLocations') ? JSON.parse(localStorage
                            .getItem('activeLocations')) : [],
                        topics: topicList,
                        new_topics: topicList
                    })
                });

                if (!response.ok) throw new Error("Gagal sinkronisasi dengan server Laravel.");

                // STEP 5: Berhasil
                localStorage.setItem('activeLocations', JSON.stringify(activeLocations));
                localStorage.setItem('fcmToken', token);

                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Lokasi disimpan dan notifikasi telah aktif.',
                    icon: 'success',
                    confirmButtonColor: '#0d9488'
                });

            } catch (error) {
                console.error("FCM Error:", error);
                Swal.fire('Gagal!', error.message, 'error');
            }
        }

        /**
         * Menghapus lokasi dari state dan UI.
         * Dipanggil via onclick="removeLocation(this)" di HTML.
         */
        function removeLocation(btn) {
            const el = getElements();
            const pill = btn.closest('.location-pill');
            const cityName = pill.getAttribute('data-name');

            // Update State
            activeLocations = activeLocations.filter(loc => loc.name !== cityName);

            // Update UI
            pill.remove();
            updateCounter();
        }

        /**
         * Menambahkan lokasi baru ke state dan UI (Belum tersimpan ke storage)
         */
        const addLocation = (cityObj) => {
            const el = getElements();
            const isDuplicate = activeLocations.some(loc => loc.name === cityObj.name);

            if (!isDuplicate) {
                activeLocations.push(cityObj);
                renderPill(cityObj.name);
                updateCounter();
            }

            el.cityInput.value = '';
            resetSuggestions();
        };

        const updateCounter = () => {
            const el = getElements();
            if (el.counter) el.counter.textContent = activeLocations.length;
        };

        // ==========================================
        // FUNGSI UI (RENDERING & STYLING)
        // ==========================================

        /**
         * Membuat elemen visual kota (Pill)
         */
        const renderPill = (name) => {
            const el = getElements();
            const pill = document.createElement('div');
            pill.className =
                "location-pill inline-flex items-center bg-white border border-[#0d9488] text-[#0d9488] px-4 py-2 rounded-full text-sm font-semibold transition group hover:bg-[#0d9488] hover:text-white cursor-pointer shadow-sm";
            pill.setAttribute('data-name', name);

            pill.innerHTML = `
                                <i class="fas fa-map-marker-alt mr-2 text-xs"></i>
                                <span>${name}</span>
                                <button type="button" class="ml-3 text-[#0d9488] group-hover:text-white focus:outline-none hover:scale-110 transition-transform" onclick="removeLocation(this)">
                                    <i class="fas fa-times text-xs"></i>
                                </button>
                            `;
            el.activeLocationsList.appendChild(pill);
        };

        const updateNotificationStatusUI = async (isActive, isFetching = false) => {
            const el = getElements();
            const loadingEl = document.getElementById('notifLoading');
            const mainCard = document.getElementById('mainCard');
            if (!el.masterToggle) return;

            // --- A. UPDATE UI DASAR (Tanpa Loading) ---
            el.statusLabel.innerText = isActive ? "Aktif" : "Mati";
            el.statusDesc.innerText = isActive ? "Pemberitahuan properti baru aktif." :
                "Notifikasi dijeda sementara.";

            if (isActive) {
                el.statusLabel.classList.replace(UI_CLASSES.INACTIVE_TEXT, UI_CLASSES.ACTIVE_TEXT);
                el.container.classList.add('bg-teal-50/50', 'border-teal-100');
                el.cityInput.disabled = false;
                el.cityInput.classList.remove(...UI_CLASSES.INPUT_DISABLED);
                mainCard.classList.remove('opacity-0');

            } else {
                el.statusLabel.classList.replace(UI_CLASSES.ACTIVE_TEXT, UI_CLASSES.INACTIVE_TEXT);
                el.container.classList.remove('bg-teal-50/50', 'border-teal-100');
                el.cityInput.disabled = true;
                el.cityInput.classList.add(...UI_CLASSES.INPUT_DISABLED);
                resetSuggestions();
                mainCard.classList.add('opacity-0');

            }

            // --- B. CEK APAKAH PERLU FETCH KE SERVER? ---
            if (!isFetching) {
                // Jika hanya render awal dari localStorage, berhenti di sini.
                updateAddButtonState(isActive && topSuggestion !== null);
                return;
            }

            // --- C. PROSES FETCHING (Dengan Loading State) ---
            try {
                // Aktifkan Loading
                loadingEl.classList.remove('hidden');
                el.masterToggle.disabled = true;
                el.statusLabel.style.opacity = "0.5";

                const response = await fetch("{{ route('toggle-notification') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                    },
                    body: JSON.stringify({
                        status: isActive,
                        topics: localStorage.getItem('activeLocations') ?
                            JSON.parse(localStorage.getItem('activeLocations')) : [],
                        fcm_token: localStorage.getItem('fcmToken')
                    })
                });

                if (!response.ok) throw new Error("Gagal sinkronisasi.");

                await idb_set('notificationStatus', isActive);
                updateAddButtonState(isActive && topSuggestion !== null);

            } catch (error) {
                console.error(error);
                // Kembalikan toggle jika gagal
                el.masterToggle.checked = !isActive;
                Toast.fire({
                    icon: 'error',
                    title: 'Gagal sinkronisasi server'
                });
            } finally {
                // Matikan Loading (Hanya berjalan jika isFetching = true)
                loadingEl.classList.add('hidden');
                el.masterToggle.disabled = false;
                el.statusLabel.style.opacity = "1";
            }
        };

        const updateAddButtonState = (isEnabled) => {
            const el = getElements();
            el.addBtn.disabled = !isEnabled;
            if (isEnabled) {
                el.addBtn.classList.remove(...UI_CLASSES.BTN_DISABLED);
                el.addBtn.classList.add(...UI_CLASSES.BTN_ENABLED);
            } else {
                el.addBtn.classList.add(...UI_CLASSES.BTN_DISABLED);
                el.addBtn.classList.remove(...UI_CLASSES.BTN_ENABLED);
            }
        };

        const resetSuggestions = () => {
            const el = getElements();
            el.suggestionBox.innerHTML = '';
            el.suggestionBox.classList.add('hidden');
            topSuggestion = null;
            updateAddButtonState(false);
        };

        // ==========================================
        // PENCARIAN & API
        // ==========================================

        const handleSearchInput = async (keyword) => {
            const el = getElements();
            clearTimeout(searchTimeout);

            if (keyword.length <= 2) {
                resetSuggestions();
                return;
            }

            // Show Loading
            el.suggestionBox.innerHTML = `<div class="p-4 text-center text-sm text-gray-500">Mencari kota...</div>`;
            el.suggestionBox.classList.remove('hidden');

            searchTimeout = setTimeout(async () => {
                try {
                    const response = await fetch("{{ route('search-cities') }}"+"?q="+encodeURIComponent(keyword));
                    const cities = await response.json();

                    if (cities.length > 0) {
                        topSuggestion = cities[0];
                        renderSuggestionList(cities);
                        updateAddButtonState(true);
                    } else {
                        showNotFound(keyword);
                    }
                } catch (error) {
                    console.error("Gagal memuat kota:", error);
                }
            }, 600);
        };

        const renderSuggestionList = (cities) => {
            const el = getElements();
            el.suggestionBox.innerHTML = '';
            cities.forEach(city => {
                const item = document.createElement('li');
                item.className =
                    'px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm border-b border-gray-50 last:border-none';
                item.textContent = city.name;
                item.onclick = () => {
                    el.cityInput.value = city.name;
                    topSuggestion = city;
                    resetSuggestions();
                    updateAddButtonState(true);
                };
                el.suggestionBox.appendChild(item);
            });
        };

        const showNotFound = (keyword) => {
            const el = getElements();
            el.suggestionBox.innerHTML =
                `<div class="p-4 text-center text-sm text-gray-500">Kota "${keyword}" tidak ditemukan.</div>`;
            updateAddButtonState(false);
        };

        // ==========================================
        // INISIALISASI EVENT LISTENERS
        // ==========================================

        document.addEventListener('DOMContentLoaded', () => {
            const el = getElements();

            // Render data awal dari localStorage
            activeLocations.forEach(loc => renderPill(loc.name));
            updateCounter();

            // State Toggle Utama
            el.masterToggle.checked = localStorage.getItem('notificationStatus') === 'true';
            updateNotificationStatusUI(el.masterToggle.checked);

            // Listener Toggle Utama
            el.masterToggle?.addEventListener('change', (e) => updateNotificationStatusUI(e.target.checked, true));

            // Listener Input Pencarian
            el.cityInput?.addEventListener('input', (e) => handleSearchInput(e.target.value.trim()));

            // Listener Enter pada Input
            el.cityInput?.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') el.addBtn.click();
            });

            // Listener Tombol Tambah
            el.addBtn?.addEventListener('click', () => {
                if (topSuggestion) addLocation(topSuggestion);
            });

            // Klik luar kotak saran untuk menutup
            document.addEventListener('click', (e) => {
                if (!el.cityInput.contains(e.target) && !el.suggestionBox.contains(e.target)) {
                    el.suggestionBox.classList.add('hidden');
                }
            });
        });
    </script>
@endsection
