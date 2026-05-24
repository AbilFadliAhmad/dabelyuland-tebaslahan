<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Cache;

class ReportController extends Controller
{

    public function indexDummy(Request $request)
    {
        // 1. Set rentang waktu (Dummy)
        $periode = $request->input('periode', 'today');
        $customDate = $request->input('custom_date', null);

        // ==========================================
        // DATA STATISTIK SUMMARY KARTU ATAS (DUMMY)
        // ==========================================
        $webViews = 15420;
        $propViews = 8250;
        $waClicks = 320;
        
        $revMembership = 5000000;
        $revKoin = 2500000;
        $revTotal = $revMembership + $revKoin;
        
        $totalUsers = 120;
        $totalProperties = 45;

        // Dummy Object untuk propertyAnalytics
        $propAnalytics = (object) [
            'wa' => 150,
            'ig' => 200,
            'twitter' => 50,
            'fb' => 80,
            'other' => 40
        ];

        // ==========================================
        // DATA DUMMY UNTUK GRAFIK (CHART)
        // ==========================================
        // Contoh untuk 7 hari terakhir
        $chartLabels = ['14 Mei', '15 Mei', '16 Mei', '17 Mei', '18 Mei', '19 Mei', '20 Mei'];
        
        $dataWeb = [1200, 1500, 1100, 1800, 2000, 2200, 5620];
        $dataProp = [500, 600, 450, 900, 1200, 1100, 3500];
        $dataWa = [20, 30, 25, 40, 50, 60, 95];
        $dataMembership = [100000, 200000, 150000, 300000, 500000, 400000, 3350000];
        $dataKoin = [50000, 50000, 100000, 200000, 300000, 400000, 1400000];
        $dataUsers = [5, 10, 8, 15, 20, 22, 40];
        $dataProperties = [2, 3, 1, 5, 8, 10, 16];
        $dataKomisi = array_map(function($m, $k) { return ($m + $k) * 0.10; }, $dataMembership, $dataKoin);

        // ==========================================
        // DATA DUMMY TRAFFIC SOURCE
        // ==========================================
        $chartTrafficSource = [
            (int) $propAnalytics->wa,
            (int) $propAnalytics->ig,
            (int) $propAnalytics->twitter,
            (int) $propAnalytics->fb,
            (int) $propAnalytics->other
        ];
        
        $totalTraffic = array_sum($chartTrafficSource);
        $trafficSources = [
            'wa'      => round((($propAnalytics->wa ?? 0) / $totalTraffic) * 100),
            'ig'      => round((($propAnalytics->ig ?? 0) / $totalTraffic) * 100),
            'twitter' => round((($propAnalytics->twitter ?? 0) / $totalTraffic) * 100),
            'fb'      => round((($propAnalytics->fb ?? 0) / $totalTraffic) * 100),
            'other'   => round((($propAnalytics->other ?? 0) / $totalTraffic) * 100),
        ];

        return view('admin.report', compact(
            'periode', 'customDate',
            'webViews', 'propViews', 'waClicks',
            'revMembership', 'revKoin', 'revTotal',
            'totalUsers', 'totalProperties',
            'chartTrafficSource', 'trafficSources', 'propAnalytics',
            'chartLabels'
        ))->with([
            'chartDataWeb' => $dataWeb,
            'chartDataProp' => $dataProp,
            'chartDataWa' => $dataWa,
            'chartDataMembership' => $dataMembership,
            'chartDataKoin' => $dataKoin,
            'chartDataUsers' => $dataUsers,
            'chartDataProperties' => $dataProperties,
            'chartDataKomisi' => $dataKomisi
        ]);
    }
    public function index(Request $request)
    {
        $periode = $request->input('periode', 'this_week');
        $customDate = $request->input('custom_date');

        // 1. Membuat Cache Key yang unik berdasarkan filter yang sedang aktif
        // Menggunakan md5 jika sewaktu-waktu customDate berisi string yang panjang/berjarak
        $cacheKey = 'admin_report_' . $periode . '_' . md5($customDate ?? '');

        // 2. Bungkus proses pengambilan data yang berat ke dalam Cache::remember
        // 1800 detik sama dengan 30 menit
        $reportData = Cache::remember($cacheKey, 1800, function () use ($periode, $customDate) {
            
            $now = Carbon::now();

            switch ($periode) {
                case 'today':
                    $start = $now->copy()->startOfDay();
                    $end = $now->copy()->endOfDay();
                    break;
                case 'this_week':
                    $start = $now->copy()->startOfWeek();
                    $end = $now->copy()->endOfWeek();
                    break;
                case 'this_year':
                    $start = $now->copy()->startOfYear();
                    $end = $now->copy()->endOfYear();
                    break;
                case 'custom':
                    if ($customDate) {
                        $dates = explode(' - ', $customDate);
                        $start = Carbon::parse($dates[0])->startOfDay();
                        $end = Carbon::parse($dates[1])->endOfDay();
                    } else {
                        $start = $now->copy()->startOfMonth();
                        $end = $now->copy()->endOfMonth();
                    }
                    break;
                case 'this_month':
                default:
                    $start = $now->copy()->startOfMonth();
                    $end = $now->copy()->endOfMonth();
                    break;
            }

            // ==========================================
            // DATA STATISTIK SUMMARY KARTU ATAS
            // ==========================================
            $webViews = DB::table('website_analytics')->whereBetween('date', [$start->toDateString(), $end->toDateString()])->sum('views_count');
            
            $propAnalytics = DB::table('property_analytics')->whereBetween('date', [$start->toDateString(), $end->toDateString()])
                ->selectRaw('SUM(views_count) as prop_views, SUM(whatsapp_clicks_count) as wa_clicks, SUM(source_wa) as wa, SUM(source_fb) as fb, SUM(source_ig) as ig, SUM(source_twitter) as twitter, SUM(source_other) as other')->first();
                
            $propViews = $propAnalytics->prop_views ?? 0;
            $waClicks = $propAnalytics->wa_clicks ?? 0;

            $revenue = DB::table('transactions')->whereBetween('created_at', [$start, $end])->where('status', 'settlement')
                ->selectRaw("SUM(CASE WHEN tipe = 'membership' THEN price ELSE 0 END) as rev_membership")
                ->selectRaw("SUM(CASE WHEN tipe = 'koin' THEN price ELSE 0 END) as rev_koin")->first();
                
            $revMembership = $revenue->rev_membership ?? 0;
            $revKoin = $revenue->rev_koin ?? 0;
            $revTotal = $revMembership + $revKoin;

            $totalUsers = DB::table('users')->whereBetween('created_at', [$start, $end])->count();
            $totalProperties = DB::table('properties')->whereBetween('created_at', [$start, $end])->count();

            // ==========================================
            // PERSIAPAN ARRAY UNTUK GRAFIK (CHART)
            // ==========================================
            $diffInDays = $start->diffInDays($end);
            $isMonthly = $diffInDays > 60;

            $chartLabels = [];
            $dataWeb = [];
            $dataProp = [];
            $dataWa = [];
            $dataMembership = [];
            $dataKoin = [];
            $dataUsers = [];
            $dataProperties = [];
            $dataKomisi = [];

            if ($isMonthly) {
                $period = CarbonPeriod::create($start->copy()->startOfMonth(), '1 month', $end->copy()->startOfMonth());
                foreach ($period as $dt) {
                    $key = $dt->format('Y-m');
                    $chartLabels[] = $dt->translatedFormat('M Y');
                    $dataWeb[$key] = $dataProp[$key] = $dataWa[$key] = $dataMembership[$key] = $dataKoin[$key] = $dataUsers[$key] = $dataProperties[$key] = $dataKomisi[$key] = 0;
                }
            } else {
                $period = CarbonPeriod::create($start, '1 day', $end);
                foreach ($period as $dt) {
                    $key = $dt->format('Y-m-d');
                    $chartLabels[] = $dt->translatedFormat('d M');
                    $dataWeb[$key] = $dataProp[$key] = $dataWa[$key] = $dataMembership[$key] = $dataKoin[$key] = $dataUsers[$key] = $dataProperties[$key] = $dataKomisi[$key] = 0;
                }
            }

            $dateFormatSQL = $isMonthly ? 'DATE_FORMAT(date, "%Y-%m")' : 'date';
            $createdFormatSQL = $isMonthly ? 'DATE_FORMAT(created_at, "%Y-%m")' : 'DATE(created_at)';

            // 1. Query Web Analytics
            $webChartData = DB::table('website_analytics')->whereBetween('date', [$start->toDateString(), $end->toDateString()])
                ->selectRaw("$dateFormatSQL as label, SUM(views_count) as total")->groupBy('label')->get();
            foreach ($webChartData as $row) { if (isset($dataWeb[$row->label])) $dataWeb[$row->label] = (int) $row->total; }

            // 2. Query Property Analytics
            $propChartData = DB::table('property_analytics')->whereBetween('date', [$start->toDateString(), $end->toDateString()])
                ->selectRaw("$dateFormatSQL as label, SUM(views_count) as v_total, SUM(whatsapp_clicks_count) as wa_total")->groupBy('label')->get();
            foreach ($propChartData as $row) {
                if (isset($dataProp[$row->label])) {
                    $dataProp[$row->label] = (int) $row->v_total;
                    $dataWa[$row->label] = (int) $row->wa_total;
                }
            }

            // 3. Query Revenue
            $revChartData = DB::table('transactions')->whereBetween('created_at', [$start, $end])->where('status', 'settlement')
                ->selectRaw("$createdFormatSQL as label, SUM(CASE WHEN tipe = 'membership' THEN price ELSE 0 END) as mem_total, SUM(CASE WHEN tipe = 'koin' THEN price ELSE 0 END) as koin_total")->groupBy('label')->get();
            foreach ($revChartData as $row) {
                if (isset($dataMembership[$row->label])) {
                    $dataMembership[$row->label] = (int) $row->mem_total;
                    $dataKoin[$row->label] = (int) $row->koin_total;
                }
            }

            // 4. Query Users
            $usersChartData = DB::table('users')->whereBetween('created_at', [$start, $end])
                ->selectRaw("$createdFormatSQL as label, COUNT(id) as total")->groupBy('label')->get();
            foreach ($usersChartData as $row) { if (isset($dataUsers[$row->label])) $dataUsers[$row->label] = (int) $row->total; }

            // 5. Query Properties Aktif
            $propItemsChartData = DB::table('properties')->where('status', 'aktif')->whereBetween('created_at', [$start, $end])
                ->selectRaw("$createdFormatSQL as label, COUNT(id) as total")->groupBy('label')->get();
            foreach ($propItemsChartData as $row) { if (isset($dataProperties[$row->label])) $dataProperties[$row->label] = (int) $row->total; }

            // 5b. Query Properties Terjual
            $propSoldItemsChartData = DB::table('properties')->where('status', 'terjual')->whereBetween('updated_at', [$start, $end])
                ->selectRaw("$createdFormatSQL as label, SUM(harga) as total")->groupBy('label')->get();
            foreach ($propSoldItemsChartData as $row) { if (isset($dataKomisi[$row->label])) $dataKomisi[$row->label] = (int) $row->total * 0.1; }
            $komisiBersih = array_sum($dataKomisi);

            // 6. Data untuk Polar Area Chart
            $chartTrafficSource = [
                (int) ($propAnalytics->wa ?? 0),
                (int) ($propAnalytics->ig ?? 0),
                (int) ($propAnalytics->twitter ?? 0),
                (int) ($propAnalytics->fb ?? 0),
                (int) ($propAnalytics->other ?? 0)
            ];
            
            $totalTraffic = array_sum($chartTrafficSource);
            $trafficSources = [
                'wa' => $totalTraffic > 0 ? round((($propAnalytics->wa ?? 0) / $totalTraffic) * 100) : 0,
                'ig' => $totalTraffic > 0 ? round((($propAnalytics->ig ?? 0) / $totalTraffic) * 100) : 0,
                'twitter' => $totalTraffic > 0 ? round((($propAnalytics->twitter ?? 0) / $totalTraffic) * 100) : 0,
                'fb' => $totalTraffic > 0 ? round((($propAnalytics->fb ?? 0) / $totalTraffic) * 100) : 0,
                'other' => $totalTraffic > 0 ? round((($propAnalytics->other ?? 0) / $totalTraffic) * 100) : 0,
            ];

            // Kelompokkan seluruh variabel hasil query ke dalam satu array terstruktur untuk disimpan di cache
            return [
                'komisiBersih' => $komisiBersih,
                'webViews' => $webViews,
                'propViews' => $propViews,
                'waClicks' => $waClicks,
                'revMembership' => $revMembership,
                'revKoin' => $revKoin,
                'revTotal' => $revTotal,
                'totalUsers' => $totalUsers,
                'totalProperties' => $totalProperties,
                'chartTrafficSource' => $chartTrafficSource,
                'trafficSources' => $trafficSources,
                'propAnalytics' => $propAnalytics,
                'chartLabels' => $chartLabels,
                'chartDataWeb' => array_values($dataWeb),
                'chartDataProp' => array_values($dataProp),
                'chartDataWa' => array_values($dataWa),
                'chartDataMembership' => array_values($dataMembership),
                'chartDataKoin' => array_values($dataKoin),
                'chartDataUsers' => array_values($dataUsers),
                'chartDataProperties' => array_values($dataProperties),
                'chartDataKomisi' => array_values($dataKomisi)
            ];
        });

        // 3. Kembalikan data ke View dari hasil Cache ($reportData)
        return view('admin.report', [
            'komisiBersih' => $reportData['komisiBersih'],
            'periode' => $periode,
            'customDate' => $customDate,
            'webViews' => $reportData['webViews'],
            'propViews' => $reportData['propViews'],
            'waClicks' => $reportData['waClicks'],
            'revMembership' => $reportData['revMembership'],
            'revKoin' => $reportData['revKoin'],
            'revTotal' => $reportData['revTotal'],
            'totalUsers' => $reportData['totalUsers'],
            'totalProperties' => $reportData['totalProperties'],
            'chartTrafficSource' => $reportData['chartTrafficSource'],
            'trafficSources' => $reportData['trafficSources'],
            'propAnalytics' => $reportData['propAnalytics'],
            'chartLabels' => $reportData['chartLabels']
        ])->with([
            'chartDataWeb' => $reportData['chartDataWeb'],
            'chartDataProp' => $reportData['chartDataProp'],
            'chartDataWa' => $reportData['chartDataWa'],
            'chartDataMembership' => $reportData['chartDataMembership'],
            'chartDataKoin' => $reportData['chartDataKoin'],
            'chartDataUsers' => $reportData['chartDataUsers'],
            'chartDataProperties' => $reportData['chartDataProperties'],
            'chartDataKomisi' => $reportData['chartDataKomisi']
        ]);
    }
}