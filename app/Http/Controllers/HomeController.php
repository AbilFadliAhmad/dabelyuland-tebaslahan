<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Banner;
use App\Models\Property;
use App\Models\Testimoni;
use App\Models\PropertyRecommendation;
use App\Models\PropertyHighlight;
// use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    public function index(Request $request)
    {

        // 1. Definisikan Query Dasar Rekomendasi (Gunakan property_id untuk kestabilan cursor)
        $queryRekomendasi = PropertyRecommendation::with(['property', 'property.mainImage'])
            ->whereHas('property', function ($q) {
                $q->where('status', 'aktif');
            })
            ->latest('property_id');
        // dd($queryRekomendasi);

        // 2. Isolasi Request AJAX: Hanya proses rekomendasi
        if ($request->ajax()) {
            /** @var \Illuminate\Pagination\CursorPaginator $rekomendasi */
            $rekomendasi = $queryRekomendasi->cursorPaginate(10);

            $html = '';
            foreach ($rekomendasi as $item) {
                $html .= view('partials.property.cardProperty', ['item' => $item])->render();
            }

            return response()->json([
                'html' => $html,
                'next_cursor' => $rekomendasi->nextCursor() ? $rekomendasi->nextCursor()->encode() : null,
                'hasMore' => $rekomendasi->hasMorePages()
            ]);
        }

        // 3. Query Berat: Hanya dijalankan saat pertama kali halaman dimuat (Bukan AJAX)
        $request['lat'] = -7.300000;
        $request['lng'] = 112.2331;
        $highlights = $this->searchPropertyNearby($request); // Query Spasial Berat
        $testimonis = Testimoni::orderBy('created_at', 'desc')->take(3)->get();
        $banners = Banner::where('status', 'aktif')->get();

        // Data awal rekomendasi
        $rekomendasi = $queryRekomendasi->cursorPaginate(1);

        return view('frontside.home', compact('testimonis', 'banners', 'rekomendasi', 'highlights'));
    }

    public function searchPropertyNearby(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        $lat = $request->lat;
        $lng = $request->lng;

        $radii = [100000, 200000, 400000, 800000, 1000000];
        $properties = collect();

        foreach ($radii as $radius) {
            // Perbaikan: Filter status lewat relasi 'property'
            $properties = PropertyHighlight::with(['property', 'property.mainImage'])
                ->terdekat($lat, $lng, $radius)
                ->whereHas('property', function ($q) {
                    $q->where('status', 'aktif');
                })
                ->where('expired_at', '>', now()) // Pastikan belum expired
                ->limit(3)
                ->get();

            if ($properties->count() >= 3) {
                break;
            }
        }

        return $properties;
    }

    // untuk detail
    public function show(Property $property)
    {
        // Load relasi
        $property = $property->load(['user', 'galleries']);


        // Kirim data ke view
        return view('frontside.property-details', compact('property'));
    }
}
