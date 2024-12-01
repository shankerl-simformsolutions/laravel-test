<?php

namespace Tests\Integration;

use App\Models\User;
use App\Services\UserService;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use App\Events\UserRegistered;
use App\Notifications\WelcomeEmail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Http\UploadedFile;
class UserServiceTest extends TestCase
{
    use RefreshDatabase;

    private UserService $userService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userService = app(UserService::class);
    }

    /** @test */
    public function it_registers_new_user_and_triggers_events()
    {
        // Arrange
        Event::fake();
        Notification::fake();

        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123'
        ];

        // Act
        $user = $this->userService->register($userData);

        // Assert
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($userData['email'], $user->email);

        // Verify events were dispatched
        Event::assertDispatched(UserRegistered::class);
        
        // Verify notifications were sent
        Notification::assertSentTo($user, WelcomeEmail::class);
    }

    /** @test */
    public function it_updates_user_profile_with_avatar()
    {
        // Arrange
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password123')
        ]);

        $updateData = [
            'name' => 'Jane Doe',
            'avatar' => UploadedFile::fake()->image('avatar.jpg')
        ];

        // Act
        $updatedUser = $this->userService->updateProfile($user, $updateData);

        // Assert
        $this->assertEquals('Jane Doe', $updatedUser->name);
        $this->assertNotNull($updatedUser->avatar_path);
        $this->assertFileExists(storage_path('app/public/' . $updatedUser->avatar_path));
    }

    /** @test */
    public function it_syncs_user_roles_and_permissions()
    {
        // Arrange
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password123')
        ]);

        // Create roles and permissions
        \Spatie\Permission\Models\Role::create(['name' => 'editor', 'guard_name' => 'web']);
        \Spatie\Permission\Models\Role::create(['name' => 'writer', 'guard_name' => 'web']);

        \Spatie\Permission\Models\Permission::create(['name' => 'create-post', 'guard_name' => 'web']);
        \Spatie\Permission\Models\Permission::create(['name' => 'edit-post', 'guard_name' => 'web']);

        $roles = ['editor', 'writer'];
        $permissions = ['create-post', 'edit-post'];

        // Act
        $this->userService->syncRolesAndPermissions($user, $roles, $permissions);

        // Assert
        $this->assertTrue($user->hasRole('editor'));
        $this->assertTrue($user->hasRole('writer'));
        $this->assertTrue($user->hasPermissionTo('create-post'));
        $this->assertTrue($user->hasPermissionTo('edit-post'));
    }

    /** @test */
    public function it_handles_user_subscription_changes()
    {
        // Arrange
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password123')
        ]);

        // Act
        $this->userService->updateSubscription($user, 'premium');

        // Assert
        $this->assertEquals('premium', $user->fresh()->subscription_type);
        $this->assertDatabaseHas('subscription_histories', [
            'user_id' => $user->id,
            'type' => 'premium',
            'status' => 'active'
        ]);
    }

    /** @test */
    public function it_processes_user_deletion_with_cleanup()
    {
        // Arrange
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password123')
        ]);

        // Create related records
        $user->posts()->create(['user_id' => $user->id, 'title' => 'Test Post', 'content' => 'Test Content']);

        // Act
        $this->userService->deleteUser($user);

        // Assert
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
        $this->assertDatabaseMissing('posts', ['user_id' => $user->id]);
    }
}