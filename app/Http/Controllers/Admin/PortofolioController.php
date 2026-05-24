<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Portofolio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PortofolioController extends Controller
{
    public function index()
    {
        $portofolios = Portofolio::all();
        return view('admin.portofolio.list', compact('portofolios'));
    }
    
    public function create()
    {
        $isEdit = false;
        return view('admin.portofolio.form', compact('isEdit'));
    }

    public function store(Request $request)
    {
        // Validasi input
        $validatedData = $request->validate([
            'judul' => 'required|string|max:255',
            'pemilik' => 'required|string|max:255',
            'alamat' => 'required|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // Maksimal 2MB
        ]);

        // Upload gambar jika ada
        if ($request->hasFile('gambar')) {
            $path = $request->file('gambar')->store('portofolios', 'public');
            $validatedData['gambar'] = $path; // Simpan path relatif ke dalam database
        }

        // Simpan ke database
        Portofolio::create($validatedData);

        return redirect()->route('portofolios.index')->with('success', 'Portofolio berhasil ditambahkan.');
    }

    public function edit(Portofolio $portofolio)
    {
        $isEdit = true;
        return view('admin.portofolio.form', compact('portofolio'), compact('isEdit'));
    }

    public function update(Request $request, Portofolio $portofolio)
    {
        // Validasi data
        $validatedData = $request->validate([
            'judul' => 'required|string|max:255',
            'tipe' => 'required|in:design,build',
            'pemilik' => 'required|string|max:255',
            'alamat' => 'required|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        // Jika ada file gambar baru diupload
        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada
            if ($portofolio->gambar && Storage::disk('public')->exists($portofolio->gambar)) {
                Storage::disk('public')->delete($portofolio->gambar);
            }

            // Simpan gambar baru
            $path = $request->file('gambar')->store('portofolios', 'public');
            $validatedData['gambar'] = $path;
        }

        // Update data ke database
        $portofolio->update($validatedData);

        return redirect()->route('portofolios.index')->with('success', 'Portofolio berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Portofolio  $portofolio
     * @return \Illuminate\Http\Response
     */
    public function destroy(Portofolio $portofolio)
    {
        $portofolio->delete();

        return redirect()->route('portofolios.index')->with('success', 'Portofolio berhasil dihapus.');
    }
}