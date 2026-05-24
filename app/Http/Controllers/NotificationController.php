<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function checkNotifications() {
        $notification = DB::table('notifications')->select('id')->where('user_id', Auth::user()->id)->first();
        $exists = $notification ? true : false;
        return response()->json(['success' => true, 'exists' => $exists ]);
    }

    public function listNotifications() {
        $notifications = DB::table('notifications')->where('user_id', Auth::user()->id)->orderBy('id', 'desc')->get();
        return response()->json(['success' => true, 'notifications' => $notifications ]);
    }

    public function destroyNotification(Request $request) {
        try {
            $id = $request->id;
            // Cek apakah notifikasi ada dan milik user yang sedang login
            $notification = DB::table('notifications')
                ->where('id', $id)
                ->where('user_id', Auth::id())
                ->first();

            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notifikasi tidak ditemukan atau Anda tidak memiliki akses.'
                ], 404);
            }

            // Hapus Notifikasi
            DB::table('notifications')->where('id', $id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Notifikasi berhasil dihapus.'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server: ' . $e->getMessage()
            ], 500);
        }
    }
}
