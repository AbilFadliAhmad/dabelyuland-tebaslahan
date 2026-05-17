@extends('layouts.admin')

@section('style')
<style>
    /* Styling khusus untuk Tabel Member */
    .table-member th {
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
    .table-member td {
        padding: 1rem 1.5rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
    }
    .table-member tbody tr {
        transition: all 0.2s ease;
    }
    .table-member tbody tr:hover {
        background-color: #f8fafc;
    }
    
    /* Transisi Modal */
    #upgradeModal { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
    
    /* Kustomisasi SweetAlert */
    .swal2-popup {
        border-radius: 1.5rem !important;
        font-family: 'Inter', sans-serif;
    }
</style>
{{-- Include SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection

@section('content')
<div class="w-full min-h-screen p-4 sm:p-6 lg:p-8 font-['Inter'] bg-gray-50/50">

    {{-- ==========================================================
         HEADER AREA & STATISTIK SINGKAT
         ========================================================== --}}
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6 mb-8">
        <div>
            <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900 m-0 font-['Plus_Jakarta_Sans'] tracking-tight">
                Manajemen Pelanggan
            </h2>
            <p class="text-sm text-gray-500 m-0 mt-2 leading-relaxed max-w-2xl">
                Pantau daftar agen, cek status langganan, dan kelola tingkatan membership mereka.
            </p>
        </div>
        
        {{-- Statistik Singkat --}}
        <div class="flex items-center gap-3 overflow-x-auto pb-2 lg:pb-0 hide-scrollbar">
            <div class="bg-white px-5 py-3 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-3 shrink-0">
                <div class="w-10 h-10 rounded-full bg-amber-50 text-amber-500 flex items-center justify-center"><i class="fas fa-crown"></i></div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider m-0">Total Premium</p>
                    <p class="text-lg font-extrabold text-gray-900 m-0 leading-none mt-0.5">{{ $totalPremium ?? 0 }} <span class="text-xs font-medium text-gray-500">Agen</span></p>
                </div>
            </div>
            <div class="bg-white px-5 py-3 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-3 shrink-0">
                <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center"><i class="fas fa-users"></i></div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider m-0">Total Agen</p>
                    <p class="text-lg font-extrabold text-gray-900 m-0 leading-none mt-0.5">{{ $totalUsers ?? 0 }} <span class="text-xs font-medium text-gray-500">Agen</span></p>
                </div>
            </div>
        </div>
    </div>

    {{-- ==========================================================
         TABEL DAFTAR MEMBER
         ========================================================== --}}
    <div class="bg-white rounded-3xl shadow-[0_4px_25px_rgba(0,0,0,0.03)] border border-gray-100 overflow-hidden">
        
        {{-- ToolBar Tabel (Search & Filter) --}}
        <div class="p-5 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-4 bg-gray-50/30">
            <div class="relative w-full sm:w-72">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" id="searchUser" class="w-full pl-11 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-[#0d9488] focus:ring-2 focus:ring-[#0d9488]/20 transition-all" placeholder="Cari nama atau email agen...">
            </div>
            
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <select id="filterStatus" class="w-full sm:w-auto px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-600 font-medium focus:outline-none focus:border-[#0d9488] transition-all cursor-pointer appearance-none pr-10" style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%236B7280%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 1rem top 50%; background-size: 0.65rem auto;">
                    <option value="all">Semua Status</option>
                    <option value="gold">Gold Eksklusif</option>
                    <option value="silver">Silver Pro</option>
                    <option value="bronze">Bronze (Free)</option>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left table-member">
                <thead>
                    <tr>
                        <th class="w-[35%]">Profil Agen</th>
                        <th class="w-[20%] text-center">Status Membership</th>
                        <th class="w-[20%] text-center">Upload / Kuota</th>
                        <th class="w-[25%] text-right pr-6">Aksi Admin</th>
                    </tr>
                </thead>
                <tbody id="userTableBody">
                   @forelse($users as $user)
                        @php
                            $status = strtolower($user->membership_status ?? 'bronze');
                            // Logika penentuan kuota berdasarkan status di database
                            if ($status == 'gold') {
                                $quotaMax = 'Tanpa Batas';
                                $limitValue = 999999; 
                            } elseif ($status == 'silver') {
                                $quotaMax = 50;
                                $limitValue = 50;
                            } else {
                                $quotaMax = 10; // Sesuai permintaan Anda: Maksimal 10
                                $limitValue = 10;
                            }
                            
                            $uploadCount = $user->properties_count; // Asumsi ada relasi count
                            $progress = ($uploadCount / $limitValue) * 100;
                        @endphp
                        <tr class="user-row" data-status="{{ $status }}" data-name="{{ $user->name }}">
                            <td>{{ $user->name }}</td>
                            <td class="text-center">{{ ucfirst($status) }}</td>
                            <td class="text-center">{{ $uploadCount }} / {{ $quotaMax }}</td>
                            <td class="text-right">
                                <button onclick="openUpgradeModal({{ $user->id }}, '{{ $user->name }}')">Ubah Status</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4">Tidak ada agen terdaftar.</td></tr>
                    @endforelse
                </tbody>
            </table>
            
            {{-- Empty State Pencarian --}}
            <div id="emptySearch" class="hidden p-10 text-center flex-col items-center justify-center">
                <div class="w-16 h-16 bg-gray-50 text-gray-400 rounded-full flex items-center justify-center text-2xl mx-auto mb-4">
                    <i class="fas fa-search"></i>
                </div>
                <h5 class="text-lg font-bold text-gray-900 m-0">Agen tidak ditemukan</h5>
                <p class="text-sm text-gray-500 mt-1">Coba gunakan kata kunci pencarian yang lain.</p>
            </div>
        </div>

        {{-- Pagination (Placeholder) --}}
        <div class="p-4 border-t border-gray-100 bg-white flex justify-between items-center text-sm text-gray-500">
            <span>.</span>
            <div class="flex gap-1">
                <button disabled class="px-3 py-1.5 rounded-lg border border-gray-200 bg-gray-50 text-gray-400 cursor-not-allowed">Seb.</button>
                <button class="px-3 py-1.5 rounded-lg border border-[#0d9488] bg-teal-50 text-[#0d9488] font-bold">1</button>
                <button class="px-3 py-1.5 rounded-lg border border-gray-200 hover:bg-gray-50 text-gray-600">2</button>
                <button class="px-3 py-1.5 rounded-lg border border-gray-200 hover:bg-gray-50 text-gray-600">Sel.</button>
            </div>
        </div>

    </div>
</div>

{{-- ==========================================================
     MODAL UPGRADE/DOWNGRADE STATUS MEMBER
     ========================================================== --}}
<div id="upgradeModal" class="fixed inset-0 z-[100] hidden items-center justify-center">
    <div id="modalBackdrop" class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm opacity-0 transition-opacity duration-300" onclick="closeUpgradeModal()"></div>
    
    <div id="modalBox" class="bg-white rounded-3xl shadow-2xl w-full max-w-md mx-4 relative z-10 opacity-0 scale-95 transition-all duration-300 transform font-['Inter'] overflow-hidden">
        
        <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-white">
            <h3 class="font-['Plus_Jakarta_Sans'] font-extrabold text-gray-900 text-lg m-0">
                Kelola Akses Agen
            </h3>
            <button type="button" onclick="closeUpgradeModal()" class="text-gray-400 hover:text-red-500 bg-gray-50 hover:bg-red-50 p-2 rounded-lg transition-colors focus:outline-none">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>

        <form id="formUpdateStatus" onsubmit="saveUserStatus(event)">
            <input type="hidden" id="inpUserId">
            
            <div class="p-6">
                {{-- Info Agen Saat Ini --}}
                <div class="flex items-center gap-4 mb-6 p-4 bg-gray-50 rounded-2xl border border-gray-100">
                    <div class="w-12 h-12 bg-white rounded-full border border-gray-200 flex items-center justify-center text-gray-400 shrink-0">
                        <i class="fas fa-user"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-bold uppercase tracking-wider m-0 mb-0.5">Nama Agen</p>
                        <h4 id="modUserName" class="text-base font-extrabold text-gray-900 m-0">Nama User</h4>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-[10px] text-gray-500">Status saat ini:</span>
                            <span id="modCurrentStatus" class="text-[10px] font-bold text-[#0d9488]">Silver</span>
                        </div>
                    </div>
                </div>

                {{-- Pilihan Level Baru --}}
                <div class="mb-5">
                    <label class="block text-sm font-bold text-gray-700 mb-3">Ubah Status Akun Menjadi:</label>
                    <select id="inpNewLevel" class="w-full px-4 py-3.5 bg-white border-2 border-gray-200 rounded-xl text-sm font-bold text-gray-800 focus:outline-none focus:border-[#0d9488] transition-all cursor-pointer">
                        <option value="bronze">Bronze (Paket Dasar Gratis)</option>
                        <option value="silver">Silver Pro</option>
                        <option value="gold">Gold Eksklusif</option>
                    </select>
                </div>

                {{-- Opsi Masa Aktif (Hanya muncul jika pilih Silver/Gold) --}}
                <div id="boxDuration" class="mb-2 transition-all">
                    <label class="block text-sm font-bold text-gray-700 mb-3">Perpanjang Masa Aktif:</label>
                    <select id="inpDuration" class="w-full px-4 py-3.5 bg-white border-2 border-gray-200 rounded-xl text-sm font-bold text-gray-800 focus:outline-none focus:border-[#0d9488] transition-all cursor-pointer">
                        <option value="1">1 Bulan</option>
                        <option value="3">3 Bulan</option>
                        <option value="6">6 Bulan</option>
                        <option value="12">1 Tahun</option>
                    </select>
                </div>

                {{-- Alert Peringatan --}}
                <div class="mt-5 p-3 bg-amber-50 border border-amber-200 rounded-lg flex items-start gap-3">
                    <i class="fas fa-exclamation-triangle text-amber-500 mt-0.5"></i>
                    <p class="text-xs text-amber-800 m-0 leading-relaxed">
                        Mengubah status secara manual akan meng-override sistem penagihan. Gunakan fitur ini untuk memberikan bonus/hadiah kepada agen.
                    </p>
                </div>

            </div>

            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/80 flex justify-end gap-3">
                <button type="button" onclick="closeUpgradeModal()" class="px-5 py-2.5 rounded-xl border border-gray-200 bg-white text-gray-600 font-bold text-sm hover:bg-gray-50 transition-colors shadow-sm">
                    Batal
                </button>
                <button type="submit" id="btnSaveStatus" class="px-6 py-2.5 rounded-xl bg-[#0d9488] hover:bg-teal-700 text-white font-bold text-sm shadow-md hover:-translate-y-0.5 transition-all flex items-center">
                    Simpan Perubahan
                </button>
            </div>
        </form>

    </div>
</div>
@endsection

@section('script')
<script>
    // ==========================================
    // LOGIKA PENCARIAN & FILTER TABEL SEDERHANA
    // ==========================================
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchUser');
        const filterSelect = document.getElementById('filterStatus');
        const rows = document.querySelectorAll('.user-row');
        const emptyState = document.getElementById('emptySearch');

        function filterTable() {
            const searchTerm = searchInput.value.toLowerCase();
            const filterValue = filterSelect.value.toLowerCase();
            let visibleCount = 0;

            rows.forEach(row => {
                const name = row.getAttribute('data-name').toLowerCase();
                const email = row.getAttribute('data-email').toLowerCase();
                const status = row.getAttribute('data-status').toLowerCase();

                const matchesSearch = name.includes(searchTerm) || email.includes(searchTerm);
                const matchesFilter = filterValue === 'all' || status === filterValue;

                if (matchesSearch && matchesFilter) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            if (visibleCount === 0) {
                emptyState.classList.remove('hidden');
                emptyState.classList.add('flex');
            } else {
                emptyState.classList.add('hidden');
                emptyState.classList.remove('flex');
            }
        }

        searchInput.addEventListener('input', filterTable);
        filterSelect.addEventListener('change', filterTable);
    });

    // ==========================================
    // LOGIKA MODAL UBAH STATUS
    // ==========================================
    const modal = document.getElementById('upgradeModal');
    const backdrop = document.getElementById('modalBackdrop');
    const modalBox = document.getElementById('modalBox');
    
    const inpNewLevel = document.getElementById('inpNewLevel');
    const boxDuration = document.getElementById('boxDuration');

    // Tampilkan/Sembunyikan Pilihan Durasi berdasarkan Paket
    inpNewLevel.addEventListener('change', function() {
        if(this.value === 'bronze') {
            boxDuration.style.display = 'none';
            boxDuration.style.opacity = '0';
        } else {
            boxDuration.style.display = 'block';
            setTimeout(() => boxDuration.style.opacity = '1', 10);
        }
    });

    function openUpgradeModal(id, name, currentStatus, expiredDate) {
        // Isi Data
        document.getElementById('inpUserId').value = id;
        document.getElementById('modUserName').innerText = name;
        document.getElementById('modCurrentStatus').innerText = currentStatus;

        // Auto-select dropdown berdasarkan status saat ini (disimplifikasi)
        let statusVal = 'bronze';
        if(currentStatus.toLowerCase().includes('gold')) statusVal = 'gold';
        if(currentStatus.toLowerCase().includes('silver')) statusVal = 'silver';
        
        inpNewLevel.value = statusVal;
        
        // Trigger event change untuk menyembunyikan/menampilkan durasi
        inpNewLevel.dispatchEvent(new Event('change'));

        // Buka Modal
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        setTimeout(() => {
            backdrop.classList.remove('opacity-0');
            modalBox.classList.remove('opacity-0', 'scale-95');
            modalBox.classList.add('opacity-100', 'scale-100');
        }, 10);
        
        document.body.style.overflow = 'hidden';
    }

    function closeUpgradeModal() {
        backdrop.classList.add('opacity-0');
        modalBox.classList.remove('opacity-100', 'scale-100');
        modalBox.classList.add('opacity-0', 'scale-95');
        
        setTimeout(() => {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }, 300);
    }

    // Simulasi Save (AJAX Template)
    function saveUserStatus(e) {
        e.preventDefault();
        const btn = document.getElementById('btnSaveStatus');
        const originalText = btn.innerHTML;
        
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Memproses...';
        btn.disabled = true;

        // SIMULASI PROSES KE BACKEND
        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
            closeUpgradeModal();

            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Status akun agen berhasil diperbarui.',
                confirmButtonColor: '#0d9488',
                confirmButtonText: 'Tutup'
            });
            
            // Di sistem nyata, lakukan window.location.reload() atau perbarui baris tabel via JS
        }, 1000);
    }
</script>
@endsection