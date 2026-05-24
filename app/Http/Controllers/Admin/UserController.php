<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Membership;
use App\Models\UserWallet;
use App\Models\Property;
use App\Models\Banner;
use App\Models\PropertyHighlight;
use App\Models\PropertyRecommendation;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class UserController extends Controller
{
public function indexDummy()
    {
        // 1. Data Wallet Dummy
        $wallet = (object) [
            'dabelyu_koin'         => 1250000,
            'recommendation_quota' => 8,
            'highlight_quota'      => 3,
            'banner_quota'         => 1,
            'push_quota'           => 15,
        ];

        // 2. Data Membership Dummy
        // Anda bisa mengubah 'name' menjadi 'Bronze', 'Silver', atau 'Basic' untuk melihat perubahan warna dinamis di UI
        $membership = (object) [
            'name'                 => 'Gold Premium', 
            'recommendation_quota' => 10,
            'highlight_quota'      => 5,
            'banner_quota'         => 2,
        ];

        // 3. Data Traffic Sources Dummy (Persentase idealnya berjumlah 100)
        $trafficSources = [
            'wa'      => 45,
            'fb'      => 25,
            'ig'      => 15,
            'twitter' => 10,
            'other'   => 5,
        ];

        // 4. Data Chart Dummy (7 Hari Terakhir)
        $chartLabels = ['Jumat', 'Sabtu', 'Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis'];
        $chartViews  = [120, 180, 250, 140, 160, 210, 300];
        $chartWa     = [15, 22, 35, 18, 20, 28, 45];

        // 5. Data Top Properties Dummy
        $topProperties = collect([
            (object) [
                'judul'       => 'Rumah Minimalis Modern Jakarta Selatan',
                'views_count' => 1450,
                'mainImage'   => null // Sengaja di-set null agar memicu gambar placeholder dari via.placeholder.com di Blade
            ],
            (object) [
                'judul'       => 'Apartemen 2BR Sudirman Pusat Kota',
                'views_count' => 1230,
                'mainImage'   => null
            ],
            (object) [
                'judul'       => 'Ruko 3 Lantai Strategis Surabaya',
                'views_count' => 890,
                'mainImage'   => null
            ]
        ]);

        // 6. Data Worst Properties Dummy
        $worstProperties = collect([
            (object) [
                'judul'       => 'Tanah Kavling Ujung Berung',
                'views_count' => 12,
                'mainImage'   => null
            ],
            (object) [
                'judul'       => 'Rumah Tua Butuh Renovasi Depok',
                'views_count' => 5,
                'mainImage'   => null
            ],
            (object) [
                'judul'       => 'Gudang Terbengkalai Bekasi Timur',
                'views_count' => 2,
                'mainImage'   => null
            ]
        ]);

        // Kembalikan ke view persis seperti aslinya
        return view('user.dashboard.index', compact(
            'wallet', 
            'membership', 
            'topProperties', 
            'worstProperties',
            'trafficSources', 
            'chartLabels', 
            'chartViews', 
            'chartWa'
        ));
    }

    public function index()
    {
        $today = Carbon::today()->toDateString();
        $userId = Auth::user()->id;

        $last7Days = collect();
        for ($i = 6; $i >= 0; $i--) {
            $last7Days->push(Carbon::today()->subDays($i)->toDateString());
        }
        $sevenDaysAgo = $last7Days->first();

        // Mengambil dompet user beserta batas maksimal dari paket membership miliknya
        $wallet = UserWallet::where('user_id', $userId)->first();
        $membership = Membership::find($wallet->membership_id);

        $propAnalytics7Days = DB::table('property_analytics')
            ->whereBetween('date', [$sevenDaysAgo, $today])
            ->whereIn('property_id', Property::where('user_id', $userId)->pluck('id')->toArray())
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
        

        $propToday = $propAnalytics7Days->get($today);
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
        // EKSTRAKSI DATA CHART (7 HARI TERAKHIR)
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
            ->where('properties.status', 'aktif')
            ->where('properties.user_id', '==', $userId)
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
            ->where('properties.user_id', '==', $userId)
            // Gunakan COALESCE untuk mengubah nilai NULL (jika tidak ada data analitik) menjadi 0
            ->orderByRaw('COALESCE(property_analytics.views_count, 0) ASC')
            ->select('properties.*', DB::raw('COALESCE(property_analytics.views_count, 0) as views_count'))
            ->take(10)
            ->get();

        return view('user.dashboard.index', compact('wallet', 'membership', 'topProperties', 'worstProperties','trafficSources', 'chartLabels', 'chartViews', 'chartWa'));
    }

    // List Users
    public function list(Request $request)
    {
        $query = User::with(['wallet.membership'])->where('role', 'user');

        // Filter Nama/Email
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        // Filter Membership
        if ($request->filled('membership') && $request->membership !== 'all') {
            $query->whereHas('wallet.membership', function($q) use ($request) {
                $q->where('id', $request->membership);
            });
        }

        $users = $query->latest()->paginate(10);

        if ($request->ajax()) {
            $html = '';
            foreach ($users as $index => $val) {
                // Kita kirim $index untuk membantu penomoran di frontend jika perlu
                $html .= view('admin.user.row', compact('val'))->render();
            }
            return response()->json([
                'html' => $html,
                'hasMore' => $users->hasMorePages()
            ]);
        }

        $memberships = Membership::all();
        return view('admin.user.list', compact('users', 'memberships'));
    }

    // Update Wallet User
    public function updateWallet(Request $request)
    {
        $request->validate([
            'koin' => 'required|integer|min:0',
            'membership' => 'required|exists:memberships,id',
            'id' => 'required|exists:users,id',
        ]);

        $id = $request->id;

        try {
            DB::beginTransaction();

            // Ambil wallet beserta data membership saat ini
            $wallet = UserWallet::with('membership')->where('user_id', $id)->firstOrFail();
            $currentMembership = $wallet->membership ?? Membership::where('name', 'Bronze')->first();
            $newMembership = Membership::findOrFail($request->membership);

            // 1. Jika ID Membership sama, hanya fokus ke update koin
            if ($currentMembership->id === $newMembership->id) {
                $wallet->update([
                    'dabelyu_koin' => $request->koin,
                    'updated_at' => now(),
                ]);
                
                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'Jumlah koin berhasil diperbarui!',
                    'data' => $wallet
                ]);
            }

            // 2. Ambil semua data aktif untuk filtering di sisi aplikasi
            $activeRecs = PropertyRecommendation::where('user_id', $id)->get();
            $activeHighlights = PropertyHighlight::where('user_id', $id)->get();
            $activeBanners = Banner::where('user_id', $id)->get();

            // Hitung penggunaan token (quota) saat ini
            $usedRecQuota = $activeRecs->where('amount_token', '>', 0)->count();
            $usedHighQuota = $activeHighlights->where('amount_token', '>', 0)->count();
            $usedBanQuota = $activeBanners->where('amount_token', '>', 0)->where('is_active', 1)->count();

            // 3. Tentukan apakah ini Upgrade atau Downgrade berdasarkan Price
            $isUpgrade = $newMembership->price > $currentMembership->price;

            // Logika Sinkronisasi Kuota
            $types = [
                'recommendation' => ['used' => $usedRecQuota, 'items' => $activeRecs],
                'highlight'      => ['used' => $usedHighQuota, 'items' => $activeHighlights],
                'banner'         => ['used' => $usedBanQuota, 'items' => $activeBanners],
            ];

            $finalQuotas = [];

            foreach ($types as $key => $data) {
                $newLimit = $newMembership->{$key . '_quota'};
                
                if (!$isUpgrade && $data['used'] > $newLimit) {
                    // Kasus Turun Rank: Hapus Random jika penggunaan melebihi jatah baru
                    $data['items']->where('amount_token', '>', 0)
                        ->shuffle()
                        ->take($data['used'] - $newLimit)
                        ->each->delete();
                    
                    $finalQuotas[$key . '_quota'] = 0;
                } else {
                    // Kasus Naik Rank: Sisa = Jatah Baru - Yang sedang berjalan
                    $remaining = $newLimit - $data['used'];
                    $finalQuotas[$key . '_quota'] = ($remaining < 0) ? 0 : $remaining;
                }
            }

            // 4. Update Final ke Wallet
            $wallet->update([
                'dabelyu_koin'         => $request->koin,
                'membership_id'        => $newMembership->id,
                'recommendation_quota' => $finalQuotas['recommendation_quota'],
                'highlight_quota'      => $finalQuotas['highlight_quota'],
                'banner_quota'         => $finalQuotas['banner_quota'],
                'push_quota'           => $newMembership->push_quota, // Reset push quota sesuai rank baru
                'updated_at'           => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $isUpgrade ? 'Upgrade membership berhasil!' : 'Downgrade membership berhasil, kuota disesuaikan.',
                'data'    => $wallet
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui wallet: ' . $e->getMessage()
            ], 500);
        }
    }

    // Halaman Edit Admin
    public function editAdmin($id)
    {
        $user = User::where('id', $id)->where('role', 'admin')->firstOrFail();
        $isEdit = true;
        return view('admin.user.form', compact('user'), compact('isEdit'));
    }

    // Proses Update Admin
    public function updateAdmin(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|unique:users,name,' . $id,
            'email' => 'required|email|unique:users,email,' . $id,
            'nowa' => 'required|unique:users,nowa,' . $id,
            'password' => 'nullable|confirmed',
        ], [
            'name.required' => 'Nama wajib diisi',
            'name.unique' => 'Nama sudah terdaftar',
            'nowa.required' => 'Nomor wajib diisi',
            'nowa.unique' => 'Nomor sudah terdaftar',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'password.confirmed' => 'Password tidak cocok dengan konfirmasi',
        ]);

        $user = User::findOrFail($id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->nowa = $request->nowa;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('user')->with('success', 'Data admin berhasil diperbarui.');
    }

    public function deleteUser($id)
    {
        $user = User::where('id', $id)->where('role', 'user')->firstOrFail();
        $user->delete();

        return redirect()->route('admin.verify.users')->with('success', 'Akun pengguna berhasil dihapus.');
    }

    public function createAdmin()
    {
        $isEdit = false;
        return view('admin.user.form', compact('isEdit')); // View khusus untuk register admin
    }

    public function storeAdmin(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:users,name',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed',
            'nowa' => 'required|string',
        ], [
            'name.required' => 'Nama wajib diisi',
            'name.unique' => 'Username sudah terdaftar',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'password.required' => 'Password wajib diisi',
            'password.confirmed' => 'Password tidak cocok dengan konfirmasi',
            'nowa.required' => 'Nomor WhatsApp wajib diisi',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'admin', // Role admin
            'nowa' => $request->nowa,
            'is_verified' => true, // Langsung diverifikasi karena admin dibuat oleh admin
        ]);

        return redirect()->route('user')->with('success', 'Akun admin berhasil dibuat.');
    }

    public function deleteAdmin($id)
    {
        $user = User::where('id', $id)->where('role', 'admin')->firstOrFail();
        $user->delete();

        return redirect()->route('user')->with('success', 'Akun admin berhasil dihapus.');
    }
}
