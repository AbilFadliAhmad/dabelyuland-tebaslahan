<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;

class UserQuestController extends Controller
{
    public function index()
    {
        $userId = Auth::user()->id;
        $today = Carbon::today()->toDateString();
        $progressiveDate = '2000-01-01'; // Hardcode tanggal unik untuk misi berkelanjutan 

        // 1. check jumlah quest
        $questCount = DB::table('quests')
            ->where('is_active', 1)
            ->count();

        // 2. check jumlah misi hari ini
        $userQuestCount = DB::table('user_quests')
            ->where('user_id', $userId)
            ->where('date', $today)
            ->count();

        // 2.2 Jika total misi hari ini tidak sama dengan total misi, maka init quest baru
        if ($userQuestCount != $questCount) {
            $this->initQuest();
        }

        // 3. Ambil data gabungan untuk ditampilkan di Blade
        $quests = DB::table('user_quests')
            ->join('quests', 'user_quests.quest_id', '=', 'quests.id')
            ->where('user_quests.user_id', $userId)
            ->whereIn('user_quests.date', [$today, $progressiveDate])
            ->where('quests.is_active', 1)
            // Memilih data master dan memberikan alias untuk ID user_quests
            ->select('quests.*', 'user_quests.id as user_quest_id', 'user_quests.current_progress', 'user_quests.current_target', 'user_quests.is_completed')
            ->get();

        return view('user.quest.index', compact('quests'));
    }

    public function claim(int $id)
    {
        $userId = Auth::user()->id;

        // Cek data user_quest beserta tipe misinya
        $userQuest = DB::table('user_quests')
            ->join('quests', 'user_quests.quest_id', '=', 'quests.id')
            ->where('user_quests.id', $id)
            ->where('user_quests.user_id', $userId)
            ->select('user_quests.*', 'quests.type', 'quests.base_target_amount', 'quests.base_reward_coins')
            ->first();

        if (!$userQuest) {
            return back()->with('error', 'Misi tidak valid.');
        }

        // Validasi apakah benar-benar sudah memenuhi target
        if ($userQuest->current_progress < $userQuest->current_target) {
            return back()->with('error', 'Target misi belum tercapai!');
        }

        // Validasi jangan sampai diklaim dua kali
        if ($userQuest->is_completed) {
            return back()->with('error', 'Hadiah untuk misi ini sudah diklaim.');
        }

        // --- TAMBAHKAN KOIN KE WALLET USER ---
        $wallet = DB::table('user_wallets')->where('user_id', $userId)->first();
        if ($wallet) {
            DB::table('user_wallets')->where('user_id', $userId)->increment('dabelyu_koin', $userQuest->base_reward_coins);
        } else {
            DB::table('user_wallets')->insert([
                'user_id'      => $userId,
                'dabelyu_koin' => $userQuest->base_reward_coins,
                'created_at'   => Carbon::now(),
                'updated_at'   => Carbon::now(),
            ]);
        }

        // --- UPDATE STATUS MISI ---
        if ($userQuest->type === 'daily') {
            DB::table('user_quests')->where('id', $id)->update([
                'is_completed' => 1,
                'updated_at'   => Carbon::now()
            ]);
        } else {
            // Jika progresif (Upload Properti), naikkan targetnya, is_completed tetap 0
            DB::table('user_quests')->where('id', $id)->update([
                'current_target' => $userQuest->current_target + $userQuest->base_target_amount,
                'is_completed'   => 0, // Reset agar bisa dikerjakan lagi
                'updated_at'     => Carbon::now()
            ]);
        }

        return back()->with('success', 'Selamat! Anda berhasil mengklaim ' . $userQuest->base_reward_coins . ' Koin.');
    }

