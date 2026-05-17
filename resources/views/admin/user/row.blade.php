@php
    $wallet = $val->wallet;
    $membershipName = $wallet->membership->name ?? 'Bronze';
    $membershipId = $wallet->membership_id ?? 1;

    // Warna badge berdasarkan membership
    $badgeClass = match (strtolower($membershipName)) {
        'gold premium' => 'bg-amber-100 text-amber-700 border-amber-200',
        'silver pro' => 'bg-slate-100 text-slate-700 border-slate-200',
        default => 'bg-orange-100 text-orange-700 border-orange-200',
    };
@endphp

<tr class="hover:bg-gray-50/80 transition-colors group align-middle user-row">
    <td class="py-4 px-4">
        <div class="flex items-center gap-3">
            <div
                class="w-10 h-10 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600 font-bold shrink-0 shadow-sm border border-white">
                {{ strtoupper(substr($val->name, 0, 1)) }}
            </div>
            <div>
                <span class="block text-sm font-bold text-gray-900">{{ $val->name }}</span>
                <span class="text-[11px] text-gray-400">Join: {{ $val->created_at->format('d M Y') }}</span>
            </div>
        </div>
    </td>
    <td class="py-4 px-4 text-sm text-gray-600">{{ $val->email }}</td>
    <td class="py-4 px-4">
        <div
            class="flex items-center text-sm font-semibold text-[#0d9488] bg-teal-50 px-3 py-1 rounded-lg w-max border border-teal-100">
            <i class="bi bi-whatsapp mr-2"></i> {{ $val->nowa ?? '-' }}
        </div>
    </td>
    <td class="py-4 px-4 text-center">
        <span class="px-3 py-1 rounded-full text-[10px] font-bold border uppercase tracking-wider {{ $badgeClass }}">
            {{ strtok($membershipName, ' ') }}
        </span>
    </td>
    <td class="py-4 px-4 text-right">
        <div class="flex items-center justify-end gap-2">
            {{-- Tombol Detail (Buka Modal) --}}
            <button type="button"
                onclick="openDetailModal(
                    {{ $val->id }}, 
                    '{{ $val->name }}', 
                    {{ $wallet->dabelyu_koin ?? 0 }}, 
                    {{ $wallet->membership_id ?? 1 }},
                    {{ $wallet->push_quota ?? 0 }},
                    {{ $wallet->banner_quota ?? 0 }},
                    {{ $wallet->highlight_quota ?? 0 }},
                    {{ $wallet->recommendation_quota ?? 0 }}
                )"
                class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white transition-all flex items-center justify-center shadow-sm"
                title="Detail & Wallet">
                <i class="fas fa-wallet text-xs"></i>
            </button>

            {{-- Tombol Hapus (SweetAlert) --}}
            <button type="button" onclick="confirmDelete({{ $val->id }}, '{{ $val->name }}')"
                class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white transition-all flex items-center justify-center shadow-sm"
                title="Hapus Agent">
                <i class="fas fa-trash-alt text-xs"></i>
            </button>
        </div>
    </td>
</tr>
