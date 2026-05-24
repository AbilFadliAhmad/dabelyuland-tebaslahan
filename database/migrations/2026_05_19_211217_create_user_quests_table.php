<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_quests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('quest_id')->constrained()->cascadeOnDelete();
            
            // Jika type = 'daily', isi dengan tanggal hari ini. Jika 'progressive', biarkan NULL.
            $table->date('date')->nullable(); 
            
            $table->integer('current_progress')->default(0); 
            
            // Target yang bisa membesar (10 -> 20 -> 30) khusus untuk quest progressive
            $table->integer('current_target'); 
            
            $table->boolean('is_completed')->default(false); 
            
            $table->timestamps();
            
            // Memastikan unik. (Jika date NULL untuk progressive, user tetap hanya punya 1 baris)
            $table->unique(['user_id', 'quest_id', 'date']); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_quests');
    }
};
