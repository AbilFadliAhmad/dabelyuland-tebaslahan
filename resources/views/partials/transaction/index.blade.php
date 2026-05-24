@extends('layouts.user')

@section('content')
    <div class="p-6 md:p-8 font-['Inter'] bg-[#FAFAFA] min-h-screen relative">
        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 font-['Plus_Jakarta_Sans'] tracking-tight">Riwayat
                Transaksi</h1>
            <p class="text-sm text-gray-500 mt-2">Daftar semua transaksi pembelian Koin dan Membership Anda.</p>
        </div>

        {{-- Notifikasi --}}
        @if (session('success'))
            <div
                class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm font-semibold flex items-center gap-3">
                <i class="fas fa-check-circle text-lg"></i> {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div
                class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm font-semibold flex items-center gap-3">
                <i class="fas fa-exclamation-circle text-lg"></i> {{ session('error') }}
            </div>
        @endif

        {{-- Tabel / List Transaksi --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

            {{-- Tampilan Desktop (Table) --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600 font-semibold border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4">Tanggal & ID</th>
                            <th class="px-6 py-4">Layanan</th>
                            <th class="px-6 py-4">Nominal</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-center">Aksi Refund</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($transactions as $tx)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-800">
                                        {{ \Carbon\Carbon::parse($tx->created_at)->format('d M Y, H:i') }}</div>
                                    <div class="text-xs text-gray-500 font-mono mt-1">#{{ $tx->order_id }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    @if ($tx->tipe == 'membership')
                                        <span class="flex items-center gap-2 font-semibold text-indigo-600"><i
                                                class="fas fa-crown text-indigo-400"></i> Membership</span>
                                    @else
                                        <span class="flex items-center gap-2 font-semibold text-amber-600"><i
                                                class="fas fa-coins text-amber-400"></i> Top-up Koin</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-bold text-gray-700">Rp
                                    {{ number_format($tx->price, 0, ',', '.') }}</td>
                                <td class="px-6 py-4">
                                    @if ($tx->status == 'settlement')
                                        <span
                                            class="px-2.5 py-1 bg-green-100 text-green-700 text-[11px] font-bold rounded-md">Berhasil</span>
                                    @elseif($tx->status == 'pending')
                                        <span
                                            class="px-2.5 py-1 bg-amber-100 text-amber-700 text-[11px] font-bold rounded-md">Menunggu</span>
                                    @elseif($tx->status == 'request')
                                        <span
                                            class="px-2.5 py-1 bg-blue-100 text-blue-700 text-[11px] font-bold rounded-md">Proses
                                            Refund</span>
                                    @elseif($tx->status == 'refund')
                                        <span
                                            class="px-2.5 py-1 bg-gray-200 text-gray-600 text-[11px] font-bold rounded-md">Dana
                                            Dikembalikan</span>
                                    @else
                                        <span
                                            class="px-2.5 py-1 bg-red-100 text-red-700 text-[11px] font-bold rounded-md">Gagal/Batal</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if ($tx->can_refund && $tx->status == 'settlement')
                                        <button type="button" onclick="openRefundModal('{{ $tx->order_id }}')"
                                            class="px-4 py-2 bg-red-50 text-red-600 hover:bg-red-500 hover:text-white rounded-lg text-xs font-bold transition-colors">
                                            Ajukan Refund
                                        </button>
                                    @else
                                        <span class="text-xs text-gray-400 cursor-not-allowed"
                                            title="Syarat refund tidak terpenuhi (Lebih dari 24 jam, fitur sudah terpakai, atau bukan Membership)">Tidak
                                            Tersedia</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500 font-medium">Belum ada riwayat
                                    transaksi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Tampilan Mobile (Card) --}}
            <div class="md:hidden divide-y divide-gray-100">
                @forelse($transactions as $tx)
                    <div class="p-4">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                @if ($tx->tipe == 'membership')
                                    <div class="font-bold text-indigo-600 text-sm flex items-center gap-1.5"><i
                                            class="fas fa-crown text-indigo-400"></i> Membership</div>
                                @else
                                    <div class="font-bold text-amber-600 text-sm flex items-center gap-1.5"><i
                                            class="fas fa-coins text-amber-400"></i> Top-up Koin</div>
                                @endif
                                <div class="text-xs text-gray-500 font-mono mt-0.5">#{{ $tx->order_id }}</div>
                            </div>
                            <div class="text-right">
                                <div class="font-bold text-gray-800 text-sm">Rp
                                    {{ number_format($tx->price, 0, ',', '.') }}</div>
                                <div class="text-[10px] text-gray-400 mt-0.5">
                                    {{ \Carbon\Carbon::parse($tx->created_at)->format('d M, H:i') }}</div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between mt-4">
                            <div>
                                @if ($tx->status == 'settlement')
                                    <span
                                        class="px-2.5 py-1 bg-green-100 text-green-700 text-[10px] font-bold rounded-md">Berhasil</span>
                                @elseif($tx->status == 'pending')
                                    <span
                                        class="px-2.5 py-1 bg-amber-100 text-amber-700 text-[10px] font-bold rounded-md">Menunggu</span>
                                @elseif($tx->status == 'request')
                                    <span
                                        class="px-2.5 py-1 bg-blue-100 text-blue-700 text-[10px] font-bold rounded-md">Proses
                                        Refund</span>
                                @elseif($tx->status == 'refund')
                                    <span
                                        class="px-2.5 py-1 bg-gray-200 text-gray-600 text-[10px] font-bold rounded-md">Dikembalikan</span>
                                @else
                                    <span
                                        class="px-2.5 py-1 bg-red-100 text-red-700 text-[10px] font-bold rounded-md">Gagal</span>
                                @endif
                            </div>
                            <div>
                                @if ($tx->can_refund)
                                    <button type="button" onclick="openRefundModal('{{ $tx->order_id }}')"
                                        class="px-3 py-1.5 bg-red-50 text-red-600 border border-red-200 rounded-lg text-xs font-bold transition-colors">
                                        Refund
                                    </button>
                                @else
                                    <span class="text-[10px] text-gray-400 italic">No Refund</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-500 font-medium text-sm">Belum ada riwayat transaksi.</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- MODAL REFUND (Tersembunyi secara default) --}}
    <div id="refundModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        {{-- Background Backdrop Overlay --}}
        <div class="fixed inset-0 bg-gray-900/75 transition-opacity backdrop-blur-sm"></div>

        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                {{-- Modal Panel --}}
                <div
                    class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div
                                class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <i class="fas fa-exclamation-triangle text-red-600"></i>
                            </div>
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                <h3 class="text-lg font-bold leading-6 text-gray-900 font-['Plus_Jakarta_Sans']"
                                    id="modal-title">
                                    Pengajuan Refund
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        Anda akan membatalkan layanan Membership untuk transaksi <span id="modalOrderId"
                                            class="font-bold text-gray-700"></span>. Status premium Anda akan langsung
                                        dicabut.
                                    </p>
                                </div>

                                {{-- Form Alasan --}}
                                <div class="mt-4">
                                    <form id="refundForm" method="POST" action="">
                                        @csrf
                                        <label for="reason" class="block text-sm font-semibold text-gray-700 mb-1">Pilih
                                            Alasan Refund <span class="text-red-500">*</span></label>
                                        <select id="reason" name="reason" required
                                            class="mt-1 block w-full rounded-xl border-gray-300 py-2.5 pl-3 pr-10 text-base focus:border-red-500 focus:outline-none focus:ring-red-500 sm:text-sm bg-gray-50 border">
                                            <option value="" disabled selected>Pilih salah satu alasan...</option>
                                            <option value="salah_beli">Salah memilih paket / Tidak sengaja membeli</option>
                                            <option value="fitur_error">Fitur membership tidak berfungsi / Error</option>
                                            <option value="berubah_pikiran">Berubah pikiran (Belum memakai fitur sama
                                                sekali)</option>
                                            <option value="lainnya">Alasan lainnya</option>
                                        </select>

                                        <div class="mt-3">
                                            <label for="detail_reason"
                                                class="block text-sm font-semibold text-gray-700 mb-1">Detail Tambahan
                                                (Opsional)</label>
                                            <textarea id="detail_reason" name="detail_reason" rows="2"
                                                class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm bg-gray-50 border p-3"
                                                placeholder="Jelaskan lebih detail jika perlu..."></textarea>
                                        </div>
                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 border-t border-gray-100">
                        <button type="button" onclick="submitRefund()"
                            class="inline-flex w-full justify-center rounded-xl bg-red-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto transition-colors">
                            Kirim Pengajuan
                        </button>
                        <button type="button" onclick="closeRefundModal()"
                            class="mt-3 inline-flex w-full justify-center rounded-xl bg-white px-4 py-2.5 text-sm font-bold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto transition-colors">
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SCRIPT PENGENDALI MODAL --}}
    <script>
        function openRefundModal(orderId) {
            // Tampilkan modal
            document.getElementById('refundModal').classList.remove('hidden');

            // Set ID Transaksi ke dalam teks
            document.getElementById('modalOrderId').innerText = '#' + orderId;

            // Ubah action URL form secara dinamis menggantikan :id menjadi order_id
            let url = "{{ route('account.transaction.refund', ':id') }}";
            url = url.replace(':id', orderId);
            document.getElementById('refundForm').action = url;
        }

        function closeRefundModal() {
            // Sembunyikan modal
            document.getElementById('refundModal').classList.add('hidden');

            // Reset form
            document.getElementById('refundForm').reset();
        }

        function submitRefund() {
            // Validasi form sebelum dikirim
            let reason = document.getElementById('reason').value;
            if (reason === "") {
                alert('Silakan pilih alasan refund terlebih dahulu!');
                return;
            }

            // Jika lolos validasi, submit form
            document.getElementById('refundForm').submit();
        }
    </script>
@endsection
