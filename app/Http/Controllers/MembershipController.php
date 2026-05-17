<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Membership;
use App\Models\User;

class MembershipController extends Controller
{
   public function index()
    {
        $memberships = [
            (object) [
                'id' => 1,
                'name' => 'Bronze',
                'price' => 0,
                'duration_days' => 30,
                'max_properties' => 10,
                'recommendation_quota' => 1,
                'push_quota' => 3,
                'highlight_quota' => 0,
                'has_verified_badge' => false,
                'badge_name' => 'Dasar',
                'description' => 'Cocok untuk mencoba layanan kami.'
            ],
            (object) [
                'id' => 2,
                'name' => 'Silver Pro',
                'price' => 49000,
                'duration_days' => 30,
                'max_properties' => 50,
                'recommendation_quota' => 10,
                'push_quota' => 30,
                'highlight_quota' => 2,
                'has_verified_badge' => true,
                'badge_name' => 'Paling Laris',
                'description' => 'Untuk agen profesional dengan banyak listing.'
            ],
            (object) [
                'id' => 3,
                'name' => 'Gold Premium',
                'price' => 149000,
                'duration_days' => 30,
                'max_properties' => 9999,
                'recommendation_quota' => 50,
                'push_quota' => 150,
                'highlight_quota' => 10,
                'has_verified_badge' => true,
                'badge_name' => 'Eksklusif',
                'description' => 'Dominasi pasar dengan visibilitas maksimal.'
            ]
        ];

        return view('admin.membership.index', compact('memberships')); 
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'price' => 'required|numeric|min:0',
            'max_properties' => 'required|numeric|min:1',
            'highlight_quota' => 'required|numeric|min:0',
        ]);

        $package = Membership::findOrFail($id);
        $package->update([
            'price' => $request->price,
            'max_properties' => $request->max_properties,
            'highlight_quota' => $request->highlight_quota,
        ]);

        // Mengembalikan response JSON untuk ditangkap oleh Javascript
        return response()->json([
            'success' => true,
            'message' => 'Paket berhasil diperbarui!'
        ]);
    }

    public function userList()
    {
        // Mengambil hanya user dengan role 'user' (bukan admin)
        $users = User::where('role', 'user')
                    ->orderBy('created_at', 'desc')
                    ->get();

        // Hitung statistik berdasarkan data asli
        $totalUsers = $users->count();
        $totalPremium = $users->whereIn('membership_status', ['silver', 'gold'])->count();

        return view('admin.membership.list', compact('users', 'totalUsers', 'totalPremium'));
    }

    
}