    // Fungsi khusus untuk dipanggil oleh Javascript via background (Beacon)
    public function initQuest()
    {
        if (!Auth::check()) {
            return response()->json(['status' => 'unauthenticated']);
        }

        $user = Auth::user();
        $today = Carbon::today()->toDateString();
        $progressiveDate = '2000-01-01'; // Hardcode tanggal unik untuk misi berkelanjutan

        $activeQuests = DB::table('quests')->where('is_active', 1)->get();

        foreach ($activeQuests as $quest) {
            $questDate = $quest->type === 'daily' ? $today : $progressiveDate;

            // Cek apakah quest ini sudah ada di database untuk user ini
            $exists = DB::table('user_quests')
                ->where('user_id', $user->id)
                ->where('quest_id', $quest->id)
                ->where('date', $questDate)
                ->exists();

            if (!$exists) {
                // Jika misinya adalah Login Harian, langsung set progress = 1 agar langsung siap klaim
                $initialProgress = ($quest->code === 'daily_login') ? 1 : ($quest->code === 'upload_property' ? $user->total_properties : 0);

                DB::table('user_quests')->insert([
                    'user_id'          => $user->id,
                    'quest_id'         => $quest->id,
                    'date'             => $questDate,
                    'current_progress' => $initialProgress,
                    'current_target'   => $quest->base_target_amount,
                    'is_completed'     => 0,
                    'created_at'       => Carbon::now(),
                    'updated_at'       => Carbon::now(),
                ]);
            }
        }

        return response()->json(['status' => 'success']);
    }

    public function shareProperty()
    {
        if (!Auth::check()) return response()->json(['status' => 'unauthenticated']);
        $userId = Auth::user()->id;
        
        // Panggil fungsi penambah progres
        $this->incrementDailyQuestProgress($userId, 'share_sosmed');

        return response()->json([
            'status'  => 'success',
            'message' => 'Progres misi share berhasil diperbarui.'
        ]);
    }

    /**
     * Endpoint saat ada Pengunjung luar membuka tautan referal.
     * Route ini TIDAK BOLEH menggunakan middleware 'auth' karena yang akses adalah tamu.
     */
    public function uniqueViewProperty(Request $request)
    {
        // Ambil ID Agen pembuat referal dari request (misal URL: /search/?ref=5)
        // Pastikan di frontend kamu mengirim parameter 'ref_user_id'
        $userId = (int) $request->ref_user_id; 

        // Hanya jalankan jika ada ID referalnya
        if ($userId) {
            $this->incrementDailyQuestProgress($userId, 'visitor_referral');
        }

        return response()->json([
            'status' => 'success'
        ]);
    }

    private function incrementDailyQuestProgress(int $userId, string $questCode)
    {
        $today = Carbon::today()->toDateString();

        // 1. Cari data master quest berdasarkan kodenya
        $quest = DB::table('quests')
            ->where('code', $questCode)
            ->where('is_active', 1)
            ->first();

        // Jika misi tidak ada atau dinonaktifkan, hentikan proses
        if (!$quest) return;

        // 2. Cari progres misi user tersebut hari ini
        $userQuest = DB::table('user_quests')
            ->where('user_id', $userId)
            ->where('quest_id', $quest->id)
            ->where('date', $today)
            ->first();

        if ($userQuest) {
            // 3a. Jika data sudah ada, tambah progres JIKA belum mencapai target
            if ($userQuest->current_progress < $userQuest->current_target) {
                DB::table('user_quests')
                    ->where('id', $userQuest->id)
                    ->increment('current_progress');
            }
        } else {
            // 3b. Fallback: Jika background init gagal berjalan dan data kosong, buat baru dengan progress = 1
            DB::table('user_quests')->insert([
                'user_id'          => $userId,
                'quest_id'         => $quest->id,
                'date'             => $today,
                'current_progress' => 1,
                'current_target'   => $quest->base_target_amount,
                'is_completed'     => 0,
                'created_at'       => Carbon::now(),
                'updated_at'       => Carbon::now(),
            ]);
        }
    }
}