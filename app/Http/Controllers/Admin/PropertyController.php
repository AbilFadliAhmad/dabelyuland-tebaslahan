<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\GalleryProperty;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Exception;

class PropertyController extends Controller
{
    public function index(Request $request)
    {
        // Dapatkan user yang sedang login
        $user = Auth::user();

        // 1. Ambil potongan terakhir dari URL (misal hasilnya: 'lands' atau 'buildings')
        $typeProperty = last($request->segments());

        // 2. Mulai merakit query dasar (belum mengambil data ke database)
        $query = Property::query();


        // 3. Filter berdasarkan Hak Akses (Admin vs User Biasa)
        if ($user->role !== 'admin') {
            $query->where('user_id', Auth::id());
        }

        // 4. Filter berdasarkan   
        if ($typeProperty === 'lands') {
            // Jika URL berakhiran 'lands', wajib HANYA tipe 'tanah'
            $query->where('tipe', 'tanah');
        } else {
            // Jika bukan 'lands' (berarti 'buildings' dll), ambil SEMUA KECUALI 'tanah'
            $query->where('tipe', '!=', 'tanah');
        }

        // 5. Eksekusi query ke database
        $query->whereNotIn('status', ['draft', 'dihapus', 'terjual']);
        $properties = $query->with('mainImage')->get();

        return view('partials.property.list', compact('properties', 'typeProperty', 'user'));
    }

    // Buat draftnya atau pakai draft yang sudah ada setiap masuk ke form pembuatan bangunan 
    public function create(Request $request, string $typeProperty)
    {
        // 1. Dapatkan Session ID asli bawaan Laravel
        $currentSessionId = $request->session()->getId();
        $userId = Auth::id();

        // 2. Gunakan firstOrCreate untuk "Daur Ulang" draft. 
        // Mencegah database penuh jika user me-refresh halaman berulang kali.
        $draft = Property::firstOrCreate(
            [
                // Kondisi Pencarian (Apakah user dan sesi ini sudah punya draft?)
                'user_id' => $userId, 
                'session_id' => $currentSessionId,
            ],
            [
                // JIKA BELUM ADA, buat baru dengan data dummy ini.
                // itu NOT NULL, kamu WAJIB memberikan nilai sementara agar MySQL tidak error.
                'judul' => 'Draft Kosong',
                'slug' => 'draft-kosong-' . $currentSessionId,
                'harga' => 0,
            ]
        );

        // Variable pembantu untuk view
        $propertyId = $draft->id;
        $images = GalleryProperty::where('property_id', $propertyId)->orderBy('sort', 'asc')->get();
        $isEdit = false;
        $user = Auth::user();

        // 3. Kirim variabel $draft ke View menggunakan fungsi compact()
        // Agar di file Blade kamu bisa memanggil {{ $draft->id }}
        return view('partials.property.form', compact('propertyId', 'typeProperty', 'isEdit', 'images', 'user'));
    }

    // Edit - hanya pemilik atau admin
    public function edit(Property $property)
    {
        if ($property->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403, 'Akses ditolak');
        }
        
        // Kirim data ke view
        $typeProperty = $property->tipe == 'tanah' ? 'land' : 'building';
        $propertyId = $property->id;
        $isEdit = true;
        $images = GalleryProperty::where('property_id', $property->id)->orderBy('sort', 'asc')->get();
        $user = Auth::user();

