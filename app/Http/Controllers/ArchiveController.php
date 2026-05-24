<?php

namespace App\Http\Controllers;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ArchiveController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Property::with(['user', 'mainImage'])
            ->whereIn('status', ['terjual', 'dihapus', 'ditolak']);

        // Server-side Filtering
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // JIka rolenya bukan admin
        if ($user->role !== 'admin') {
            $query->where('user_id', $user->id);
        }

        // Pencarian properti berdasarkan nama agen khusus admin
        if ($user->role === 'admin' && $request->filled('agent')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->agent . '%');
            });
        }

        $trashedProperties = $query->latest()->paginate(10);
        if ($request->is_ajax || $request->ajax()) {
            $html = '';
            
            if ($trashedProperties->isEmpty()) {
                $html = '
                    <tr>
                        <td colspan="5" class="text-center py-12">
                            <div class="flex flex-col items-center justify-center text-gray-500">
                                <i class="bi bi-search mb-3 text-[3rem] text-slate-300"></i>
                                <h6 class="font-bold text-gray-700 mb-1">Riwayat Tidak Ditemukan</h6>
                                <p class="text-sm m-0">Tidak ada riwayat properti yang cocok dengan kriteria pencarian atau filter Anda.</p>
                            </div>
                        </td>
                    </tr>
                ';
            } else {
                foreach ($trashedProperties as $property) {
                    // Render setiap elemen baris menggunakan komponen tadi
                    $html .= view('admin.trash.row', compact('property'))->render();
                }
            }
            return response()->json([
                'html' => $html,
                'hasMore' => $trashedProperties->hasMorePages()
            ]);
        }

        return view('partials.archive.index', compact('trashedProperties'));
    }

    public function restore($id)
    {
        // 1. Cari properti atau gagalkan jika tidak ketemu
        $property = Property::findOrFail($id);
        $user = Auth::user();
        $oldStatus = $property->status; // Simpan status lama untuk keperluan log/notifikasi

        // 2. Logika Otorisasi & Validasi Status Berdasarkan Role
        if ($user->role === 'admin') {
            // Admin: Bebas memulihkan properti dari status apa pun
            $property->status = 'aktif';
        } else {
            // User Biasa: Pastikan hanya bisa memulihkan properti miliknya sendiri terlebih dahulu
            if ($property->user_id !== $user->id) {
                return redirect()->back()->with('error', 'Akses ditolak: Ini bukan properti milik Anda.');
            }

            // User Biasa: Hanya bisa jika status saat ini adalah 'dihapus' atau 'terjual'
            $allowedStatuses = ['dihapus', 'terjual'];
            if (!in_array($oldStatus, $allowedStatuses)) {
                return redirect()->back()->with('error', "Anda tidak dapat mengaktifkan kembali properti yang berstatus '{$oldStatus}'.");
            }

            $property->status = 'aktif';
        }

        // 3. Eksekusi Perubahan Database & Pencatatan Audit Log di dalam Transaksi
        DB::transaction(function () use ($property, $user, $oldStatus) {
            $property->save();

            // Catat ke tabel audit_logs
            DB::table('audit_logs')->insert([
                'user_id'     => $user->id,
                'type'        => 'properti',
                'action'      => 'update',
                'description' => "Pengguna {$user->name} ({$user->role}) memulihkan properti '{$property->judul}' (ID #{$property->id}) kembali aktif dari status sebelumnya: '{$oldStatus}'.",
                'created_at'  => now(),
                'updated_at'  => now()
            ]);
        });

        // Amankan Flash Message Session sebelum memutuskan koneksi
        session()->flash('success', 'Properti berhasil diaktifkan kembali.');
        session()->save();

        // =========================================================================
        // ASYNC TRICK VIA CLOUDFLARE TUNNEL
        // =========================================================================
        header("Location: " . url()->previous());
        header("Connection: close");
        header("Content-Length: 0");

        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }

        // =========================================================================
        // ASYNC BACKGROUND PROCESS (PROSES DIKIRIM SETELAH HALAMAN REFRESH)
        // =========================================================================
        try {
            set_time_limit(60);

            // Hanya kirim notifikasi FCM jika tindakan dilakukan oleh Admin (Bukan oleh user itu sendiri)
            if ($user->role === 'admin') {
                // Targetkan topik ke agen pemilik properti tersebut
                $fcmTopic = "user_" . $property->user_id; 
                $fcmTitle = 'Properti Diaktifkan Kembali';
                $fcmBody  = "Kabar baik! Properti '{$property->judul}' telah dipulihkan dan diaktifkan kembali oleh admin di platform.";
                
                // Redirect link ke halaman detail properti milik agen di frontside
                $fcmUrl   = $property->tipe === 'tanah' 
                    ? route('user.lands.index') // Sesuaikan dengan penamaan nama route index/detail properti agenmu
                    : route('user.buildings.index');

                FCMController::sendNotification($fcmTopic, $fcmTitle, $fcmBody, $fcmUrl);
            }
        } catch (\Exception $e) {
            Log::error('FCM Restore Background Error: ' . $e->getMessage());
        }

        exit;
    }
}
