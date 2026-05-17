    @extends('layouts.admin')

    @section('style')
        <link rel="stylesheet" href="{{ asset('assets/vendors/sweetalert2/sweetalert2.min.css') }}">
        <style>
            /* Kustomisasi gaya bawaan Simple-Datatables agar serasi dengan Tailwind */
            .dataTable-wrapper {
                font-family: 'Inter', sans-serif !important;
            }

            .dataTable-input {
                border: 1px solid #e5e7eb !important;
                border-radius: 0.75rem !important;
                padding: 0.6rem 1rem !important;
                outline: none !important;
                font-size: 0.875rem !important;
            }

            .dataTable-input:focus {
                border-color: #0d9488 !important;
                box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.1) !important;
            }

            .dataTable-selector {
                border: 1px solid #e5e7eb !important;
                border-radius: 0.5rem !important;
                padding: 0.4rem 1.5rem 0.4rem 0.75rem !important;
            }

            .dataTable-table>thead>tr>th {
                border-bottom: 2px solid #e5e7eb !important;
                padding-bottom: 1rem !important;
            }

            .dataTable-table>tbody>tr>td {
                vertical-align: middle !important;
                padding: 1rem 1.25rem !important;
                border-bottom: 1px solid #f3f4f6 !important;
            }

            .dataTable-info {
                color: #6b7280 !important;
                font-size: 0.875rem !important;
            }

            .dataTable-pagination a {
                border: 1px solid #e5e7eb !important;
                border-radius: 0.375rem !important;
                color: #4b5563 !important;
            }

            .dataTable-pagination .active a {
                background-color: #0d9488 !important;
                color: white !important;
                border-color: #0d9488 !important;
            }
        </style>
    @endsection

    @section('content')
        <div class="min-h-screen bg-gray-50/50 p-6 font-['Inter']">
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 m-0">Manajemen Membership Agen</h2>
                <p class="text-sm text-gray-500 mt-1">Kelola status membership, koin, dan data profil agen Dabelyuland.</p>
            </div>

            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                {{-- ToolBar --}}
                <div class="p-5 border-b border-gray-100 flex flex-col md:flex-row justify-between gap-4 bg-gray-50/30">
                    <div class="relative w-full md:w-72">
                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text" id="searchAgent" oninput="debounceSearch()"
                            class="w-full pl-11 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:border-[#0d9488]"
                            placeholder="Cari nama atau email...">
                    </div>
                    <select id="filterMembership" onchange="applyFilters()"
                        class="px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-medium focus:border-[#0d9488] cursor-pointer">
                        <option value="all">Semua Membership</option>
                        @foreach ($memberships as $membership)
                            <option value="{{ $membership->id }}">{{ $membership->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50/80 border-b border-gray-100">
                                <th class="py-4 px-4 text-xs font-bold text-gray-500 uppercase">Agent</th>
                                <th class="py-4 px-4 text-xs font-bold text-gray-500 uppercase">Email</th>
                                <th class="py-4 px-4 text-xs font-bold text-gray-500 uppercase">WhatsApp</th>
                                <th class="py-4 px-4 text-xs font-bold text-gray-500 uppercase text-center">Membership</th>
                                <th class="py-4 px-4 text-xs font-bold text-gray-500 uppercase text-right pr-8">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="userTableBody">
                            @foreach ($users as $val)
                                @include('admin.user.row', ['val' => $val])
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Show More --}}
                <div id="loadMoreArea"
                    class="p-6 text-center border-t border-gray-50 {{ $users->hasMorePages() ? '' : 'hidden' }}">
                    <button onclick="loadMore()" class="text-sm font-bold text-[#0d9488] hover:underline">
                        Lihat Agen Lainnya <i class="fas fa-chevron-down ml-1"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- MODAL DETAIL AGENT --}}
        <div id="detailModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black/40 transition-opacity" onclick="closeModal()"></div>
                <div
                    class="bg-white rounded-3xl overflow-hidden shadow-2xl z-50 w-full max-w-md transform transition-all p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Detail & Wallet Agen</h3>

                    <div class="space-y-4">
                        {{-- Identitas Agen --}}
                        <div class="p-4 bg-indigo-50 rounded-2xl border border-indigo-100">
                            <p class="text-[10px] uppercase font-bold text-indigo-400 mb-1">Agent</p>
                            <p id="modalAgentName" class="text-lg font-extrabold text-indigo-900">-</p>
                        </div>

                        {{-- Detail Kuota (Read-Only) --}}
                        <div class="grid grid-cols-2 gap-3 p-4 bg-gray-50 rounded-2xl border border-gray-100">
                            <div class="flex flex-col">
                                <span class="text-[10px] font-bold text-gray-400 uppercase">Push Quota</span>
                                <span id="detailPush" class="text-sm font-bold text-gray-700">-</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-[10px] font-bold text-gray-400 uppercase">Banner Quota</span>
                                <span id="detailBanner" class="text-sm font-bold text-gray-700">-</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-[10px] font-bold text-gray-400 uppercase">Highlight Quota</span>
                                <span id="detailHighlight" class="text-sm font-bold text-gray-700">-</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-[10px] font-bold text-gray-400 uppercase">Recommendation</span>
                                <span id="detailRec" class="text-sm font-bold text-gray-700">-</span>
                            </div>
                        </div>

                        <form id="walletForm">
                            {{-- UserID --}}
                            <input type="hidden" id="modalUserId" />
                            {{-- Input yang Bisa Diubah --}}
                            <div class="grid grid-cols-2 gap-4 mb-6">
                                {{-- Input Koin --}}
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-2">Dabelyu Koin</label>
                                    <input type="number" name="koin" id="modalKoin"
                                        class="w-full h-11 px-4 bg-white border border-gray-200 rounded-xl text-sm font-bold focus:border-[#0d9488] focus:ring-2 focus:ring-[#0d9488]/10 outline-none transition-all">
                                </div>

                                {{-- Dropdown Membership --}}
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-2">Membership</label>
                                    <select name="membership" id="modalMembership"
                                        class="w-full h-11 px-4 bg-white border border-gray-200 rounded-xl text-sm font-bold focus:border-[#0d9488] outline-none cursor-pointer appearance-none"
                                        style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%236B7280%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 1rem top 50%; background-size: 0.65rem auto;">
                                        @foreach ($memberships as $membership)
                                            <option value="{{ $membership->id }}">{{ $membership->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- Action Buttons --}}
                            <div class="flex flex-col gap-2">
                                <button type="button" onclick="updateWallet()"
                                    class="w-full py-3 bg-[#0d9488] text-white rounded-xl font-bold shadow-lg shadow-teal-500/30 hover:bg-[#0b7a70] transition-all">
                                    Simpan Perubahan
                                </button>
                                <button type="button" onclick="closeModal()"
                                    class="w-full py-3 border-2 bg-gray-100 text-gray-500 rounded-xl font-bold hover:bg-gray-200 transition-all text-sm">
                                    Tutup
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @section('script')
        <script>
            let currentPage = 1;
            let searchTimer;

            function debounceSearch() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(() => applyFilters(true), 500);
            }

            function applyFilters(reset = true) {
                if (reset) currentPage = 1;

                let search = document.getElementById('searchAgent').value;
                let membership = document.getElementById('filterMembership').value;

                fetch(`{{ route('admin.list.users') }}?page=${currentPage}&search=${search}&membership=${membership}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        const body = document.getElementById('userTableBody');
                        if (reset) body.innerHTML = data.html;
                        else body.insertAdjacentHTML('beforeend', data.html);

                        document.getElementById('loadMoreArea').style.display = data.hasMore ? 'block' : 'none';
                    });
            }

            function loadMore() {
                currentPage++;
                applyFilters(false);
            }

            // SWEETALERT DELETE
            function confirmDelete(userId, userName) {
                Swal.fire({
                    title: 'Hapus Agen?',
                    text: `Data ${userName} akan dihapus permanen dari sistem!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#f3f4f6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: '<span class="text-gray-500">Batal</span>',
                    customClass: {
                        popup: 'rounded-3xl'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Jalankan AJAX delete atau submit form
                        window.location.href = `/admin/user/delete/${userId}`;
                    }
                });
            }

            // MODAL LOGIC
            function openDetailModal(id, name, koin, membershipId, push, banner, highlight, rec) {
                // Isi data identitas & editable
                document.getElementById('modalUserId').value = id;
                document.getElementById('modalAgentName').innerText = name;
                document.getElementById('modalKoin').value = koin;
                document.getElementById('modalMembership').value = membershipId;

                // Isi data detail kuota (Read-only)
                document.getElementById('detailPush').innerText = push + " Sisa Slot";
                document.getElementById('detailBanner').innerText = banner + " Slot";
                document.getElementById('detailHighlight').innerText = highlight + " Slot";
                document.getElementById('detailRec').innerText = rec + " Slot";

                document.getElementById('detailModal').classList.remove('hidden');
            }

            function closeModal() {
                document.getElementById('detailModal').classList.add('hidden');
            }

            async function updateWallet() {
                const userId = document.getElementById('modalUserId').value; // Pastikan ada input hidden ID di modal
                const koin = document.getElementById('modalKoin').value;
                const membershipId = document.getElementById('modalMembership').value;

                // Tampilkan loading SweetAlert2
                Swal.fire({
                    title: 'Memproses...',
                    text: 'Sedang mensinkronisasi kuota membership',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                try {
                    const response = await fetch(`{{ route('admin.update.wallet') }}`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content'),
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            koin: koin,
                            membership: membershipId,
                            id: userId
                        })
                    });

                    const result = await response.json();

                    if (result.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: result.message,
                            customClass: {
                                popup: 'rounded-3xl'
                            }
                        }).then(() => {
                            location.reload(); // Reload untuk melihat perubahan di tabel
                        });
                    } else {
                        Swal.fire('Gagal!', result.message, 'error');
                    }
                } catch (error) {
                    Swal.fire('Error!', 'Gagal menghubungi server.', 'error');
                }
            }
        </script>
    @endsection