        return view('partials.property.form', compact('property', 'typeProperty', 'propertyId', 'isEdit', 'images', 'user'));
    }

    // Menggunakan update untuk menyimpan properti karena menggunakan prinsip Draft First
    public function store(Request $request)
    {
        // 0. Ubah format string menjadi numerik
        $request->merge([
            'harga' => str_replace('.', '', $request->harga),
            'luas_tanah' => str_replace('.', '', $request->luas_tanah),
            'luas_bangunan' => str_replace('.', '', $request->luas_bangunan),
        ]);

        $validated = $request->validate([
            'judul'             => 'required|string|max:255',
            'harga'             => 'required|integer|min:0',
            'deskripsi'         => 'required|string',
            'tipe'              => 'required|in:apartemen,rumah,ruko,kantor,gudang,tanah',
            'alamat_detail'     => 'required|string',
            'kategori'          => 'required|in:3 Lantai,2 Lantai,1 Lantai,Lainnya,Tanah Kosong,Sawah,Kebun',
            'transaksi'         => 'required|in:Dijual,Disewa',
            'legalitas'         => 'required|in:SHM,HGB,HP,SHMSRS,HGU,HPL,Girik,Petok_D,Letter_C,Eigendom,Sultan_Ground,AJB,PPJB,Lainnya',
            'luas_tanah'        => 'required|integer|min:0',
            'luas_bangunan'     => 'nullable|integer|min:0',
            'jumlah_kamar_tidur'=> 'nullable|integer|min:0',
            'jumlah_kamar_mandi'=> 'nullable|integer|min:0',
            'latitude'          => 'nullable|numeric',
            'longitude'         => 'nullable|numeric',
            'id'                => 'required',
            'kota'              => 'required|string',
        ], [
            '*.required' => ':Attribute wajib diisi.',
            '*.in'       => 'Pilihan :attribute tidak valid.',
        ]);
        $user = Auth::user();

        // 2. Mencari Data Properti
        // findOrFail akan otomatis melempar error 404 jika ID tidak ditemukan
        $property = Property::findOrFail($request->id);

        // [Opsional] Keamanan: Pastikan yang mengedit adalah pemilik properti atau admin
        if ($user->role !== 'admin' && $property->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit properti ini.');
        }

        DB::transaction(function () use ($request, $property, $validated, $user) {
            $validated['slug'] = Str::slug($request->judul) . '-' . Str::random(6);
            $validated['status'] = $user->role == 'admin' ? 'aktif' : 'menunggu';

            $property->update($validated);

            // Panggil method yang sama di sini
            $this->syncPropertyImages($request, $property);
        });

        // 7 Perbarui SessionId
        $request->session()->regenerate();

        // 7. Redirect Pengguna
        // Arahkan kembali ke halaman index sesuai role user
        $route = $user->role === 'admin' ? 'admin.buildings.index' : 'user.buildings.index';
        
        return redirect()->route($route)
            ->with('success', 'Data properti berhasil diperbarui dan disimpan.');
    }

     // Update - hanya pemilik atau admin
    public function update(Request $request)
    {
        // 1. Otorisasi (Gunakan find agar hemat query)
        $property = Property::findOrFail($request->id);
        if (Auth::user()->id !== $property->user_id && Auth::user()->role !== 'admin') {
            abort(403, 'Akses ditolak: Anda bukan pemilik properti ini.');
        }

        // Ubah format string menjadi numerik
        $request->merge([
            'harga' => str_replace('.', '', $request->harga),
            'luas_tanah' => str_replace('.', '', $request->luas_tanah),
            'luas_bangunan' => str_replace('.', '', $request->luas_bangunan),
        ]);
            
         // 2. Validasi (Gunakan wildcard untuk pesan error)
        $validated = $request->validate([
            'judul'             => 'required|string|max:255',
            'harga'             => 'required|integer|min:0',
            'deskripsi'         => 'required|string',
            'tipe'              => 'required|in:apartemen,rumah,ruko,kantor,gudang,tanah',
            'kategori'          => 'required|in:3 Lantai,2 Lantai,1 Lantai,Lainnya,Tanah Kosong,Sawah,Kebun',
            'transaksi'         => 'required|in:Dijual,Disewa',
            'legalitas'         => 'required|in:SHM,HGB,HP,SHMSRS,HGU,HPL,Girik,Petok_D,Letter_C,Eigendom,Sultan_Ground,AJB,PPJB,Lainnya',
            'luas_tanah'        => 'required|integer|min:0',
            'luas_bangunan'     => 'nullable|integer|min:0',
            'jumlah_kamar_tidur'=> 'nullable|integer|min:0',
            'jumlah_kamar_mandi'=> 'nullable|integer|min:0',
            'latitude'          => 'nullable|numeric',
            'longitude'         => 'nullable|numeric',
            'id'                => 'required',
            'kota'              => 'required|string',
            'alamat_detail'     => 'required|string',
        ], [
            '*.required' => ':Attribute wajib diisi.',
            '*.in'       => 'Pilihan :attribute tidak valid.',
        ]);

        // Gunakan DB Transaction agar proses upload gambar & update data sinkron
        DB::transaction(function () use ($request, $property, $validated) {
            // Update data text
            $property->update($validated);

            // Panggil method sinkronisasi gambar
            $this->syncPropertyImages($request, $property);
        });

        // Redirect setelah sukses
        $role = Auth::user()->role ?? 'user';
        $routeType = $validated['tipe'] === 'tanah' ? 'lands' : 'buildings';
        
        return redirect()->route("{$role}.{$routeType}.index")
                        ->with('success', 'Data properti berhasil diperbarui!');   
    }

    private function syncPropertyImages(Request $request, $property)
    {
        $keys = ['-image_low.webp', '-image_high.webp', '-image_ori.webp'];

        for ($i = 1; $i <= 10; $i++) {
            $imagePath = $request->input("temp_preview_$i");
            $existingImage = GalleryProperty::where('property_id', $property->id)
                ->where('sort', $i)
                ->first();

            // LOGIKA 1: Jika input kosong (user menghapus foto di UI)
            if (!$imagePath) {
                if ($existingImage) {
                    // Hapus file fisik dari storage
                    foreach ($keys as $key) {
                        Storage::disk('public')->delete($existingImage->image_path . $key);
                    }
                    // Hapus data dari database
                    $existingImage->delete();
                }
                continue; // Lanjut ke urutan berikutnya
            }

            // LOGIKA 2: Jika ada perubahan (foto baru dari folder temp)
            if (str_starts_with($imagePath, 'temp/')) {
                // Pastikan file temp benar-benar ada sebelum diproses
                if (Storage::disk('public')->exists($imagePath . '-image_low.webp')) {
                    
                    // Jika sebelumnya sudah ada foto di urutan ini, hapus foto lamanya dulu
                    if ($existingImage) {
                        foreach ($keys as $key) {
                            Storage::disk('public')->delete($existingImage->image_path . $key);
                        }
                    }

                    // Pindahkan file dari temp/ ke property/
                    $newPath = str_replace('temp/', 'property/', $imagePath);
                    foreach ($keys as $key) {
                        Storage::disk('public')->move($imagePath . $key, $newPath . $key);
                    }

                    // Update atau Create record di database
                    GalleryProperty::updateOrCreate(
                        ['property_id' => $property->id, 'sort' => $i],
                        ['image_path' => $newPath]
                    );
                }
            }
            // Jika path tidak diawali temp/ (berarti path property/), abaikan karena tidak ada perubahan
        }
    }

    // Upload image dengan strategi 'Draft First'
    public function uploadImage(Request $request) 
    {
        // 1. Validasi (Gunakan wildcard * jika pesan errornya sama semua)
        $request->validate([
            'image_high' => 'required|image|mimes:webp|max:1024',
            'image_low'  => 'required|image|mimes:webp|max:1024',
            'image_ori'  => 'required|image|mimes:webp|max:1024',
            'id'         => ['required', Rule::exists('properties', 'id')->where('user_id', Auth::id())],
            'sort'       => 'required|integer',
            'isEdit'     => 'required',
            'typeProperty' => 'required',
        ], [
            'image_*.required' => 'Gambar harus diisi.',
            'image_*.max'      => 'Ukuran gambar maksimal 1MB.',
            'id.exists'        => 'Properti tidak ditemukan.',
            '*.required'       => ':Attribute wajib diisi.',
        ]);

        // Validasi gambar jika yang diupload adalah sampul utama
        if ($request->sort == 1) {
            // $isBuilding = ($request->tipe !== 'tanah');
            $check = $this->checkImageIsProperty($request->file('image_high'), $request->typeProperty == 'building');
            // Tambahkan true agar menjadi array, bukan stdClass
            $result = $check->getData(true);

            if (!($result['is_valid'] ?? false)) {
                Log::info('Azure Tags:', ['tags' => $result['detected_tags'] ?? []]);

                return response()->json([
                    'test' => $result,
                    'status' => 'error',
                    'message' => $result['message'] ?? 'Foto tidak memenuhi kriteria properti.',
                    'debug_tags' => $result['detected_tags'] ?? [] // Munculkan di network tab untuk debug
                ], 422);
            }
        }

        // 2. Inisialisasi Data
        $keys     = ['-image_low.webp', '-image_high.webp', '-image_ori.webp'];
        // $isEdit   = $request->boolean('isEdit');
        // $folder   = $isEdit ? 'temp' : 'property';
        $folder   = 'temp';
        $prefix   = time() . '_' . Str::random(5);

        // 3. Simpan File ke Storage
        foreach ($keys as $key) {
            $filename = $prefix.$key;
            $inputName = str_replace(['-','.webp'], '', $key);
            $request->file($inputName)->storeAs($folder, $filename, 'public'); 
        }

        // 4. Update Database & Cleanup (Hanya jika bukan mode Edit)
        // if (!$isEdit) {
        //     $oldImage = GalleryProperty::where('property_id', $request->id)
        //         ->where('sort', $request->sort)
        //         ->first();

        //     // Hapus file lama jika ada (berdasarkan prefix yang tersimpan)
        //     if ($oldImage) {
        //         foreach ($keys as $key) {
        //             Storage::disk('public')->delete($oldImage->image_path.$key);
        //         }
        //     }

        //     // Simpan path ke DB (hanya simpan prefix/base path-nya)
        //     GalleryProperty::updateOrCreate(
        //         ['property_id' => $request->id, 'sort' => $request->sort],
        //         ['image_path' => "{$folder}/{$prefix}"]
        //     );
        // }

        // Kembalikan respons sukses
        return response()->json([
            'status'     => 'success',
            'message'    => 'Gambar berhasil diperbarui!',
            'image_path' => asset("storage/{$folder}/{$prefix}-image_low.webp")
        ]);
    }

    // Fungsi untuk memeriksa gambar properti
    public function checkImageIsProperty(UploadedFile $image, bool $isBuilding)
    {
        try {
            $endpoint = config('azure.vision_endpoint') . 'vision/v3.2/analyze';
            
            // 1. Kirim request ke Azure
            $response = Http::withoutVerifying()->withHeaders([
                'Ocp-Apim-Subscription-Key' => config('azure.vision_key'),
                'Content-Type' => 'application/octet-stream'
            ])->withBody(
                file_get_contents($image->getRealPath()), 'application/octet-stream' // Menggunakan binner gambar
            )->post($endpoint . '?visualFeatures=Tags');

            // 2. Jika response sukses
            if ($response->successful()) {
                $tags = collect($response->json()['tags']);

                // 3. Tentukan kata kunci berdasarkan tipe properti (Bangunan vs Tanah)
                if (filter_var($isBuilding, FILTER_VALIDATE_BOOLEAN)) {
                    $validKeywords = ['house', 'building', 'residential', 'home', 'facade', 'property', 'apartment', 'architecture'];
                    $label = 'bangunan/rumah';
                } else {
                    $validKeywords = ['land', 'field', 'grass', 'nature', 'outdoor', 'ground', 'soil', 'lot', 'empty land'];
                    $label = 'tanah/lahan';
                }

                // 4. Cek apakah ada tag yang cocok dengan tingkat keyakinan (confidence) > 50%
                $isValid = $tags->contains(function ($tag) use ($validKeywords) {
                    return in_array(strtolower($tag['name']), $validKeywords) && $tag['confidence'] > 0.5;
                });

                return response()->json([
                    'is_valid'      => $isValid,
                    'property_type' => $label,
                    'detected_tags' => $tags->pluck('name'), // Berguna untuk kamu melihat hasil dari Azure
                    'message'       => $isValid 
                                        ? "Sip! Gambar terdeteksi sebagai $label." 
                                        : "Foto tidak terdeteksi sebagai $label. Mohon pastikan foto sesuai dengan tipe properti."
                ], 200);
            }

            // Jika API Azure merespons dengan error (misal: key salah, kuota habis)
            return response()->json(['error' => 'Gagal menghubungi layanan pendeteksi gambar (Azure).'], 502);

        } catch (Exception $e) {
            // Tangkap error internal sistem (misal: file gambar rusak atau tidak terbaca)
            return response()->json([
                'error'   => 'Terjadi kesalahan saat memproses gambar.',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    // Hapus image
    public function deleteImage(Request $request)
    {
        try {
            // Validasi apakah yang menghapus adalah pemilik properti
            $property = Property::findOrFail($request->id);
            if ($property->user_id !== Auth::id()) abort(403, 'Akses ditolak');

            // Inisialisasi Variable
            $propertyId = $request->id;
            $sort = $request->sort;

            // 1. Cari datanya terlebih dahulu (untuk mengambil path gambar)
            $gallery = GalleryProperty::where('property_id', $propertyId)
                                    ->where('sort', $sort)
                                    ->first();

            // Jika data ditemukan
            if ($gallery) {
                // 2. Hapus data dari database
                $gallery->delete();

                // 3. Hapus file fisik dari storage jika ada
                if ($gallery->image_path && Storage::disk('public')->exists($gallery->image_path)) {
                    Storage::disk('public')->delete($gallery->image_path);
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Gambar berhasil dihapus dari galeri dan server!',
            ], 200);

        } catch (\Exception $e) {
            // Catat pesan error asli ke dalam file storage/logs/laravel.log untuk keperluan debugging
            Log::error('Error Hapus Gambar TebasLahan: ' . $e->getMessage());

            // Kembalikan respons JSON yang rapi ke frontend
            return response()->json([
                'status' => 'error',
                // Jangan tampilkan pesan error asli database ke user demi keamanan
                'message' => 'Terjadi kesalahan pada server saat menghapus gambar.', 
            ], 500); // 500 adalah kode status standar untuk Internal Server Error
        }
    }

    // Show - Admin & Pemilik bisa lihat
    public function show(Property $building)
    {
        if ($building->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403, 'Akses ditolak');
        }

        return Auth::user()->role === 'admin'
            ? view('admin.buildings.show', compact('building'))
            : view('user.buildings.show', compact('building'));
    }

    // Mengarsipkan properti di tempat sampah (mengubah status menjadi dihapus atau terjual)
    public function archive(Request $request, Property $property)
    {
        // 1. Otorisasi
        if ($property->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403, 'Akses ditolak');
        }

        $reasonKey = $request->reason;
        $otherReasonText = $request->other_reason;

        // 2. Logika Penentuan Status Baru
        // Jika terjual dari tebaslahan, status = terjual. Selain itu = dihapus.
        $newStatus = ($reasonKey === 'tebaslahan') ? 'terjual' : 'dihapus';

        // 3. Mapping teks alasan untuk disimpan di kolom deleted_reason
        $reasons = [
            'belum_terjual' => 'Belum Terjual',
            'tebaslahan'    => 'Terjual melalui platform Tebaslahan',
            'sosmed'        => 'Terjual melalui Media Sosial',
            'lainnya'       => 'Lainnya: ' . $otherReasonText,
        ];

        $property->update([
            'status'         => $newStatus,
            'deleted_reason' => $reasons[$reasonKey] ?? 'Tanpa alasan spesifik',
            'is_tersedia'    => false, // Otomatis tidak tersedia di web
        ]);

        $message = ($newStatus === 'terjual') 
            ? 'Selamat! Properti berhasil ditandai sebagai Terjual.' 
            : 'Properti telah berhasil dihapus dari daftar aktif.';

        return back()->with('success', $message);
    }

    // Tandai sebagai sudah disewa
    public function toggleAvailability(Property $property)
    {
        // 1. Otorisasi: Pastikan hanya pemilik atau admin yang bisa akses
        if (Auth::id() !== $property->user_id && Auth::user()->role !== 'admin') {
            abort(403, 'Anda tidak memiliki izin untuk mengubah status properti ini.');
        }

        // 2. Toggle Status: Gunakan operator NOT (!) untuk membalikkan nilai boolean
        $property->update([
            'is_tersedia' => !$property->is_tersedia
        ]);

        // 3. Pesan Dinamis: Memberikan feedback yang lebih jelas ke user
        $status = $property->is_tersedia ? 'tersedia kembali' : 'Sedang disewa';
        
        return back()->with('success', "Properti berhasil ditandai sebagai {$status}.");
    }

    // Toggle tampilkan website
    public function toggleVisibility(Property $property)
    {
        // 1. Otorisasi: Pastikan hanya pemilik atau admin yang bisa mengubah
        if (Auth::id() !== $property->user_id && Auth::user()->role !== 'admin') {
            abort(403, 'Akses ditolak: Anda tidak memiliki izin untuk mengubah visibilitas properti ini.');
        }

        // 2. Logika Toggle: Tentukan status baru
        $isNowActive = $property->status !== 'aktif'; 
        $newStatus = $isNowActive ? 'aktif' : 'non-aktif';

        $property->update(['status' => $newStatus]);

        // 3. Pesan Feedback yang Dinamis
        $message = $isNowActive 
            ? 'Properti sekarang tampil di website.' 
            : 'Properti berhasil disembunyikan dari website.';

        return back()->with('success', $message);
    }

    public function verifyProperty(Property $property)
    {
        // 1. Otorisasi: Pastikan hanya pemilik atau admin yang bisa akses
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Anda tidak memiliki izin untuk memverifikasi properti ini.');
        }

        // 2. Verifikasi Properti
        $property->update([
            'status' => 'aktif'
        ]);

        // 3. Pesan Dinamis: Memberikan feedback yang lebih jelas ke user        
        return back()->with('success', "Properti ini telah berhasil diverifikasi.");
    }
}