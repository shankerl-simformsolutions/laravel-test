<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    /**
     * Fetch the authenticated user's profile.
     */
    public function show(Request $request)
    {
        return response()->json([
            'name' => $request->user()->name,
            'email' => $request->user()->email,
        ]);
    }

    /**
     * Update the authenticated user's profile.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $request->user()->id,
        ]);

        $request->user()->update($validated);

        return response()->json([
            'message' => 'Profile updated successfully.',
            'user' => $request->user(),
        ]);
    }

    /**
     * Upload a new avatar for the authenticated user.
     */
    public function uploadAvatar(Request $request)
    {
        $validated = $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Delete the old avatar if it exists
        if ($request->user()->avatar_path) {
            Storage::disk('public')->delete($request->user()->avatar_path);
        }

        // Store the new avatar
        $path = $request->file('avatar')->store('avatars', 'public');
        $request->user()->update(['avatar_path' => $path]);

        return response()->json([
            'message' => 'Avatar uploaded successfully.',
            'avatar_path' => $path,
        ]);
    }
}

