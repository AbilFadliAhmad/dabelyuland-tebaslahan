<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
   public function getProperties(Request $request) 
    {
        // 1. Ambil array ID dari request
        $ids = $request->ids;

        // 2. Query ke database
        $properties = Property::whereIn('id', $ids)
            ->where('status', 'aktif')
            ->with('mainImage') // Mengambil relasi gambar utama
            ->select('id', 'kota', 'judul', 'slug', 'harga') // Ambil kolom yang dibutuhkan saja
            ->get();

        // 3. Transformasi data untuk menyertakan URL gambar
        return $properties->map(function($item) {
            // Ambil path gambar jika ada, jika tidak gunakan gambar default
            $imagePath = $item->mainImage ? $item->mainImage->image_path : null;
            
            return [
                'id'     => $item->id,
                'kota'   => $item->kota,
                'judul'  => $item->judul,
                'slug'   => $item->slug,
                'harga'  => $item->harga,
                // Sesuaikan format URL gambar (misal menggunakan storage)
                'gambar' => $imagePath 
                            ? asset('storage/' . $imagePath . '-image_low.webp') 
                            : asset('frontside/img/default-property.jpg')
            ];
        });
    }
}
