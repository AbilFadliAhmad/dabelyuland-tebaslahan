<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index() {
        $user = Auth::user();
        $transactions = [];

        if($user->role == 'admin') {
            $transactions = DB::table('transactions')->orderBy('created_at', 'desc')->get();
        } else {
            $transactions = DB::table('transactions')->where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
        }
        return view('partials.transaction.index', compact('transactions'));
    }
}
