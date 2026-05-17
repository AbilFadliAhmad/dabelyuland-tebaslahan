@if ($typeProperty == 'lands')
    {{-- Tabel Tanah --}}
    <table class="w-full text-left align-middle border-collapse" id="table1">
        <thead class="bg-gray-50/50 border-b border-gray-200">
            <tr>
                <th class="w-[5%] text-center py-3 px-2 font-semibold text-gray-700 hidden md:table-cell">No</th>
                <th class="w-[40%] py-3 px-2 font-semibold text-gray-700">Informasi Tanah</th>
                <th class="w-[20%] py-3 px-2 font-semibold text-gray-700">Harga & Luas</th>
                <th class="w-[20%] py-3 px-2 font-semibold text-gray-700">Pengaturan Status</th>
                <th class="w-[15%] text-center py-3 px-2 font-semibold text-gray-700">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($properties as $property)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    {{-- Kolom No --}}
                    <td class="text-center text-gray-500 font-bold hidden md:table-cell py-3 px-2">
                        {{ $loop->iteration }}
                    </td>

                    {{-- Kolom Informasi Properti (Gambar, Judul, Lokasi, User) --}}
                    <td class="py-3 px-2">
                        <div class="flex items-center gap-3">
                            {{-- Gambar Lahan --}}
                            @if (!empty($property->mainImage))
                                <img src="{{ asset('storage/' . ($property->mainimage?->image_path . '-image_low.webp' ?? 'default.jpg')) }}"
                                    alt="Foto Tanah"
                                    class="w-[110px] h-[75px] object-cover rounded-lg shrink-0 border border-gray-200"
                                    loading="lazy"
                                    onerror="this.outerHTML='<div class=\'w-[110px] h-[75px] rounded-lg bg-gray-100 flex items-center justify-center text-gray-400 shrink-0 border border-gray-200\'><i class=\'bi bi-image text-red-500 text-2xl\'></i></div>'">
                            @else
                                <div
                                    class="w-[110px] h-[75px] rounded-lg bg-gray-100 flex items-center justify-center text-gray-400 shrink-0 border border-gray-200">
                                    <i class="bi bi-map text-2xl"></i>
                                </div>
                            @endif

                            {{-- Teks Info --}}
                            <div class="min-w-0">
                                <h6 class="mb-1 font-bold text-gray-900 truncate text-base max-w-[300px]">
                                    {{ $property->judul }}
                                </h6>

                                <div class="text-gray-500 line-clamp-2 mb-1 text-[13.6px] leading-snug">
                                    <i class="bi bi-geo-alt mr-1 text-red-500"></i> {{ $property->kota ?? '-' }}
                                </div>

                                <div class="text-gray-500 flex items-center text-xs">
                                    <i class="bi bi-person-badge mr-1"></i> Ditambahkan oleh:
                                    <span
                                        class="font-medium ml-1 text-gray-700">{{ optional($property->user)->name ?? 'Sistem' }}</span>
                                </div>
                            </div>
                        </div>
                    </td>

                    {{-- Kolom Harga & Kategori/Luas --}}
                    <td class="py-3 px-2">
                        <div class="font-bold text-green-600 mb-1.5 text-[1.05rem]">
                            Rp{{ number_format((float) str_replace(['Rp', '.', ' '], '', $property->harga), 0, ',', '.') }}
                        </div>
                        <div class="flex flex-wrap gap-1.5">
                            <span
                                class="inline-flex items-center bg-blue-50 text-blue-600 border border-blue-100 px-2 py-1 rounded text-xs font-semibold">
                                <i class="bi bi-arrows-fullscreen mr-1"></i> {{ $property->luas_tanah }} m²
                            </span>
                            <span
                                class="inline-flex items-center bg-gray-50 text-gray-600 border border-gray-200 px-2 py-1 rounded text-xs font-semibold">
                                <i class="bi bi-tag mr-1"></i> {{ ucfirst(str_replace('_', ' ', $property->kategori)) }}
                            </span>
                        </div>
                    </td>

                    {{-- Kolom Pengaturan Status (Kumpulan Badge & Tombol Form) --}}
                    <td class="py-3 px-2">
                        <div class="flex flex-wrap gap-2">
                            {{-- 1. BADGE STATUS TRANSAKSI (Informatif) --}}
                            @if ($property->transaksi == 'Dijual')
                                <span
                                    class="inline-flex items-center px-2.5 py-1.5 rounded-md text-xs font-semibold bg-green-500 text-white cursor-default">
                                    <i class="bi bi-check-circle mr-1"></i> Dijual
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center px-2.5 py-1.5 rounded-md text-xs font-semibold bg-yellow-400 text-gray-900 cursor-default">
                                    <i class="bi bi-key mr-1"></i> Disewa
                                </span>
                            @endif

                            {{-- 2. LOGIKA PERCABANGAN: MODERASI VS PENGATURAN --}}
                            @if ($property->status == 'menunggu')
                                {{-- TAMPILAN KHUSUS ADMIN: PERLU VERIFIKASI --}}

                                {{-- Tombol Kunjungi (Untuk Cek Kelayakan) --}}
                                <a href="/property/{{ $property->slug }}" target="_blank"
                                    class="inline-flex items-center px-2.5 py-1.5 rounded-md text-xs font-semibold bg-gray-100 text-gray-700 border border-gray-400 hover:bg-gray-200 transition-all">
                                    <i class="bi bi-box-arrow-up-right mr-1"></i> Kunjungi
                                </a>

                                {{-- Tombol Verifikasi (Aksi Utama) --}}
                                <form id="form-verify-{{ $property->id }}"
                                    action="{{ route('admin.property.verify-property', $property->id) }}"
                                    method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')

                                    <button type="button" onclick="confirmVerify('{{ $property->id }}')"
                                        class="inline-flex items-center px-2.5 py-1.5 rounded-md text-xs font-bold bg-indigo-600 text-white hover:bg-indigo-700 transition-all shadow-sm active:scale-95">
                                        <i class="bi bi-patch-check-fill mr-1"></i> Verifikasi Sekarang
                                    </button>
                                </form>
                            @else
                                {{-- TAMPILAN PENGATURAN (Jika Sudah Terverifikasi/Aktif/Non-Aktif) --}}

                                {{-- Toggle Ketersediaan (Hanya untuk Sewa) --}}
                                @if ($property->transaksi == 'Disewa')
                                    @php
                                        $isAvailable = $property->is_tersedia;
                                        $statusClasses = $isAvailable
                                            ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100'
                                            : 'bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-100';
                                    @endphp
                                    <form action="{{ route('user.property.toggle-availability', $property->id) }}"
                                        method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold border transition-all active:scale-95 {{ $statusClasses }}">
                                            <i
                                                class="bi {{ $isAvailable ? 'bi-check-circle-fill' : 'bi-clock-history' }}"></i>
                                            <span>{{ $isAvailable ? 'Tersedia' : 'Tersewa' }}</span>
                                        </button>
                                    </form>
                                @endif

                                {{-- Toggle Visibilitas Website --}}
                                @php
                                    $isActive = $property->status == 'aktif';
                                @endphp
                                <form action="{{ route('admin.property.toggle-visibility', $property->id) }}"
                                    method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        class="inline-flex items-center px-2.5 py-1.5 rounded-md text-xs font-semibold transition-all active:scale-95 {{ $isActive ? 'bg-cyan-600 text-white hover:bg-cyan-700' : 'bg-gray-500 text-white hover:bg-gray-600' }}">
                                        <i class="bi {{ $isActive ? 'bi-eye' : 'bi-eye-slash' }} mr-1"></i>
                                        {{ $isActive ? 'Tampil' : 'Sembunyi' }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>

                    {{-- Kolom Aksi --}}
                    <td class="text-center py-3 px-2">
                        {{-- Validasi Kepemilikan Data --}}
                        <div class="flex justify-center gap-2">
                            <a href="{{ route($user->role === 'admin' ? 'admin.property.edit' : 'user.property.edit', $property->id) }}"
                                class="inline-flex items-center justify-center w-8 h-8 rounded border border-blue-500 text-blue-500 hover:bg-blue-500 hover:text-white transition-colors"
                                title="Edit Lahan">
                                <i class="bi bi-pencil-square"></i>
                            </a>

                            <form id="delete-form-{{ $property->id }}"
                                action="{{ route('admin.property.archive', $property->id) }}" method="POST"
                                class="inline m-0">
                                @csrf
                                @method('PATCH') {{-- Gunakan PATCH karena kita hanya mengubah status, bukan menghapus baris --}}

                                {{-- Input hidden untuk mengirim alasan dan status baru ke backend --}}
                                <input type="hidden" name="reason" id="reason-{{ $property->id }}">
                                <input type="hidden" name="other_reason" id="other-reason-{{ $property->id }}">

                                <button type="button" onclick="showDeletePopup('{{ $property->id }}')"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded border border-red-500 text-red-500 hover:bg-red-500 hover:text-white transition-colors"
                                    title="Hapus / Arsipkan Lahan">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center py-12">
                        <div class="flex flex-col items-center justify-center text-gray-500">
                            <i class="bi bi-map mb-3 text-[3rem] text-slate-300"></i>
                            <h6 class="font-bold text-gray-700 mb-1">Belum ada data tanah</h6>
                            <p class="text-sm m-0">Klik tombol "Tambah Properti" untuk memasukkan data properti baru.
                            </p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
@else
    {{-- Tabel Bangunan --}}
    <table class="w-full text-left align-middle border-collapse" id="table1">
        <thead class="bg-gray-50/50 border-b border-gray-200">
            <tr>
                <th class="w-[5%] text-center py-3 px-2 font-semibold text-gray-700 hidden md:table-cell">No</th>
                <th class="w-[40%] py-3 px-2 font-semibold text-gray-700">Informasi Properti</th>
                <th class="w-[20%] py-3 px-2 font-semibold text-gray-700">Harga & Tipe</th>
                <th class="w-[20%] py-3 px-2 font-semibold text-gray-700">Pengaturan Status</th>
                <th class="w-[15%] text-center py-3 px-2 font-semibold text-gray-700">Aksi</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-gray-100">
            @php $no = 1; @endphp
            @if (isset($properties) && count($properties) > 0)
                @foreach ($properties as $property)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        {{-- Kolom No --}}
                        <td class="text-center text-gray-500 font-bold hidden md:table-cell py-3 px-2">
                            {{ $no++ }}
                        </td>

                        {{-- Kolom Informasi Properti (Gambar, Judul, Lokasi, User) --}}
                        <td class="py-3 px-2">
                            <div class="flex items-center gap-3">
                                {{-- Gambar --}}
                                @if (!empty($property->mainImage))
                                    <img src="{{ asset('storage/' . ($property->mainimage?->image_path ?? 'default') . '-image_low.webp') }}"
                                        alt="foto properti"
                                        class="w-[110px] h-[75px] object-cover rounded-lg shrink-0 border border-gray-100"
                                        loading="lazy">
                                @else
                                    <div
                                        class="bg-gray-100 flex items-center justify-center text-gray-400 shrink-0 w-[110px] h-[75px] rounded-lg">
                                        <i class="bi bi-house-door text-2xl"></i>
                                    </div>
                                @endif

                                {{-- Teks --}}
                                <div class="min-w-0">
                                    <h6 class="mb-1 font-bold text-gray-900 truncate text-base max-w-[300px]">
                                        {{ $property->judul }}
                                    </h6>

                                    <div class="text-gray-500 line-clamp-2 mb-1 text-[13.6px] leading-snug">
                                        <i class="bi bi-geo-alt mr-1 text-red-500"></i>
                                        {{ $property->kota ?? '-' }}
                                    </div>

                                    <div class="text-gray-500 flex items-center text-xs">
                                        <i class="bi bi-person-badge mr-1"></i> Ditambahkan oleh:
                                        <span
                                            class="font-medium ml-1 text-gray-700">{{ optional($property->user)->name ?? 'Sistem' }}</span>
                                    </div>
                                </div>
                            </div>
                        </td>

                        {{-- Kolom Harga & Tipe --}}
                        <td class="py-3 px-2">
                            <div class="font-bold text-green-600 mb-1.5 text-[1.05rem]">
                                Rp{{ number_format((float) str_replace(['Rp', '.', ' '], '', $property->harga), 0, ',', '.') }}
                            </div>
                            <span
                                class="inline-flex items-center bg-gray-50 text-gray-600 border border-gray-200 px-2 py-1 rounded text-xs font-semibold">
                                <i class="bi bi-tag mr-1"></i> {{ ucfirst($property->tipe) }}
                            </span>
                        </td>

                        {{-- Kolom Pengaturan Status (Kumpulan Badge & Tombol Form) --}}
                        <td class="py-3 px-2">
                            <div class="flex flex-wrap gap-2">
                                {{-- 1. BADGE STATUS TRANSAKSI (Informatif) --}}
                                @if ($property->transaksi == 'Dijual')
                                    <span
                                        class="inline-flex items-center px-2.5 py-1.5 rounded-md text-xs font-semibold bg-green-500 text-white cursor-default">
                                        <i class="bi bi-check-circle mr-1"></i> Dijual
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center px-2.5 py-1.5 rounded-md text-xs font-semibold bg-yellow-400 text-gray-900 cursor-default">
                                        <i class="bi bi-key mr-1"></i> Disewa
                                    </span>
                                @endif

                                {{-- 2. LOGIKA PERCABANGAN: MODERASI VS PENGATURAN --}}
                                @if ($property->status == 'menunggu')
                                    {{-- TAMPILAN KHUSUS ADMIN: PERLU VERIFIKASI --}}

                                    {{-- Tombol Kunjungi (Untuk Cek Kelayakan) --}}
                                    <a href="/property/{{ $property->slug }}" target="_blank"
                                        class="inline-flex items-center px-2.5 py-1.5 rounded-md text-xs font-semibold bg-gray-100 text-gray-700 border border-gray-400 hover:bg-gray-200 transition-all">
                                        <i class="bi bi-box-arrow-up-right mr-1"></i> Kunjungi
                                    </a>

                                    {{-- Tombol Verifikasi (Aksi Utama) --}}
                                    <form id="form-verify-{{ $property->id }}"
                                        action="{{ route('admin.property.verify-property', $property->id) }}"
                                        method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')

                                        <button type="button" onclick="confirmVerify('{{ $property->id }}')"
                                            class="inline-flex items-center px-2.5 py-1.5 rounded-md text-xs font-bold bg-indigo-600 text-white hover:bg-indigo-700 transition-all shadow-sm active:scale-95">
                                            <i class="bi bi-patch-check-fill mr-1"></i> Verifikasi Sekarang
                                        </button>
                                    </form>
                                @else
                                    {{-- TAMPILAN PENGATURAN (Jika Sudah Terverifikasi/Aktif/Non-Aktif) --}}

                                    {{-- Toggle Ketersediaan (Hanya untuk Sewa) --}}
                                    @if ($property->transaksi == 'Disewa')
                                        @php
                                            $isAvailable = $property->is_tersedia;
                                            $statusClasses = $isAvailable
                                                ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100'
                                                : 'bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-100';
                                        @endphp
                                        <form action="{{ route('user.property.toggle-availability', $property->id) }}"
                                            method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold border transition-all active:scale-95 {{ $statusClasses }}">
                                                <i
                                                    class="bi {{ $isAvailable ? 'bi-check-circle-fill' : 'bi-clock-history' }}"></i>
                                                <span>{{ $isAvailable ? 'Tersedia' : 'Tersewa' }}</span>
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Toggle Visibilitas Website --}}
                                    @php
                                        $isActive = $property->status == 'aktif';
                                    @endphp
                                    <form action="{{ route('admin.property.toggle-visibility', $property->id) }}"
                                        method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="inline-flex items-center px-2.5 py-1.5 rounded-md text-xs font-semibold transition-all active:scale-95 {{ $isActive ? 'bg-cyan-600 text-white hover:bg-cyan-700' : 'bg-gray-500 text-white hover:bg-gray-600' }}">
                                            <i class="bi {{ $isActive ? 'bi-eye' : 'bi-eye-slash' }} mr-1"></i>
                                            {{ $isActive ? 'Tampil' : 'Sembunyi' }}
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>

                        {{-- Kolom Aksi --}}
                        <td class="text-center py-3 px-2">
                            {{-- Validasi Kepemilikan Data --}}
                            <div class="flex justify-center gap-2">
                                <a href="{{ route($user->role === 'admin' ? 'admin.property.edit' : 'user.property.edit', $property->id) }}"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded border border-blue-500 text-blue-500 hover:bg-blue-500 hover:text-white transition-colors"
                                    title="Edit Lahan">
                                    <i class="bi bi-pencil-square"></i>
                                </a>

                                <form id="delete-form-{{ $property->id }}"
                                    action="{{ route('admin.property.archive', $property->id) }}" method="POST"
                                    class="inline m-0">
                                    @csrf
                                    @method('PATCH') {{-- Gunakan PATCH karena kita hanya mengubah status, bukan menghapus baris --}}

                                    {{-- Input hidden untuk mengirim alasan dan status baru ke backend --}}
                                    <input type="hidden" name="reason" id="reason-{{ $property->id }}">
                                    <input type="hidden" name="other_reason" id="other-reason-{{ $property->id }}">

                                    <button type="button" onclick="showDeletePopup('{{ $property->id }}')"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded border border-red-500 text-red-500 hover:bg-red-500 hover:text-white transition-colors"
                                        title="Hapus / Arsipkan Lahan">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="5" class="text-center py-12">
                        <div class="flex flex-col items-center justify-center text-gray-500">
                            <i class="bi bi-building mb-3 text-[3rem] text-slate-300"></i>
                            <h6 class="font-bold text-gray-700 mb-1">Belum ada data Bangunan</h6>
                            <p class="text-sm m-0">Klik tombol "Tambah Properti" untuk memasukkan data properti baru.
                            </p>
                        </div>
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
@endif
