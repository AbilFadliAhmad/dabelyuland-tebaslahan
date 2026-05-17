<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        // Gunakan cursorPaginate (ID > last_id) untuk performa maksimal
        /** @var \Illuminate\Pagination\CursorPaginator $allProperties */
        $allProperties = $this->applyFilters($request)->cursorPaginate(10);

        // Deteksi Request AJAX
        if ($request->ajax()) {
            $html = '';
            foreach ($allProperties as $property) {
                // Render partial cardProperty langsung di sini
                $html .= view('partials.property.cardProperty', [
                    'item' => $property,
                    'isSearch' => true,
                ])->render();
            }

            return response()->json([
                'html' => $html,
                'nextCursor' => $allProperties->nextCursor() ? $allProperties->nextCursor()->encode() : null,
                'hasMore' => $allProperties->hasMorePages()
            ]);
        }

        $propertyTypes = ['apartemen','rumah','ruko','kantor','gudang','tanah'];
        return view('frontside.search', compact('allProperties', 'propertyTypes'));
    }

    private function applyFilters(Request $request)
    {
        // Eager loading mainImage agar tidak terjadi N+1 query
        $query = Property::with('mainImage')->where('status', 'aktif');

        $query->when($request->lokasi, function ($q) use ($request) {
            return $q->where('kota', 'like', '%' . $request->lokasi . '%');
        });

        $query->when($request->kategori_slug, function ($q) use ($request) {
            return $q->where('tipe', $request->kategori_slug);
        });

        if ($request->harga) {
            $range = explode('-', $request->harga);
            if (count($range) == 2) {
                $query->whereBetween('harga', [(int)$range[0], (int)$range[1]]);
            }
        }

        // Pastikan sorting konsisten untuk cursorPaginate
        return $query->latest('id'); 
    }
}