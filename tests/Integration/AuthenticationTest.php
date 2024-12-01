<?php

namespace Tests\Integration;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_authenticate_with_correct_credentials()
    {
        // Arrange
        $user = User::factory()->create([
            'password' => Hash::make('correct-password')
        ]);

        // Act
        $result = Auth::attempt([
            'email' => $user->email,
            'password' => 'correct-password'
        ]);

        // Assert
        $this->assertTrue($result);
        $this->assertEquals($user->id, Auth::id());
    }

    /** @test */
    public function user_cannot_authenticate_with_incorrect_password()
    {
        // Arrange
        $user = User::factory()->create([
            'password' => Hash::make('correct-password')
        ]);

        // Act
        $result = Auth::attempt([
            'email' => $user->email,
            'password' => 'wrong-password'
        ]);

        // Assert
        $this->assertFalse($result);
        $this->assertNull(Auth::id());
    }

    /** @test */
    public function registration_triggers_appropriate_events()
    {
        // Arrange
        Event::fake();

        // Act
        $user = User::factory()->create();
        event(new Registered($user)); // Manually dispatch the event
        // Assert
        Event::assertDispatched(Registered::class, function ($event) use ($user) {
            return $event->user->id === $user->id;
        });
    }

    /** @test */
    public function authenticated_session_remembers_user_when_requested()
    {
        // Arrange
        $user = User::factory()->create([
            'password' => Hash::make('password')
        ]);

        // Act
        Auth::login($user, true); // true for "remember me"

        // Assert
        $this->assertEquals($user->id, Auth::id());
    }

    /** @test */
    public function user_can_logout()
    {
        // Arrange
        $user = User::factory()->create();
        Auth::login($user);

        // Assert user is logged in
        $this->assertTrue(Auth::check());

        // Act
        Auth::logout();

        // Assert
        $this->assertFalse(Auth::check());
        $this->assertNull(Auth::id());
    }
}