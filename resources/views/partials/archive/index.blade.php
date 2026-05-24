@extends('layouts.admin')

@section('style')
    <style>
        .table-archive th {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.70rem;
            letter-spacing: 0.05em;
            color: #64748b;
            background-color: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
            padding: 1rem 1.5rem;
        }

        .table-archive td {
            padding: 1rem 1.5rem;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
        }

        .filter-tab.active {
            background-color: #0d9488;
            color: white;
            border-color: #4f46e5;
        }
    </style>
@endsection

@section('content')
    <div class="w-full min-h-screen p-4 sm:p-6 lg:p-8 font-['Inter'] bg-gray-50/50">

        {{-- HEADER --}}
        <div class="mb-4">
            <h2 class="text-2xl font-bold text-gray-900 m-0">
                Arsip Properti
            </h2>
            <p class="text-sm text-gray-500 m-0 mt-1">Pantau data properti yang sudah terjual, ditolak, atau dihapus dari
                sistem.</p>
        </div>

        {{-- FILTER & SEARCH --}}
        <div class="flex flex-col md:flex-row gap-4 mb-6 justify-between items-end">
            {{-- Tabs Filter --}}
            <div class="flex p-1 bg-gray-200/50 rounded-2xl w-full md:w-auto">
                <button onclick="filterStatus('all')"
                    class="filter-tab active px-4 py-2 rounded-xl text-xs font-bold transition-all"
                    data-status="all">Semua</button>
                <button onclick="filterStatus('terjual')"
                    class="filter-tab px-4 py-2 rounded-xl text-xs font-bold transition-all text-gray-600"
                    data-status="terjual">Terjual</button>
                <button onclick="filterStatus('dihapus')"
                    class="filter-tab px-4 py-2 rounded-xl text-xs font-bold transition-all text-gray-600"
                    data-status="dihapus">Dihapus</button>
                <button onclick="filterStatus('ditolak')"
                    class="filter-tab px-4 py-2 rounded-xl text-xs font-bold transition-all text-gray-600"
                    data-status="ditolak">Ditolak</button>
            </div>

            {{-- Search by Agent --}}
            <div class="relative w-full md:w-80">
                <i class="fas fa-user-tie absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" id="searchAgent"
                    class="w-full pl-11 pr-4 py-2.5 bg-white border border-gray-200 rounded-2xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all shadow-sm"
                    placeholder="Cari berdasarkan nama agen...">
            </div>
        </div>

        {{-- TABEL Sampah --}}
        <div class="bg-white rounded-3xl shadow-[0_4px_25px_rgba(0,0,0,0.03)] border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left table-archive">
                    <thead>
                        <tr>
                            <th class="w-[30%]">Info Properti</th>
                            <th class="w-[15%]">Nama Agen</th>
                            <th class="w-[15%] text-center">Status Akhir</th>
                            <th class="w-[25%]">Catatan / Alasan</th>
                            <th class="w-[15%] text-right pr-6">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="archiveTableBody">
                        @forelse($trashedProperties as $property)
                            @include('partials.archive.row', ['property' => $property])
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-12">
                                    <div class="flex flex-col items-center justify-center text-gray-500">
                                        <i class="bi bi-archive mb-3 text-[3rem] text-slate-300"></i>
                                        <h6 class="font-bold text-gray-700 mb-1">Belum ada Properti yang diarsipkan</h6>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                {{-- Container Show More --}}
                <div id="showMoreContainer"
                    class="p-4 text-center {{ $trashedProperties->hasMorePages() ? '' : 'hidden' }}">
                    <button onclick="loadMore()" class="text-sm font-bold text-indigo-600 hover:text-indigo-800">
                        Lihat Lebih Banyak <i class="fas fa-chevron-down ml-1"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        let currentPage = 1;
        let currentStatus = 'all';
        let currentAgent = '';
        let searchTimer; // Untuk debounce pencarian

        // Filtering Status & Search by Agent Name
        async function fetchProperties(isNewFilter = false) {
            const tableBody = document.getElementById('archiveTableBody');
            const showMoreBtn = document.getElementById('showMoreContainer');

            if (isNewFilter) {
                currentPage = 1; // Reset halaman jika filter berubah
                tableBody.innerHTML =
                    '<tr><td colspan="5" class="text-center py-10"><i class="fas fa-spinner fa-spin mr-2"></i>Memuat data...</td></tr>';
            }

            // Bangun URL dengan parameter query
            let url =
                `{{ route('account.properties.archive') }}?page=${currentPage}&status=${currentStatus}&agent=${currentAgent}&is_ajax=1`;
            try {
                const response = await fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await response.json();

                if (isNewFilter) {
                    tableBody.innerHTML = data.html; // Ganti seluruh isi jika filter/search baru
                } else {
                    tableBody.insertAdjacentHTML('beforeend', data.html); // Tambahkan elemen ke bawah
                }

                // Kontrol tampilan tombol "Show More"
                if (data.hasMore) {
                    showMoreBtn.classList.remove('hidden');
                } else {
                    showMoreBtn.classList.add('hidden');
                }

            } catch (error) {
                console.error(error);
                tableBody.innerHTML =
                    '<tr><td colspan="5" class="text-center py-10 text-rose-500">Gagal memuat data.</td></tr>';
            }
        }

        // Trigger saat tab status diklik[cite: 4]
        function filterStatus(status) {
            currentStatus = status;

            // Update tampilan tab aktif
            document.querySelectorAll('.filter-tab').forEach(tab => {
                tab.classList.remove('active', 'bg-indigo-600', 'text-white');
                tab.classList.add('text-gray-600');
                if (tab.getAttribute('data-status') === status) {
                    tab.classList.add('active', 'bg-indigo-600', 'text-white');
                }
            });

            fetchProperties(true);
        }

        // Trigger pencarian Agen (Debounce 500ms)[cite: 3]
        const searchInput = document.getElementById('searchAgent');
        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                clearTimeout(searchTimer);
                currentAgent = e.target.value;
                searchTimer = setTimeout(() => {
                    fetchProperties(true);
                }, 500); // Menunggu 0.5 detik setelah berhenti mengetik
            });
        }

        // Trigger untuk tombol Lihat Lebih Banyak[cite: 3]
        function loadMore() {
            currentPage++;
            fetchProperties(false); // false artinya data akan ditambahkan ke bawah (append)
        }

        function confirmAction(button, type) {
            let isDelete = type === 'delete';
            Swal.fire({
                title: isDelete ? 'Hapus Permanen?' : 'Pulihkan Properti?',
                text: isDelete ? 'Data ini akan hilang selamanya!' : 'Properti akan dikembalikan ke daftar aktif.',
                icon: isDelete ? 'warning' : 'question',
                showCancelButton: true,
                confirmButtonColor: isDelete ? '#ef4444' : '#4f46e5',
                confirmButtonText: isDelete ? 'Ya, Hapus!' : 'Ya, Pulihkan!',
                customClass: {
                    popup: 'rounded-3xl',
                    confirmButton: 'rounded-xl',
                    cancelButton: 'rounded-xl'
                }
            }).then((result) => {
                if (result.isConfirmed) button.closest('form').submit();
            });
        }

        function showReasonDetail(judul, reason, status) {
            // Tentukan ikon dan warna berdasarkan status
            let iconColor = '#f43f5e'; // Default Rose (Dihapus)
            let headerText = 'Catatan Penghapusan';

            if (status === 'terjual') {
                iconColor = '#10b981'; // Emerald (Terjual)
                headerText = 'Detail Penjualan';
            } else if (status === 'ditolak') {
                iconColor = '#f59e0b'; // Amber (Ditolak)
                headerText = 'Alasan Penolakan';
            }

            Swal.fire({
                title: `<span class="text-lg font-bold text-gray-800">${headerText}</span>`,
                html: `
            <div class="text-left mt-2">
                <div class="p-3 bg-gray-50 rounded-xl border border-gray-100 mb-4">
                    <p class="text-[10px] uppercase font-bold text-gray-400 mb-1">Properti</p>
                    <p class="text-sm font-semibold text-gray-700">${judul}</p>
                </div>
                <div class="px-1">
                    <p class="text-[10px] uppercase font-bold text-gray-400 mb-2">Keterangan Lengkap:</p>
                    <div class="text-sm leading-relaxed text-gray-600 italic">
                        "${reason}"
                    </div>
                </div>
            </div>
        `,
                icon: 'info',
                iconColor: iconColor,
                confirmButtonText: 'Tutup',
                confirmButtonColor: '#4f46e5',
                customClass: {
                    popup: 'rounded-3xl p-6 shadow-xl',
                    confirmButton: 'rounded-xl px-8 py-2.5 font-bold text-sm'
                }
            });
        }
    </script>
@endsection
