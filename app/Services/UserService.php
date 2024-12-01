<?php

namespace App\Services;

use App\Events\UserRegistered;
use App\Models\User;
use App\Notifications\WelcomeEmail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use App\Models\SubscriptionHistory;
class UserService
{
    /**
     * Register a new user and trigger the necessary events.
     *
     * @param array $data
     * @return User
     */
    public function register(array $data): User
    {
        // Hash the password and create the user
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);

        // Dispatch the UserRegistered event
        event(new UserRegistered($user));

        // Send a welcome email
        Notification::send($user, new WelcomeEmail());

        return $user;
    }

    /**
     * Update the user's profile, including avatar upload.
     *
     * @param User $user
     * @param array $data
     * @return User
     */
    public function updateProfile(User $user, array $data): User
    {
        // Handle avatar upload
        if (isset($data['avatar'])) {
            $avatarPath = $data['avatar']->store('avatars', 'public');
            $user->avatar_path = $avatarPath;
        }

        // Update other profile data
        $user->fill($data);
        $user->save();

        return $user;
    }

    /**
     * Sync user roles and permissions.
     *
     * @param User $user
     * @param array $roles
     * @param array $permissions
     */
    public function syncRolesAndPermissions(User $user, array $roles, array $permissions): void
    {
        // Sync roles
        $user->syncRoles($roles);

        // Sync permissions
        $user->syncPermissions($permissions);
    }

    /**
     * Update the user's subscription type and log changes.
     *
     * @param User $user
     * @param string $subscriptionType
     */
    public function updateSubscription(User $user, string $subscriptionType): void
    {
        // Update the user's subscription
        $user->subscription_type = $subscriptionType;
        $user->save();

        // Log the subscription history
        SubscriptionHistory::factory()->create([            
            'type' => $subscriptionType,
            'status' => 'active',
            'user_id' => $user->id
        ]);
    }

    /**
     * Delete a user and clean up related data.
     *
     * @param User $user
     */
    public function deleteUser(User $user): void
    {
        // Delete related posts and comments
        $user->posts()->delete();

        // Delete the user
        $user->delete();
    }
}
