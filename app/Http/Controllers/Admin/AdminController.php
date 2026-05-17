<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class AdminController extends Controller
{
    public function verifyUsers()
    {
        $users = User::where('role', 'user')->get();
        return view('admin.verify.list', compact('users'));
    }

    public function verifyUser($id)
    {
        $user = User::findOrFail($id);

        // Toggle is_verified
        $user->update([
            'is_verified' => !$user->is_verified,
        ]);

        $message = $user->is_verified ? 'Pengguna berhasil diverifikasi.' : 'Verifikasi pengguna berhasil dibatalkan.';

        return back()->with('success', $message);
    }

    public function dashboard()
    {
        $pendingUsers = User::where('role', 'user')->where('is_verified', false)->get();
        return view('admin.dashboard', compact('pendingUsers'));
    }
}
