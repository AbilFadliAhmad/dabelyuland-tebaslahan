<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CoinPackage;
use App\Models\ServicePrice;

class CoinPackageController extends Controller
{
    
    public function index()
    {
        // Mengambil data Paket Top Up Koin
        $packages = CoinPackage::orderBy('harga', 'asc')->get(); 
        
        // Mengambil data Tarif Promo
        $servicePrices = ServicePrice::orderBy('jenis_layanan', 'asc')->orderBy('jumlah_hari', 'asc')->get();

        return view('admin.koin.index', compact('packages', 'servicePrices'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'koin' => 'required|integer|min:1',
            'harga' => 'required|integer|min:1',
            'theme' => 'required',
        ]);


        if ($request->has('is_best')) {
            CoinPackage::where('is_best', true)->update(['is_best' => false]);
        }

        CoinPackage::create([
            'koin' => $request->koin,
            'harga' => $request->harga,
            'badge' => $request->badge,
            'theme' => $request->theme,
            'desc' => $request->desc,
            'saving' => $request->saving,
            'is_best' => $request->has('is_best'),
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->back()->with('success', 'Paket koin berhasil ditambahkan!');
    }

    public function destroy($id)
    {
        CoinPackage::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Paket koin berhasil dihapus!');
    }

    public function update(Request $request, $id)
    {
        $package = CoinPackage::findOrFail($id);

        $request->validate([
            'koin' => 'required|integer|min:1',
            'harga' => 'required|integer|min:1',
            'theme' => 'required',
        ]);

        // Cek best value
        if ($request->has('is_best') && !$package->is_best) {
            CoinPackage::where('is_best', true)->update(['is_best' => false]);
        }

        $package->update([
            'koin' => $request->koin,
            'harga' => $request->harga,
            'badge' => $request->badge,
            'theme' => $request->theme,
            'desc' => $request->desc,
            'saving' => $request->saving,
            'is_best' => $request->has('is_best'),
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->back()->with('success', 'Paket koin berhasil diperbarui!');
    }
}