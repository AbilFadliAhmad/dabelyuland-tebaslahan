<?php

namespace App\Http\Controllers;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrashController extends Controller
{
    public function index(Request $request)
    {
        $query = Property::with(['user', 'mainImage'])
            ->whereIn('status', ['terjual', 'dihapus', 'ditolak']);

        // Server-side Filtering
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if (Auth::user()->role === 'admin' && $request->filled('agent')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->agent . '%');
            });
        }

        $trashedProperties = $query->latest()->paginate(10);

        if ($request->ajax()) {
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

        return view('admin.trash.index', compact('trashedProperties'));
    }

    public function restore(Request $request, $id)
    {
        // 1. Cari properti atau gagalkan jika tidak ketemu
        $property = Property::findOrFail($id);
        $user = auth()->user();

        // 2. Logika Otorisasi
        if ($user->role === 'admin') {
            // Admin bisa memulihkan semua (termasuk yang 'ditolak')
            $property->status = 'aktif';
        } else {
            // User biasa: Cek apakah status saat ini diizinkan untuk dipulihkan
            $allowedStatuses = ['dihapus', 'terjual'];
            
            if (in_array($property->status, $allowedStatuses)) {
                $property->status = 'aktif';
            } else {
                // Jika status tidak diizinkan (misal masih 'ditolak' oleh admin)
                return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk mengaktifkan kembali properti ini.');
            }

            // Pastikan user hanya bisa memulihkan properti miliknya sendiri
            if ($property->user_id !== $user->id) {
                return redirect()->back()->with('error', 'Akses ditolak.');
            }
        }

        // 3. Simpan perubahan
        $property->save();

        return redirect()->back()->with('success', 'Properti berhasil diaktifkan kembali.');
    }
}
