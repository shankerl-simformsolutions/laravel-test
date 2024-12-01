<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DailyCleanup extends Command
{
    protected $signature = 'app:daily-cleanup';
    protected $description = 'Perform daily cleanup tasks';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Perform your cleanup logic here
        $this->info('Daily cleanup completed.');
    }
}
