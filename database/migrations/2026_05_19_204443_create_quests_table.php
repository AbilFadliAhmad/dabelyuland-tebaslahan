<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('quests', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); 
            $table->string('title'); 
            $table->enum('type', ['daily', 'progressive']); // Pembeda reset harian atau berkelanjutan
            $table->integer('base_target_amount'); // Target awal (1, 5, 10)
            $table->integer('base_reward_coins'); // Koin dasar yang didapat
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

         $quests = [
            [
                'code'               => 'daily_login',
                'title'              => 'Login Harian',
                'type'               => 'daily',
                'base_target_amount' => 1, // Cukup 1 kali login
                'base_reward_coins'  => 1,
                'created_at'         => Carbon::now(),
                'updated_at'         => Carbon::now(),
            ],
            [
                'code'               => 'share_sosmed',
                'title'              => 'Bagikan 5 Properti ke Sosmed',
                'type'               => 'daily',
                'base_target_amount' => 5, 
                'base_reward_coins'  => 2,
                'created_at'         => Carbon::now(),
                'updated_at'         => Carbon::now(),
            ],
            [
                'code'               => 'visitor_referral',
                'title'              => 'Dapatkan 5 Pengunjung Unik',
                'type'               => 'daily',
                'base_target_amount' => 5, 
                'base_reward_coins'  => 3,
                'created_at'         => Carbon::now(),
                'updated_at'         => Carbon::now(),
            ],
            [
                'code'               => 'upload_property',
                'title'              => 'Agen Produktif (Upload Properti)',
                'type'               => 'progressive',
                'base_target_amount' => 10, // Dimulai dari 10
                'base_reward_coins'  => 4, // Reward besar karena berkelanjutan
                'created_at'         => Carbon::now(),
                'updated_at'         => Carbon::now(),
            ]
        ];

        DB::table('quests')->insert($quests);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quests');
    }
};
