<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;

class CommandTest extends TestCase
{
    /** @test */
    public function custom_command_executes_successfully()
    {
        // Act
        $exitCode = Artisan::call('app:cleanup-old-users');

        // Assert
        $this->assertEquals(0, $exitCode);
    }

    /** @test */
    public function scheduled_command_runs_at_correct_time()
    {
        // Arrange
        $schedule = app()->make(\Illuminate\Console\Scheduling\Schedule::class);

        // Act
        $events = collect($schedule->events())->filter(function ($event) {
            return str_contains($event->command, 'app:daily-cleanup');
        });

        // Assert
        $this->assertEquals(1, $events->count());
        $this->assertEquals('0 0 * * *', $events->first()->expression); // Runs at midnight
    }
}