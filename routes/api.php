<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MidtransController;
use App\Http\Controllers\Admin\SesiController;
use App\Models\Property; 


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Rute untuk menerima notifikasi otomatis dari Midtrans (Webhook)
Route::post('/midtrans/webhook', [MidtransController::class, 'handleWebhook'])->name('membership.webhook');

// Rute untuk mengirim OTP dengan bantuan Qstash dari Upstash
Route::post('/internal/process-otp', [SesiController::class, 'processOtp'])->name('process-otp');

// Testing Routes
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/cities', function (Request $request) {
    $search = strtolower($request->query('q'));

    if (!$search) return response()->json([]);

    $cities = Property::select('kota')
        ->where('kota', 'LIKE', $search . '%')
        ->distinct()
        ->limit(10)
        ->pluck('kota') // Mengambil array string langsung ['Jakarta', 'Jombang']
        ->map(fn($kota) => ['name' => $kota]);

    return response()->json($cities);
})->name('search-cities');

Route::post('/test-send', [App\Http\Controllers\FCMController::class, 'testSend'])->name('send-send');


// ================= TESTING ROUTES (Aman dihapus) =================
// Route::get('/benchmark', function () {
//     return view('benchmark.index');
// });
// Route::get('/api/benchmark/{stage}', function ($stage) {
//     $timeOld = 0; $timeNew = 0; $rowsOld = 0; $rowsNew = 0;

//     if ($stage == 1) {
//         // TAHAP 1: Pengambilan Dasar (100 Data Properti Rumah Terbaru)
//         $start = microtime(true);
//         $oldData = DB::table('properties_benchmark')->where('tipe', 'rumah')->limit(100)->get();
//         $timeOld = round((microtime(true) - $start) * 1000, 2);
//         $rowsOld = count($oldData);

//         $start = microtime(true);
//         $newData = DB::table('buildings')->where('tipe_bangunan', 'rumah')->limit(100)->get();
//         $timeNew = round((microtime(true) - $start) * 1000, 2);
//         $rowsNew = count($newData);
//     } 
//     elseif ($stage == 2) {
//         // TAHAP 2: Filtering Sedang (Harga & Luas Tanah)
//         $start = microtime(true);
//         $oldData = DB::table('properties_benchmark')
//             ->where('tipe', 'rumah')
//             ->where('harga', '>=', 500000000)
//             ->where('luas_tanah', '>=', 100)
//             ->get();
//         $timeOld = round((microtime(true) - $start) * 1000, 2);
//         $rowsOld = count($oldData);

//         $start = microtime(true);
//         $newData = DB::table('buildings')
//             ->where('tipe_bangunan', 'rumah')
//             ->where('harga', '>=', 500000000)
//             ->where('luas_tanah', '>=', 100)
//             ->get();
//         $timeNew = round((microtime(true) - $start) * 1000, 2);
//         $rowsNew = count($newData);
//     } 
//     elseif ($stage == 3) {
//         // TAHAP 3: Filtering Berat (Teks Pencarian, Filter Kompleks & Pengurutan)
//         $start = microtime(true);
//         $oldData = DB::table('properties_benchmark')
//             ->where('tipe', 'rumah')
//             ->where('deskripsi', 'like', '%strategis%')
//             ->where('jumlah_kamar_tidur', '>=', 3)
//             ->orderBy('harga', 'desc')
//             ->limit(500)
//             ->get();
//         $timeOld = round((microtime(true) - $start) * 1000, 2);
//         $rowsOld = count($oldData);

//         $start = microtime(true);
//         $newData = DB::table('buildings')
//             ->where('tipe_bangunan', 'rumah')
//             ->where('deskripsi', 'like', '%strategis%')
//             ->where('jumlah_kamar_tidur', '>=', 3)
//             ->orderBy('harga', 'desc')
//             ->limit(500)
//             ->get();
//         $timeNew = round((microtime(true) - $start) * 1000, 2);
//         $rowsNew = count($newData);
//     }

//     return response()->json([
//         'old' => ['time' => $timeOld, 'rows' => $rowsOld],
//         'new' => ['time' => $timeNew, 'rows' => $rowsNew],
//     ]);
// });