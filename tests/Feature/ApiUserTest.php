<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Facades\Storage;

class ApiUserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function users_can_get_their_profile_via_api()
    {
        // Arrange
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        // Act
        $response = $this->getJson('/api/profile');

        // Assert
        $response->assertOk()
            ->assertJson([
                'name' => $user->name,
                'email' => $user->email,
            ]);
    }

    /** @test */
    public function unauthorized_users_cannot_access_api()
    {
        // Act
        $response = $this->getJson('/api/profile');

        // Assert
        $response->assertUnauthorized();
    }

    /** @test */
    public function users_can_update_profile_via_api()
    {
        // Arrange
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        // Act
        $response = $this->putJson('/api/profile', [
            'name' => 'Updated Name',
            'email' => 'updated@example.com'
        ]);

        // Assert
        $response->assertOk();
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com'
        ]);
    }

    /** @test */
    public function api_validates_profile_update_data()
    {
        // Arrange
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        // Act
        $response = $this->putJson('/api/profile', [
            'name' => '', // Invalid empty name
            'email' => 'not-an-email' // Invalid email format
        ]);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email']);
    }

    /** @test */
    public function users_can_upload_avatar_via_api()
    {
        // Arrange
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $file = UploadedFile::fake()->image('avatar.jpg');

        // Act
        $response = $this->postJson('/api/profile/avatar', [
            'avatar' => $file
        ]);

        // Assert
        $response->assertOk();
        $this->assertNotNull($user->fresh()->avatar_path);
        Storage::disk('public')->assertExists($user->fresh()->avatar_path);
    }
}