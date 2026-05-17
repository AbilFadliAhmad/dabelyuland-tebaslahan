<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('email')->unique();
            // $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('role', ['admin', 'user'])->default('user'); // Role pengguna
            $table->string('nowa');     // Nomor WhatsApp
            $table->boolean('is_verified')->default(false); // Verifikasi admin
            $table->integer('total_properties')->default(0);
            $table->rememberToken();
            $table->timestamps();
        });

        // Buat data masternya terlebih dahulu
        DB::table('users')->insert([
            ['id' => 1, 'name' => 'user', 'email' => 'user@gmail.com', 'password' => bcrypt('123'), 'role' => 'user', 'nowa' => '08123456789', 'is_verified' => true],
            ['id' => 2, 'name' => 'admin', 'email' => 'admin@gmail.com', 'password' => bcrypt('123'), 'role' => 'admin', 'nowa' => '08123456789', 'is_verified' => true],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
