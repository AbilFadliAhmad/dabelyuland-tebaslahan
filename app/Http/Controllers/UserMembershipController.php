<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Membership;

class UserMembershipController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // 1. Ambil semua paket dari database (untuk pricing cards)
        $Memberships = Membership::orderBy('price', 'asc')->get();

        // 2. Ambil data paket milik user saat ini
        // Kita cari di tabel Memberships berdasarkan status membership user
        $currentMembership = Membership::where('name', $user->membership_status)->first();
        
        // Jika user tidak punya status (null), arahkan ke Bronze sebagai default
        if (!$currentMembership) {
            $currentMembership = Membership::where('name', 'Bronze')->first();
        }


        return view('user.membership.index', compact('user', 'Memberships', 'currentMembership',  ));
    }
}