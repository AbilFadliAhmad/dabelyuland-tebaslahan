<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Property;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class AdminController extends Controller
{
    public function dashboard()
    {
        $today = Carbon::today()->toDateString();
        $yesterday = Carbon::yesterday()->toDateString();

        // Membuat array 7 hari terakhir (dari 6 hari lalu sampai hari ini)
        $last7Days = collect();
        for ($i = 6; $i >= 0; $i--) {
            $last7Days->push(Carbon::today()->subDays($i)->toDateString());
        }
        $sevenDaysAgo = $last7Days->first();

        // ==========================================
        // 2. QUERY OPTIMASI (MINIM KONEKSI DATABASE)
        // ==========================================

        // Query A: Properti & User Baru Hari Ini (Bisa digabung jika mau, namun 2 query terpisah ini sudah cukup cepat)
        $propertiesStats = Property::whereDate('updated_at', $today)
            ->selectRaw('status, COUNT(*) as count, SUM(harga) as total_harga')
            ->groupBy('status')
            ->get()
            ->keyBy('status'); // Mengubah key array menjadi nama status (contoh: 'terjual', 'tersedia')
        
        $propertiesCountToday = $propertiesStats->except(['terjual', 'draft'])->sum('count');
        $propertiesSoldToday = (object) [
            'count' => $propertiesStats->get('terjual')->count ?? 0,
            'total_harga' => $propertiesStats->get('terjual')->total_harga ?? 0,
        ];

        $usersToday = User::where('role', 'user')->whereDate('created_at', $today)->count();

        // Query B: Ambil DATA WEBSITE ANALYTICS (Kunjungan Global) langsung 2 hari dalam 1 Query
        $webAnalytics2Days = DB::table('website_analytics')
            ->whereBetween('date', [$yesterday, $today])
            ->select('date', 'views_count')
            ->get()
            ->keyBy('date');

        // Query C: Ambil DATA PROPERTY ANALYTICS (Klik WA & Sumber) langsung 7 hari dalam 1 Query
        $propAnalytics7Days = DB::table('property_analytics')
            ->whereBetween('date', [$sevenDaysAgo, $today])
            ->selectRaw('
                date,
                SUM(views_count) as views_count, 
                SUM(whatsapp_clicks_count) as total_wa,
                SUM(source_wa) as total_src_wa,
                SUM(source_fb) as total_src_fb,
                SUM(source_ig) as total_src_ig,
                SUM(source_twitter) as total_src_twitter,
                SUM(source_other) as total_src_other
            ')
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        // Query D: Ambil Semua harga Property
        $totalPriceAllProperty = Property::all()->sum('harga');

        // Query E: Ambil Semua transaksi yang berhasil settlement
        $transactionStats = Transaction::whereIn(DB::raw('DATE(created_at)'), [$yesterday, $today])
            ->where('status', 'settlement')
            ->selectRaw('DATE(created_at) as tanggal, SUM(price) as total_harga, COUNT(*) as total_transaksi')
            ->groupBy('tanggal')
            ->get()
            ->keyBy('tanggal');
        
        
        $transactionCountToday = $transactionStats->get($today)->total_transaksi ?? 0;
        $transactionToday = $transactionStats->get($today)->total_harga ?? 0;
        $transactionYesterday = $transactionStats->get($yesterday)->total_harga ?? 0;

        $transactionPercent = $transactionYesterday > 0 
            ? round((($transactionToday - $transactionYesterday) / $transactionYesterday) * 100) 
            : ($transactionToday > 0 ? 100 : 0);

        // ==========================================
        // 3. EKSTRAKSI DATA KARTU METRIK (TODAY VS YESTERDAY)
        // ==========================================

        // Ambil data dari hasil Query B (Website Analytics)
        $webToday = $webAnalytics2Days->get($today);
        $webYesterday = $webAnalytics2Days->get($yesterday);
        $viewsToday = $webToday ? $webToday->views_count : 0;
        $viewsYesterday = $webYesterday ? $webYesterday->views_count : 0;

        // Ambil data dari hasil Query C (Property Analytics)
        $propToday = $propAnalytics7Days->get($today);
        $propYesterday = $propAnalytics7Days->get($yesterday);
        $waToday = $propToday ? $propToday->total_wa : 0;
        $waYesterday = $propYesterday ? $propYesterday->total_wa : 0;

        // Hitung Persentase Kenaikan/Penurunan
        $viewsPercent = $viewsYesterday > 0 ? round((($viewsToday - $viewsYesterday) / $viewsYesterday) * 100) : ($viewsToday > 0 ? 100 : 0);
        $waPercent = $waYesterday > 0 ? round((($waToday - $waYesterday) / $waYesterday) * 100) : ($waToday > 0 ? 100 : 0);

        // ==========================================
        // 4. EKSTRAKSI TRAFFIC SOURCES (HANYA HARI INI)
        // ==========================================
        $srcWa = $propToday ? $propToday->total_src_wa : 0;
        $srcFb = $propToday ? $propToday->total_src_fb : 0;
        $srcIg = $propToday ? $propToday->total_src_ig : 0;
        $srcTwitter = $propToday ? $propToday->total_src_twitter : 0;
        $srcOther = $propToday ? $propToday->total_src_other : 0;

        $totalSources = ($srcWa + $srcFb + $srcIg + $srcTwitter + $srcOther) ?: 1;

        $trafficSources = [
            'wa'      => round(($srcWa / $totalSources) * 100),
            'fb'      => round(($srcFb / $totalSources) * 100),
            'ig'      => round(($srcIg / $totalSources) * 100),
            'twitter' => round(($srcTwitter / $totalSources) * 100),
            'other'   => round(($srcOther / $totalSources) * 100),
        ];

        // ==========================================
        // 5. EKSTRAKSI DATA CHART (7 HARI TERAKHIR)
        // ==========================================
        $chartLabels = [];
        $chartViews = [];
        $chartWa = [];

        foreach ($last7Days as $date) {
            $chartLabels[] = Carbon::parse($date)->translatedFormat('l'); // Nama hari otomatis lokal Indonesia
            
            // Total pengunjung Properti 
            $chartViews[] = $propAnalytics7Days->has($date) ? (int) $propAnalytics7Days[$date]->views_count : 0;
            
            // Total klik WA properti
            $chartWa[] = $propAnalytics7Days->has($date) ? (int) $propAnalytics7Days[$date]->total_wa : 0;
        }


        $topProperties = Property::with('mainImage')
            ->join('property_analytics', function ($join) use ($today) {
                $join->on('properties.id', '=', 'property_analytics.property_id')
                     ->where('property_analytics.date', '=', $today);
            })
            ->orderByDesc('property_analytics.views_count')
            ->select('properties.*', 'property_analytics.views_count')
            ->take(3)
            ->get();

        // 7. WORST 3 Properti Kurang Diminati (HARI INI)
        // SANGAT PENTING: Gunakan LEFT JOIN
        $worstProperties = Property::with('mainImage')
            ->leftJoin('property_analytics', function ($join) use ($today) {
                $join->on('properties.id', '=', 'property_analytics.property_id')
                     ->where('property_analytics.date', '=', $today);
            })
            ->where('properties.status', 'aktif')
            // Gunakan COALESCE untuk mengubah nilai NULL (jika tidak ada data analitik) menjadi 0
            ->orderByRaw('COALESCE(property_analytics.views_count, 0) ASC')
            ->select('properties.*', DB::raw('COALESCE(property_analytics.views_count, 0) as views_count'))
            ->take(10)
            ->get();

        return view('admin.dashboard', compact(
             'propertiesCountToday', 'propertiesSoldToday', 'usersToday', 
             'totalPriceAllProperty', 'transactionToday', 'transactionPercent',
             'transactionCountToday',
            'waToday', 'waPercent', 'viewsToday', 'viewsPercent',
            'trafficSources', 'chartLabels', 'chartViews', 'chartWa',
            'topProperties', 'worstProperties'
        ));
    }

    public function dashboardDummy()
    {
        // Data statis untuk keperluan demo cepat
        $data = [
            // Section 1: Metrik Utama & Marketing
            'viewsToday' => 12540,
            'viewsPercent' => 15.4,
            'waToday' => 342,
            'waPercent' => 8.2,
            'propertiesCountToday' => 24,
            'usersToday' => 56,

            // Section 2: Pendapatan
            'transactionToday' => 3500000, // Rp 3.500.000 (Membership/Koin)
            'transactionPercent' => 12.5,
            'transactionCountToday' => 45,
            
            'propertiesSoldToday' => (object) [
                'total_harga' => 2500000000, // Rp 2.500.000.000
                'count' => 4
            ],
            
            'totalPriceAllProperty' => 85000000000, // Rp 85.000.000.000 (85 Miliar)

            // Section 3: Traffic Sources
            'trafficSources' => [
                'wa' => 45,
                'fb' => 25,
                'ig' => 15,
                'twitter' => 10,
                'other' => 5
            ],

            // Section 4: Top 3 Best Properties
            'topProperties' => [
                (object) [
                    'judul' => 'Villa Mewah Eksklusif di Canggu, Bali',
                    'views_count' => 4250,
                    'mainImage' => null // Dibuat null agar memicu gambar placeholder dari via.placeholder.com
                ],
                (object) [
                    'judul' => 'Rumah Minimalis Modern Jakarta Selatan',
                    'views_count' => 3890,
                    'mainImage' => null
                ],
                (object) [
                    'judul' => 'Apartemen Studio Full Furnished Pusat Kota',
                    'views_count' => 2100,
                    'mainImage' => null
                ]
            ],

            // Section 4: Top 3 Worst Properties
            'worstProperties' => [
                (object) [
                    'judul' => 'Kavling Tanah Kosong Pinggir Tol',
                    'views_count' => 12,
                    'mainImage' => null
                ],
                (object) [
                    'judul' => 'Rumah Tua Butuh Renovasi Total',
                    'views_count' => 18,
                    'mainImage' => null
                ],
                (object) [
                    'judul' => 'Ruko 2 Lantai Mangkrak Pembangunan',
                    'views_count' => 25,
                    'mainImage' => null
                ]
            ],

            // Section 3: Chart Data (Trend 7 Hari Terakhir)
            'chartLabels' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'],
            'chartViews' => [8500, 9200, 10500, 9800, 11000, 14200, 12540],
            'chartWa' => [180, 210, 250, 230, 290, 410, 342],
        ];

        // Ganti 'admin.dashboard' dengan nama file view blade Anda
        return view('admin.dashboard', $data); 
    }
}
