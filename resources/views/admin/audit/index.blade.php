@extends('layouts.admin')

@section('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <style>
        .table-audit th {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            color: #64748b;
            background-color: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
            padding: 1rem 1.5rem;
        }

        .table-audit td {
            padding: 1rem 1.5rem;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
        }

        .table-audit tbody tr:hover {
            background-color: #f8fafc;
            cursor: pointer;
        }

        /* Animasi Modal & Filter */
        .modal-enter {
            opacity: 0;
            transform: scale(0.95);
        }

        .modal-enter-active {
            opacity: 1;
            transform: scale(1);
            transition: all 0.2s ease-out;
        }

        .modal-leave {
            opacity: 1;
            transform: scale(1);
        }

        .modal-leave-active {
            opacity: 0;
            transform: scale(0.95);
            transition: all 0.2s ease-in;
        }

        /* Transition untuk toggle filter */
        #filterPanel {
            transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.3s ease-in-out;
            overflow: hidden;
        }

        .filter-hidden {
            max-height: 0;
            opacity: 0;
            padding-top: 0 !important;
            padding-bottom: 0 !important;
            border-bottom-width: 0 !important;
        }

        .filter-visible {
            max-height: 500px;
            opacity: 1;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/id.js"></script>
@endsection

@section('content')
    <div class="w-full min-h-screen p-4 sm:p-6 lg:p-8 font-['Inter'] bg-gray-50/50 relative">

        {{-- HEADER & TOMBOL TOGGLE FILTER --}}
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-8 gap-4">
            <div>
                <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900 m-0 font-['Plus_Jakarta_Sans'] tracking-tight">
                    Audit Log Sistem</h2>
                <p class="text-sm text-gray-500 m-0 mt-2">Pantau seluruh riwayat aktivitas teknis dan operasional secara
                    global.</p>
            </div>
            <button onclick="toggleFilter()" id="btnToggleFilter"
                class="px-5 py-2.5 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 font-bold rounded-xl shadow-sm transition-all text-sm flex items-center gap-2 border-teal-100 ring-4 ring-teal-50/50">
                <i class="fas fa-filter text-[#0d9488]"></i> <span>Buka Filter Pencarian</span>
            </button>
        </div>

        {{-- CONTAINER UTAMA --}}
        <div class="bg-white rounded-3xl shadow-[0_4px_25px_rgba(0,0,0,0.03)] border border-gray-100 overflow-hidden">

            {{-- PANEL FILTER (3 Kolom Simetris & Sempurna) --}}
            <div id="filterPanel" class="p-5 border-b border-gray-100 bg-gray-50/30 filter-hidden flex flex-col gap-5">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wider pl-1">Tipe Entitas</label>
                        <select id="filterType" onchange="validateFilterForm()"
                            class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm font-semibold text-gray-700 focus:outline-none focus:border-[#0d9488] focus:ring-4 focus:ring-teal-50 transition-all">
                            <option value="" disabled selected>-- Pilih Tipe Entitas --</option>
                            <option value="properti">Properti</option>
                            <option value="highlight">Highlight</option>
                            <option value="rekomendasi">Rekomendasi</option>
                            <option value="topup">Topup</option>
                            <option value="membership">Membership</option>
                            <option value="sesi">Sesi</option>
                            <option value="service">Service</option>
                        </select>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wider pl-1">Jenis Aktivitas</label>
                        <select id="filterAction" onchange="validateFilterForm()"
                            class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm font-semibold text-gray-700 focus:outline-none focus:border-[#0d9488] focus:ring-4 focus:ring-teal-50 transition-all">
                            <option value="" disabled selected>-- Pilih Aktivitas --</option>
                            <option value="create">Create (Penambahan)</option>
                            <option value="update">Update (Perubahan)</option>
                            <option value="delete">Delete (Penghapusan)</option>
                        </select>
                    </div>

                    <div class="flex flex-col gap-1.5 relative">
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wider pl-1">Rentang Waktu</label>
                        <select id="filterDate" onchange="handleDateSelect()"
                            class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm font-semibold text-gray-700 focus:outline-none focus:border-[#0d9488] focus:ring-4 focus:ring-teal-50 transition-all">
                            <option value="" disabled selected>-- Pilih Tanggal --</option>
                            <option value="today">Hari Ini</option>
                            <option value="this_week">Minggu Ini</option>
                            <option value="this_month">Bulan Ini</option>
                            <option value="custom">Pilih Tanggal Manual...</option>
                        </select>

                        <input type="text" id="customDate" class="absolute inset-0 opacity-0 pointer-events-none">
                    </div>
                </div>

                <div class="flex justify-end pt-2 border-t border-gray-100/70">
                    <button id="btnApplyFilter" onclick="applyFilterAndFetch()" disabled
                        class="px-6 py-3 bg-[#0d9488] hover:bg-teal-700 text-white font-bold rounded-xl shadow-md shadow-teal-600/10 transition-all text-sm disabled:opacity-40 disabled:cursor-not-allowed disabled:shadow-none flex items-center gap-2">
                        <i class="fas fa-search text-xs"></i> Terapkan Kueri Pencarian
                    </button>
                </div>
            </div>

            {{-- TABEL AUDIT --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left table-audit">
                    <thead>
                        <tr>
                            <th class="w-[20%]">Waktu & ID Log</th>
                            <th class="w-[20%]">Tipe Entitas</th>
                            <th class="w-[20%] text-center">Aktivitas</th>
                            <th class="w-[40%]">Cuplikan Deskripsi</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                    </tbody>
                </table>
            </div>

            {{-- AREA LOAD MORE --}}
            <div class="p-6 border-t border-gray-100 bg-gray-50 flex flex-col items-center justify-center min-h-[100px]">
                <button id="btnLoadMore" onclick="loadMore()"
                    class="hidden px-6 py-2.5 bg-white border border-gray-200 hover:bg-gray-100 text-gray-700 font-bold rounded-xl shadow-sm transition-all text-sm flex items-center gap-2">
                    <span>Muat Lebih Banyak</span>
                    <i class="fas fa-chevron-down text-xs"></i>
                </button>
                <div id="loadingIndicator" class="hidden flex flex-col items-center">
                    <i class="fas fa-circle-notch fa-spin text-[#0d9488] text-2xl mb-2"></i>
                    <span class="text-sm text-gray-500 font-medium">Mengambil data...</span>
                </div>
                <div id="endMessage" class="hidden text-sm text-gray-400 font-medium">
                    — Semua data telah ditampilkan —
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL DETAIL DESKRIPSI --}}
    <div id="detailModal"
        class="fixed inset-0 z-50 hidden items-center justify-center bg-gray-900/40 backdrop-blur-sm p-4 transition-opacity">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden flex flex-col modal-enter"
            id="modalContainer">
            <div class="flex justify-between items-center p-5 border-b border-gray-100 bg-gray-50/50">
                <h3 class="font-bold text-gray-900 font-['Plus_Jakarta_Sans']">Detail Log Aktivitas</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
            <div class="p-6">
                <div class="mb-4 flex gap-3">
                    <span id="modalAction"
                        class="inline-flex px-3 py-1 text-xs font-extrabold uppercase rounded-lg border"></span>
                    <span id="modalType"
                        class="inline-flex px-3 py-1 bg-gray-100 text-gray-700 text-xs font-bold uppercase rounded-lg border border-gray-200"></span>
                </div>
                <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Deskripsi Lengkap</h4>
                <div id="modalDescription"
                    class="text-sm text-gray-700 bg-gray-50 p-4 rounded-xl border border-gray-100 leading-relaxed whitespace-pre-wrap">
                </div>
                <div class="mt-4 text-xs text-gray-400 font-medium">
                    ID Log: <span id="modalLogId"></span> &nbsp;|&nbsp; Waktu: <span id="modalTime"></span>
                </div>
            </div>
            <div class="p-4 border-t border-gray-100 bg-gray-50 flex justify-end">
                <button onclick="closeModal()"
                    class="px-5 py-2 bg-white border border-gray-200 hover:bg-gray-100 text-gray-700 font-bold rounded-xl text-sm transition-all">Tutup</button>
            </div>
        </div>
    </div>

    <script>
        // ==========================================
        // INISIALISASI & STATE
        // ==========================================
        let currentCursor = null;
        let hasMore = true;
        let isFetching = false;
        let isFilterMode = false;
        let selectedCustomDateValue = ""; // Menyimpan string tanggal dari Flatpickr

        // DOM Elements
        const tableBody = document.getElementById('tableBody');
        const btnLoadMore = document.getElementById('btnLoadMore');
        const loadingIndicator = document.getElementById('loadingIndicator');
        const endMessage = document.getElementById('endMessage');
        const btnApplyFilter = document.getElementById('btnApplyFilter');
        const filterDateSelect = document.getElementById('filterDate');

        // Instance Flatpickr Tersembunyi
        let customDatePicker = flatpickr("#customDate", {
            mode: "range",
            dateFormat: "Y-m-d",
            locale: "id",
            onClose: function(selectedDates, dateStr, instance) {
                if (dateStr) {
                    selectedCustomDateValue = dateStr;

                    // Membuat opsi kustom sementara atau memperbarui teks opsi 'custom'
                    let customOption = filterDateSelect.querySelector('option[value="custom"]');
                    customOption.text = `${dateStr}`;
                    filterDateSelect.value = "custom";
                } else {
                    // Jika ditutup tanpa memilih tanggal, reset select kembali ke default placeholder
                    resetCustomOptionText();
                    filterDateSelect.value = "today";
                }
                validateFilterForm();
            }
        });

        // Load data awal saat komponen dibuka
        document.addEventListener('DOMContentLoaded', () => {
            fetchLogs(true);
        });

        // ==========================================
        // LOGIKA PANEL FILTER & VALIDASI KETAT
        // ==========================================
        function toggleFilter() {
            const panel = document.getElementById('filterPanel');
            const btnSpan = document.querySelector('#btnToggleFilter span');
            if (panel.classList.contains('filter-hidden')) {
                panel.classList.remove('filter-hidden');
                panel.classList.add('filter-visible');
                btnSpan.innerText = "Tutup Filter Pencarian";
            } else {
                panel.classList.add('filter-hidden');
                panel.classList.remove('filter-visible');
                btnSpan.innerText = "Buka Filter Pencarian";
            }
        }

        function handleDateSelect() {
            if (filterDateSelect.value === 'custom') {
                // Otomatis memicu kalender melayang keluar tanpa merusak susunan grid input
                customDatePicker.open();
            } else {
                selectedCustomDateValue = "";
                resetCustomOptionText();
                validateFilterForm();
            }
        }

        function resetCustomOptionText() {
            let customOption = filterDateSelect.querySelector('option[value="custom"]');
            customOption.text = "Pilih Tanggal Manual...";
        }

        function validateFilterForm() {
            const typeVal = document.getElementById('filterType').value;
            const actionVal = document.getElementById('filterAction').value;
            const dateVal = filterDateSelect.value;

            // Form valid hanya jika semua komponen 3 filter terisi nilai pasti (Exact Value)
            let isValid = (typeVal !== "" && actionVal !== "" && dateVal !== "");

            // Jika memilih custom, wajib memastikan string range tanggal flatpickr tidak kosong
            if (dateVal === 'custom' && selectedCustomDateValue === "") {
                isValid = false;
            }

            btnApplyFilter.disabled = !isValid;
        }

        function applyFilterAndFetch() {
            isFilterMode = true;
            toggleFilter();
            fetchLogs(true);
        }

        // ==========================================
        // LOGIKA FETCH AXIOS & CURSOR PAGINATION
        // ==========================================
        function fetchLogs(reset = false) {
            if (isFetching) return;
            isFetching = true;

            if (reset) {
                currentCursor = null;
                tableBody.innerHTML = '';
                btnLoadMore.classList.add('hidden');
                endMessage.classList.add('hidden');
            }

            loadingIndicator.classList.remove('hidden');
            btnLoadMore.classList.add('hidden');

            const params = new URLSearchParams();

            if (isFilterMode) {
                params.append('type', document.getElementById('filterType').value);
                params.append('action', document.getElementById('filterAction').value);
                params.append('date', document.getElementById('filterDate').value);

                if (document.getElementById('filterDate').value === 'custom') {
                    params.append('custom_date', selectedCustomDateValue);
                }
            }

            if (currentCursor) params.append('cursor', currentCursor);

            axios.get(`${window.location.pathname}?${params.toString()}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    const logs = response.data.data;
                    currentCursor = response.data.next_cursor;
                    hasMore = response.data.has_more;

                    renderTableRows(logs);

                    loadingIndicator.classList.add('hidden');
                    if (hasMore) {
                        btnLoadMore.classList.remove('hidden');
                    } else if (logs.length > 0) {
                        endMessage.classList.remove('hidden');
                    } else {
                        tableBody.innerHTML =
                            `<tr><td colspan="4" class="text-center py-12 text-gray-400 font-medium bg-white">Tidak ada log yang sesuai dengan kriteria filter Anda.</td></tr>`;
                    }
                })
                .catch(error => {
                    console.error('Terjadi kesalahan:', error);
                    loadingIndicator.classList.add('hidden');
                })
                .finally(() => {
                    isFetching = false;
                });
        }

        function loadMore() {
            if (hasMore) fetchLogs(false);
        }

        // ==========================================
        // RENDER ROW TABEL & MODAL DETAIL LOGS
        // ==========================================
        function renderTableRows(logs) {
            logs.forEach(log => {
                const dateObj = new Date(log.created_at);
                const dateStr = dateObj.toLocaleDateString('id-ID', {
                    day: 'numeric',
                    month: 'short',
                    year: 'numeric'
                });
                const timeStr = dateObj.toLocaleTimeString('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit'
                }) + ' WIB';

                const badgeAction = getActionBadge(log.action);
                const shortDesc = log.description ? (log.description.length > 50 ? log.description.substring(0,
                    50) + '...' : log.description) : '-';
                const logDataStr = encodeURIComponent(JSON.stringify(log));

                const tr = `
                    <tr onclick="openModal('${logDataStr}')" class="transition-colors">
                        <td>
                            <p class="text-sm font-bold text-gray-900 m-0">${dateStr}</p>
                            <p class="text-xs text-gray-500 m-0">${timeStr} &bull; ID: #${log.id}</p>
                        </td>
                        <td><span class="text-sm font-bold text-gray-800 capitalize">${log.type}</span></td>
                        <td class="text-center">${badgeAction}</td>
                        <td><span class="text-xs text-gray-600 bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-100 leading-snug inline-block">${shortDesc}</span></td>
                    </tr>
                `;
                tableBody.insertAdjacentHTML('beforeend', tr);
            });
        }

        function getActionBadge(action) {
            if (action === 'create')
            return `<span class="inline-flex px-2.5 py-1 bg-green-50 text-green-600 text-[10px] font-extrabold uppercase rounded-lg border border-green-100">Create</span>`;
            if (action === 'update')
            return `<span class="inline-flex px-2.5 py-1 bg-blue-50 text-blue-600 text-[10px] font-extrabold uppercase rounded-lg border border-blue-100">Update</span>`;
            if (action === 'delete')
            return `<span class="inline-flex px-2.5 py-1 bg-rose-50 text-rose-600 text-[10px] font-extrabold uppercase rounded-lg border border-rose-100">Delete</span>`;
            return `<span class="inline-flex px-2.5 py-1 bg-gray-50 text-gray-600 text-[10px] font-extrabold uppercase rounded-lg border border-gray-200">${action}</span>`;
        }

        const modal = document.getElementById('detailModal');
        const modalContainer = document.getElementById('modalContainer');

        function openModal(encodedData) {
            const log = JSON.parse(decodeURIComponent(encodedData));
            document.getElementById('modalLogId').innerText = `#${log.id}`;
            document.getElementById('modalTime').innerText = new Date(log.created_at).toLocaleString('id-ID');
            document.getElementById('modalType').innerText = log.type;
            document.getElementById('modalAction').outerHTML = getActionBadge(log.action).replace('id=""',
                'id="modalAction"').replace('text-[10px]', 'text-xs');
            document.getElementById('modalDescription').innerText = log.description || 'Tidak ada detail deskripsi.';

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                modalContainer.classList.remove('modal-enter');
                modalContainer.classList.add('modal-enter-active');
            }, 10);
        }

        function closeModal() {
            modalContainer.classList.remove('modal-enter-active');
            modalContainer.classList.add('modal-leave-active');
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                modalContainer.classList.remove('modal-leave-active');
                modalContainer.classList.add('modal-enter');
            }, 200);
        }

        modal.addEventListener('click', function(e) {
            if (e.target === modal) closeModal();
        });
    </script>
@endsection
