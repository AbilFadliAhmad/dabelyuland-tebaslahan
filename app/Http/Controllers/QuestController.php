<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class QuestController extends Controller
{
    public function index()
    {
        $quests = DB::table('quests')->get();
        return view('admin.quest.index', compact('quests'));
    }

    public function update(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'title'              => 'required|string|max:255',
            'base_target_amount' => 'required|integer|min:1',
            'base_reward_coins'  => 'required|integer|min:0',
        ]);

        // Proses Update ke database
        DB::table('quests')->where('id', $id)->update([
            'title'              => $request->title,
            'base_target_amount' => $request->base_target_amount,
            'base_reward_coins'  => $request->base_reward_coins,
            'is_active'          => $request->has('is_active') ? 1 : 0,
            'updated_at'         => Carbon::now(),
        ]);

        return back()->with('success', 'Perubahan misi berhasil disimpan!');
    }
}
