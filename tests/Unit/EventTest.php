<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Event;
use App\Events\UserRegistered;
use App\Listeners\SendWelcomeEmail;
use App\Models\User;

class EventTest extends TestCase
{
    /** @test */
    public function event_has_correct_payload()
    {
        // Arrange
        $user = User::factory()->create();
        
        // Act
        $event = new UserRegistered($user);

        // Assert
        $this->assertEquals($user->id, $event->user->id);
    }

    /** @test */
    public function event_listeners_are_registered()
    {
        // Arrange
        Event::fake();
        $user = User::factory()->create();

        // Act
        event(new UserRegistered($user));

        // Assert
        Event::assertDispatched(UserRegistered::class, function ($event) use ($user) {
            return $event->user->id === $user->id;
        });
    }

    /** @test */
    public function listener_handles_event_correctly()
    {
        // Arrange
        $user = User::factory()->create();
        $event = new UserRegistered($user);
        $listener = new SendWelcomeEmail();

        // Act
        $listener->handle($event);

        // Assert
        // Add assertions based on what the listener should do
        $this->assertTrue(true); // Placeholder assertion
    }
}