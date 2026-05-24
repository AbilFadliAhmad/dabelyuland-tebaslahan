@extends('layouts.admin')

@section('content')
    <div class="p-6 md:p-8 font-['Inter']">
        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 font-['Plus_Jakarta_Sans']">Manajemen Quest</h1>
                <p class="text-sm text-gray-500 mt-1">Pantau dan sesuaikan daftar misi serta hadiah koin untuk agen.</p>
            </div>
        </div>

        {{-- Alert --}}
        @if (session('success'))
            <div
                class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm font-semibold flex items-center gap-3">
                <i class="fas fa-check-circle text-lg"></i> {{ session('success') }}
            </div>
        @endif

        {{-- Tabel --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600 font-semibold border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4">Judul Misi</th>
                            <th class="px-6 py-4">Tipe Misi</th>
                            <th class="px-6 py-4">Target Awal</th>
                            <th class="px-6 py-4">Hadiah Koin</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($quests as $quest)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <span class="font-bold text-gray-800">{{ $quest->title }}</span>
                                    <div class="text-xs text-gray-500 mt-1 truncate max-w-xs">{{ $quest->code }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-700">
                                        {{ $quest->type == 'daily' ? 'Harian (Daily)' : 'Berkelanjutan (Progressive)' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-bold text-gray-700">{{ $quest->base_target_amount }}x</td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 bg-amber-100 text-amber-700 font-bold rounded-full text-xs">
                                        +{{ $quest->base_reward_coins }} Koin
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if ($quest->is_active)
                                        <span
                                            class="px-2.5 py-1 bg-green-100 text-green-700 text-[11px] font-bold rounded-md">Aktif</span>
                                    @else
                                        <span
                                            class="px-2.5 py-1 bg-red-100 text-red-700 text-[11px] font-bold rounded-md">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    {{-- Tombol Edit --}}
                                    <button type="button"
                                        onclick="openEditModal({{ $quest->id }}, '{{ addslashes($quest->title) }}', {{ $quest->base_target_amount }}, {{ $quest->base_reward_coins }}, {{ $quest->is_active }})"
                                        class="w-8 h-8 rounded-lg bg-blue-50 text-blue-500 hover:bg-blue-600 hover:text-white transition-colors flex items-center justify-center mx-auto">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500 font-medium">Belum ada data
                                    misi/quest.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal Edit Misi --}}
    <div id="modalEdit" class="fixed inset-0 z-[9999] hidden flex items-center justify-center p-4 transition-opacity">

        {{-- Area Gelap (Overlay / Background) --}}
        {{-- Fungsi onclick di sini akan menutup modal saat area gelap ini diklik --}}
        <div class="absolute inset-0 bg-black/60" onclick="closeEditModal()"></div>

        {{-- Kotak Putih Konten (Modal Box) --}}
        {{-- Menggunakan relative dan z-10 agar posisinya di atas overlay gelap --}}
        <div class="relative z-10 bg-white w-full max-w-md rounded-2xl shadow-2xl overflow-hidden transform transition-all">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <h3 class="font-bold text-gray-900 font-['Plus_Jakarta_Sans'] text-lg">Edit Misi</h3>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-red-500 transition-colors">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <form id="editQuestForm" method="POST" class="p-6">
                @csrf
                @method('PATCH')

                <div class="space-y-5 text-sm">
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1.5">Judul Misi</label>
                        <input type="text" id="edit_title" name="title" required
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-[#0d9488] focus:ring-2 focus:ring-[#0d9488]/20 focus:outline-none transition-all">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1.5">Target Jumlah</label>
                            <input type="number" id="edit_target" name="base_target_amount" required min="1"
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-[#0d9488] focus:ring-2 focus:ring-[#0d9488]/20 focus:outline-none transition-all">
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1.5">Hadiah Koin</label>
                            <input type="number" id="edit_coins" name="base_reward_coins" required min="0"
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-[#0d9488] focus:ring-2 focus:ring-[#0d9488]/20 focus:outline-none transition-all">
                        </div>
                    </div>

                    <div class="flex items-center gap-3 mt-4 p-3 bg-gray-50 rounded-xl border border-gray-100">
                        <input type="checkbox" id="edit_is_active" name="is_active"
                            class="w-4 h-4 text-[#0d9488] rounded border-gray-300 focus:ring-[#0d9488]">
                        <label for="edit_is_active" class="font-medium text-gray-700 cursor-pointer">Status Misi
                            Aktif</label>
                    </div>
                </div>

                <div class="mt-8 flex gap-3">
                    <button type="button" onclick="closeEditModal()"
                        class="w-full py-2.5 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 font-bold rounded-xl transition-colors">Batal</button>
                    <button type="submit"
                        class="w-full py-2.5 bg-[#0d9488] hover:bg-teal-700 text-white font-bold rounded-xl transition-colors shadow-md shadow-teal-700/20">Simpan
                        Perubahan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>
        function openEditModal(id, title, target, coins, isActive) {
            // Atur URL action form secara dinamis
            let baseUrl = "{{ route('admin.quest.update', ':id') }}";
            document.getElementById('editQuestForm').action = baseUrl.replace(':id', id);

            // Isi nilai ke dalam input form
            document.getElementById('edit_title').value = title;
            document.getElementById('edit_target').value = target;
            document.getElementById('edit_coins').value = coins;
            document.getElementById('edit_is_active').checked = isActive === 1 ? true : false;

            // Tampilkan modal
            document.getElementById('modalEdit').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('modalEdit').classList.add('hidden');
        }
    </script>
@endsection
