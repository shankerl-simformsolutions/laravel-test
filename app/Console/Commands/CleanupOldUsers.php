<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Carbon\Carbon;

class CleanupOldUsers extends Command
{
    protected $signature = 'app:cleanup-old-users';
    protected $description = 'Cleanup users who have been inactive for more than a year';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $oldUsers = User::where('last_login', '<', Carbon::now()->subYear())->get();

        foreach ($oldUsers as $user) {
            $user->delete();
        }

        $this->info('Old users have been cleaned up.');
    }
}
