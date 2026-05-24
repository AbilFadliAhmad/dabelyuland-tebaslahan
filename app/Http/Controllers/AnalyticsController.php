<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/* Semua pemanggilan table tidak menggunakan model karena 
tidak ada relasi dan metode ini lebih cepat karena tidak ada overhead model dari orm eloquent */ 

class AnalyticsController extends Controller
{

    public function trackViewWebsite() {
        // Pastikan backend juga menggunakan tanggal WIB (Asia/Jakarta)
        $todayWIB = Carbon::now('Asia/Jakarta')->toDateString();

        // Update atau Insert ke tabel analitik
        DB::table('website_analytics')->updateOrInsert(
            ['date' => $todayWIB],
            ['views_count' => DB::raw('views_count + 1')]
        );

        return response()->json(['status' => 'success']);
    }

    public function trackViewProperty(Request $request) {
        $source = strtolower($request->input('source', 'other'));
        $propertyId = $request->property_id;

        // Tentukan kolom database
        $sourceColumn = match($source) {
            'whatsapp', 'wa' => 'source_wa',
            'facebook', 'fb' => 'source_fb',
            'instagram', 'ig' => 'source_ig',
            'twitter', 'x' => 'source_twitter',
            default => 'source_other',
        };

        // Pastikan backend juga menggunakan tanggal WIB (Asia/Jakarta)
        $todayWIB = Carbon::now('Asia/Jakarta')->toDateString();

        // Update atau Insert ke tabel analitik
        DB::table('property_analytics')->updateOrInsert(
            ['property_id' => $propertyId, 'date' => $todayWIB],
            [
                'views_count' => DB::raw('views_count + 1'),
                $sourceColumn => DB::raw("{$sourceColumn} + 1")
            ]
        );

        return response()->json(['status' => 'success']);
    }

    public function trackClickWhatsapp(Request $request) {
        $propertyId = $request->property_id;

        // Pastikan backend juga menggunakan tanggal WIB (Asia/Jakarta)
        $todayWIB = Carbon::now('Asia/Jakarta')->toDateString();

        // Update atau Insert ke tabel analitik
        DB::table('property_analytics')->updateOrInsert(
            ['property_id' => $propertyId, 'date' => $todayWIB],
            ['whatsapp_clicks_count' => DB::raw('whatsapp_clicks_count + 1')]
        );

        return response()->json(['status' => 'success']);
    }
}
