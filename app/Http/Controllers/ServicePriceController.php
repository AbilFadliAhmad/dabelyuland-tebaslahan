<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ServicePrice; // Pastikan model ini sudah kamu buat

class ServicePriceController extends Controller
{
    /**
     * Menyimpan data tarif promo baru ke database
     */
    public function store(Request $request)
    {
        // 1. Validasi bersyarat: nama_durasi dan jumlah_hari hanya wajib jika jenis_layanan BUKAN 'sundul'
        $request->validate([
            'jenis_layanan' => 'required|in:highlight,rekomendasi,banner,sundul',
            'nama_durasi'   => 'required_unless:jenis_layanan,sundul|nullable|string|max:255',
            'jumlah_hari'   => 'required_unless:jenis_layanan,sundul|nullable|integer|min:0',
            'biaya_koin'    => 'required|integer|min:0',
        ]);

        // 2. Pengecekan Duplikasi khusus untuk 'sundul'
        if ($request->jenis_layanan === 'sundul') {
            $exists = ServicePrice::where('jenis_layanan', 'sundul')->exists();
            if ($exists) {
                return redirect()->back()
                    ->with('error', 'Tarif untuk Token Sundul sudah ada. Anda hanya boleh memiliki satu tarif sundul.');
            }
        }

        // 3. Menangani nilai default karena kolom DB bersifat NOT NULL
        $namaDurasi = $request->jenis_layanan === 'sundul' ? 'Token Sundul' : $request->nama_durasi;
        $jumlahHari = $request->jenis_layanan === 'sundul' ? 0 : $request->jumlah_hari;

        // 4. Simpan ke database
        ServicePrice::create([
            'jenis_layanan' => $request->jenis_layanan,
            'nama_durasi'   => $namaDurasi,
            'jumlah_hari'   => $jumlahHari,
            'biaya_koin'    => $request->biaya_koin,
        ]);

        return redirect()->route('admin.koin.index')->with('success', 'Tarif Promo berhasil ditambahkan!');
    }

    /**
     * Mengupdate data tarif promo yang sudah ada
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'jenis_layanan' => 'required|in:highlight,rekomendasi,banner,sundul',
            'nama_durasi'   => 'required_unless:jenis_layanan,sundul|nullable|string|max:255',
            'jumlah_hari'   => 'required_unless:jenis_layanan,sundul|nullable|integer|min:0',
            'biaya_koin'    => 'required|integer|min:0',
        ]);

        $promo = ServicePrice::findOrFail($id);
        
        $promo->update([
            'jenis_layanan' => $request->jenis_layanan,
            'nama_durasi'   => $request->nama_durasi,
            'jumlah_hari'   => $request->jumlah_hari,
            'biaya_koin'    => $request->biaya_koin,
        ]);

        return redirect()->route('admin.koin.index')->with('success', 'Berhasil memperbarui Data Layanan Tarif!');
    }

    /**
     * Menghapus data tarif promo
     */
    public function destroy($id)
    {
        $promo = ServicePrice::findOrFail($id);
        $promo->delete();

        return redirect()->route('admin.koin.index')->with('success', 'Tarif Promo berhasil dihapus!');
    }
}