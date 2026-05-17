@php
    $statusLabel = $property->status;

    // Logic Badge & Row Highlight
    $badgeClass = match ($statusLabel) {
        'terjual' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'ditolak' => 'bg-amber-50 text-amber-700 border-amber-200',
        default => 'bg-rose-50 text-rose-700 border-rose-200',
    };

    $rowClass = $statusLabel === 'terjual' ? 'bg-emerald-50/30' : '';

    $imagePath = $property->mainImage->image_path ?? null;
    $imgUrl = !empty($imagePath)
        ? asset('storage/' . $property->mainImage->image_path . '-image_low.webp')
        : asset('frontside/img/default-property.jpg');
@endphp

<tr class="archive-row transition-colors {{ $rowClass }}" data-status="{{ $property->status }}"
    data-agent="{{ strtolower($property->user->name ?? '') }}">

    {{-- Info Properti --}}
    <td>
        <div class="flex items-center gap-3">
            <div class="relative">
                <img src="{{ $imgUrl }}" class="w-12 h-12 rounded-xl object-cover border border-gray-100 shrink-0">
                @if ($statusLabel === 'terjual')
                    <div
                        class="absolute -top-1 -right-1 w-5 h-5 bg-emerald-500 text-white rounded-full flex items-center justify-center border-2 border-white shadow-sm">
                        <i class="fas fa-check text-[8px]"></i>
                    </div>
                @endif
            </div>
            <div>
                <h6 class="text-sm font-bold text-gray-900 m-0 line-clamp-1">{{ $property->judul }}</h6>
                <span class="text-[10px] text-gray-400 font-medium">
                    ID: #{{ $property->id }} • {{ $property->updated_at->format('d/m/Y H:i') }}
                </span>
            </div>
        </div>
    </td>

    <td class="agent-name-cell">
        <p class="text-sm font-semibold text-gray-700 m-0">{{ $property->user->name ?? 'Anonim' }}</p>
    </td>

    <td class="text-center">
        <span
            class="px-2.5 w-fit py-1 whitespace-nowrap rounded-lg text-[10px] font-bold border uppercase tracking-wider {{ $badgeClass }}">
            @if ($statusLabel === 'terjual')
                <i class="fas fa-trophy mr-1"></i>
            @endif
            {{ $statusLabel }}
        </span>
    </td>

    <td>
        <div class="flex items-center gap-2">
            <div
                class="text-[11px] leading-relaxed text-gray-500 bg-white/50 p-2 rounded-lg border border-gray-100 italic line-clamp-1 flex-1">
                "{{ $property->deleted_reason ?? 'Tidak ada catatan tambahan.' }}"
            </div>
            <button type="button"
                onclick="showReasonDetail('{{ addslashes($property->judul) }}', '{{ addslashes($property->deleted_reason ?? 'Agen tidak memberikan alasan spesifik.') }}', '{{ $property->status }}')"
                class="p-1.5 rounded-md bg-gray-100 text-gray-400 hover:bg-indigo-50 hover:text-indigo-600 transition-colors shadow-sm">
                <i class="fas fa-info-circle text-xs"></i>
            </button>
        </div>
    </td>

    <td class="text-right pr-6">
        <div class="flex items-center justify-end gap-2">
            <form action="{{ route('admin.properties.restore', $property->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <button type="button" onclick="confirmAction(this, 'restore')"
                    class="w-8 h-8 rounded-lg bg-white text-teal-600 border border-teal-100 hover:bg-teal-500 hover:text-white flex items-center justify-center transition-all shadow-sm">
                    <i class="fas fa-redo-alt text-xs"></i>
                </button>
            </form>
        </div>
    </td>
</tr>
