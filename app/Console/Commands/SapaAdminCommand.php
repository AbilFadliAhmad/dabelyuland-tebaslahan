<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;


class SapaAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tebaslahan:sapa';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menampilkan semangat untuk admin';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Halo admin tebaslahan! tetap semangat ya untuk hari ini');
        //
    }
}
