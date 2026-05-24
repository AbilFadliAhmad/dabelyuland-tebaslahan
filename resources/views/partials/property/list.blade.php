@extends($user->role == 'admin' ? 'layouts.admin' : 'layouts.user')

@section('style')
    <style>
        /* Styling khusus untuk menyempurnakan UI Tabel */
        .table-custom th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            color: #6b7280;
            border-bottom: 2px solid #f3f4f6;
            vertical-align: middle;
        }

        .table-custom tbody tr {
            transition: background-color 0.2s ease;
        }

        .table-custom tbody tr:hover {
            background-color: #f8fafc;
        }

        /* Ukuran foto yang proporsional dan jelas */
        .img-property {
            width: 110px;
            height: 75px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        /* Memotong teks panjang agar tabel tidak melebar */
        .text-truncate-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            white-space: normal;
        }

        /* Tombol Aksi */
        .btn-action {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            transition: all 0.2s;
        }

        /* Kustomisasi Badge/Tombol Status */
        .badge-btn {
            padding: 0.4em 0.7em;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 50rem;
            border: none;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .badge-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* Penyesuaian khusus Mobile */
        @media (max-width: 768px) {
            .img-property {
                width: 80px;
                height: 60px;
            }

            .property-placeholder {
                width: 80px !important;
                height: 60px !important;
            }
        }
    </style>
@endsection

@section('content')
    <div class="w-full min-h-screen p-4 sm:p-6 lg:p-8 font-['Inter']">

        {{-- ==========================================================
         HEADER AREA & TOMBOL TAMBAH
         ========================================================== --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 m-0">
                    {{ $typeProperty == 'lands' ? 'Daftar Tanah' : 'Daftar Bangunan' }}
                </h2>
                <p class="text-sm text-gray-500 m-0 mt-1">Kelola seluruh data properti dan bangunan Dabelyuland.</p>
            </div>

            <a href="{{ route($user->role == 'admin' ? 'admin.property.create' : 'user.property.create', $typeProperty == 'lands' ? 'land' : 'building') }}"
                class="inline-flex items-center px-5 py-2.5 bg-[#0d9488] hover:bg-teal-700 text-white text-sm font-bold rounded-xl transition-all shadow-md shadow-teal-600/20 hover:-translate-y-0.5 no-underline">
                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Tambah Properti
            </a>
        </div>

        {{-- ==========================================================
         AREA TABEL
         ========================================================== --}}
        <div class="bg-white rounded-2xl shadow-[0_2px_15px_rgba(0,0,0,0.03)] border border-gray-100 overflow-hidden">
            {{-- Kita bungkus include dengan padding agar tabel tidak mepet tepi kartu --}}
            <div class="p-4 sm:p-6 overflow-x-auto">
                @include('partials.property.table', ['typeProperty' => $typeProperty])
            </div>
        </div>

        {{-- Footer --}}
        <footer class="mt-8 pb-6 text-center md:text-left">
            <p class="text-sm text-gray-400 m-0">2026 &copy; Dabelyuland Indonesia</p>
        </footer>

    </div>
@endsection

@section('script')
    <script>
        function confirmVerify(propertyId) {
            Swal.fire({
                title: 'Verifikasi Properti?',
                text: "Pastikan data sudah benar sebelum ditayangkan ke publik.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5', // Warna Indigo-600 agar match dengan tombol
                cancelButtonColor: '#6b7280', // Warna Gray-500
                confirmButtonText: 'Ya, Verifikasi!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-2xl', // Agar lebih aesthetic/rounded
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Tampilkan loading sebentar agar user tahu proses sedang berjalan
                    Swal.showLoading();
                    // Kirim form secara manual
                    document.getElementById(`form-verify-${propertyId}`).submit();
                }
            })
        }

        function showDeletePopup(id) {
            Swal.fire({
                title: '<span class="text-xl font-bold text-gray-800">Arsipkan Properti</span>',
                html: `
            <div class="text-left mt-4 space-y-3">
                <p class="text-sm text-gray-500 mb-4">Mohon pilih alasan pengarsipan properti untuk membantu pendataan kami.</p>

                <!-- Opsi Utama: Tebaslahan (Paling Atas & Stand Out) -->
                <label class="flex items-center p-3 border-2 border-teal-100 rounded-xl cursor-pointer hover:bg-teal-50 hover:border-teal-300 transition-all group">
                    <input type="radio" name="delete_reason" value="tebaslahan" class="w-4 h-4 text-teal-600 focus:ring-teal-500">
                    <div class="ml-3">
                        <span class="block text-sm font-bold text-teal-800">Terjual dari Tebaslahan</span>
                        <span class="block text-xs text-teal-600">Lahan laku melalui platform ini.</span>
                    </div>
                </label>

                <!-- Opsi Lainnya -->
                <div class="space-y-2">
                    <label class="flex items-center p-3 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition-all">
                        <input type="radio" name="delete_reason" value="belum_terjual" class="w-4 h-4 text-indigo-600">
                        <span class="ml-3 text-sm font-medium text-gray-700">Belum Terjual / Batal Jual</span>
                    </label>

                    <label class="flex items-center p-3 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition-all">
                        <input type="radio" name="delete_reason" value="sosmed" class="w-4 h-4 text-indigo-600">
                        <span class="ml-3 text-sm font-medium text-gray-700">Terjual dari Media Sosial</span>
                    </label>

                    <label class="flex items-center p-3 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition-all">
                        <input type="radio" name="delete_reason" value="lainnya" id="opt-lainnya" class="w-4 h-4 text-indigo-600">
                        <span class="ml-3 text-sm font-medium text-gray-700">Alasan Lainnya</span>
                    </label>
                </div>

                <!-- Input Textarea (Muncul hanya jika memilih 'lainnya') -->
                <div id="wrapper-lainnya" class="hidden mt-3 animate-fade-in">
                    <textarea id="input-alasan-lainnya" 
                        class="w-full p-3 text-sm border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" 
                        placeholder="Tuliskan detail alasannya..." rows="2"></textarea>
                </div>
            </div>
        `,
                showCancelButton: true,
                confirmButtonText: 'Ya, Arsipkan!',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#9C27B0', // Red-500
                cancelButtonColor: '#9ca3af', // Gray-400
                padding: '2rem',
                customClass: {
                    popup: 'rounded-3xl',
                    confirmButton: 'rounded-xl px-6 py-2.5 text-sm font-bold',
                    cancelButton: 'rounded-xl px-6 py-2.5 text-sm font-bold'
                },
                // Logika Interaktif saat Popup Terbuka
                didOpen: () => {
                    const radios = Swal.getPopup().querySelectorAll('input[name="delete_reason"]');
                    const wrapper = Swal.getPopup().querySelector('#wrapper-lainnya');

                    radios.forEach(radio => {
                        radio.addEventListener('change', (e) => {
                            if (e.target.id === 'opt-lainnya') {
                                wrapper.classList.remove('hidden');
                            } else {
                                wrapper.classList.add('hidden');
                            }
                        });
                    });
                },
                // Validasi sebelum Submit
                preConfirm: () => {
                    const selected = Swal.getPopup().querySelector('input[name="delete_reason"]:checked');
                    if (!selected) {
                        Swal.showValidationMessage('Silakan pilih salah satu alasan!');
                        return false;
                    }

                    const reason = selected.value;
                    let otherText = '';

                    if (reason === 'lainnya') {
                        otherText = Swal.getPopup().querySelector('#input-alasan-lainnya').value;
                        if (!otherText) {
                            Swal.showValidationMessage('Alasan lainnya tidak boleh kosong!');
                            return false;
                        }
                    }

                    return {
                        reason: reason,
                        otherText: otherText
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    submitDeleteForm(id, result.value.reason, result.value.otherText);
                }
            });
        }

        function submitDeleteForm(id, reason, otherText = '') {
            document.getElementById(`reason-${id}`).value = reason;
            document.getElementById(`other-reason-${id}`).value = otherText;
            Swal.showLoading();
            document.getElementById(`delete-form-${id}`).submit();
        }
    </script>
@endsection
