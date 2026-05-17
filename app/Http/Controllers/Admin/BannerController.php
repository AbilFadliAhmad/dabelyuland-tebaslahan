<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class BannerController extends Controller
{
    public function index()
    {
        $images = Banner::all();
        return view('admin.banner.list', compact('images'));
    }

    public function create()
    {
        $isEdit = false;
        return view('admin.banner.form', compact('isEdit'));
    }

    public function store(Request $request)  
    {
        $validatedData = $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Maksimal 2MB
        ]);

        if ($request->hasFile('image')) {
            $validatedData['image'] = $request->file('image')->store('banners', 'public');
        }
        $user = Auth::user();
        $validatedData['is_active'] = $user->role == 'admin' ? true : false;
        $validatedData['user_id'] = $user->id;

        Banner::create($validatedData);
        return redirect()->route('banner.index')->with('success', 'Gambar berhasil ditambahkan.');
    }

    public function edit(Banner $banner)
    {
        $isEdit = true;
        return view('admin.banner.form', compact('banner'), compact('isEdit'));
    }

    public function update(Request $request, Banner $banner)
    {
        $validatedData = $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Maksimal 2MB
            'status' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($banner->image) {
                Storage::disk('public')->delete($banner->image);
            }
            $validatedData['image'] = $request->file('image')->store('banners', 'public');
        }

        $banner->update($validatedData);

        return redirect()->route('banner.index')->with('success', 'Banner berhasil diperbarui.');
    }

    public function destroy(Banner $banner)
    {
        // Hapus gambar dari penyimpanan
        if ($banner->image) {
            Storage::disk('public')->delete($banner->image);
        }

        $banner->delete();

        return redirect()->route('banner.index')->with('success', 'Banner berhasil dihapus.');
    }

    public function toggleStatus(Request $request, $id)
    {
        // Sesuaikan nama Model dengan model bannermu (misal: Banner atau Image)
        $banner = \App\Models\Banner::findOrFail($id); 
        
        $banner->is_active = $request->is_active;
        $banner->save();

        return response()->json([
            'success' => true,
            'message' => 'Status berhasil diubah'
        ]);
    }
}