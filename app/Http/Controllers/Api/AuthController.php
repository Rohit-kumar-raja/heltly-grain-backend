<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email|unique:app_users,email',
                'password' => 'required|min:6',
                'fullName' => 'nullable|string',
                // Profile fields
                'age' => 'nullable|integer',
                'gender' => 'nullable|string',
                'height' => 'nullable|numeric',
                'weight' => 'nullable|numeric',
                'activity_level' => 'nullable|string',
                'goal' => 'nullable|string',
                'phone' => 'nullable|string',
                'profile_picture' => 'nullable|string',
            ]);

            $user = AppUser::create([
                'name' => $validated['fullName'] ?? explode('@', $validated['email'])[0],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'age' => $validated['age'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'height' => $validated['height'] ?? null,
                'weight' => $validated['weight'] ?? null,
                'activity_level' => $validated['activity_level'] ?? null,
                'goal' => $validated['goal'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'profile_picture' => $validated['profile_picture'] ?? null,
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'user' => $user,
                'token' => $token,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = AppUser::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['success' => false, 'message' => 'Invalid login details'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'user' => $user,
            'token' => $token
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['success' => true]);
    }

    public function user(Request $request)
    {
        return $request->user();
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        // Check if we have a file upload for profile_picture
        $rules = [
            'fullName' => 'nullable|string',
            'age' => 'nullable|integer',
            'gender' => 'nullable|string',
            'height' => 'nullable|numeric',
            'weight' => 'nullable|numeric',
            'activity_level' => 'nullable|string',
            'goal' => 'nullable|string',
            'phone' => 'nullable|string',
            // Allow image file or existing string URL
            'profile_picture' => 'nullable',
        ];

        // If it's a file, validate as image
        if ($request->hasFile('profile_picture')) {
            $rules['profile_picture'] = 'image|mimes:jpeg,png,jpg,gif|max:2048';
        }

        $validated = $request->validate($rules);

        if (isset($validated['fullName'])) {
            $user->name = $validated['fullName'];
        }

        // Handle File Upload
        if ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');
            $path = $file->store('profile-pictures', 'public');
            $url = asset('storage/' . $path);

            $user->profile_picture = $url;
        } elseif (isset($validated['profile_picture']) && is_string($validated['profile_picture'])) {
            $user->profile_picture = $validated['profile_picture'];
        }

        $user->fill([
            'age' => $validated['age'] ?? $user->age,
            'gender' => $validated['gender'] ?? $user->gender,
            'height' => $validated['height'] ?? $user->height,
            'weight' => $validated['weight'] ?? $user->weight,
            'activity_level' => $validated['activity_level'] ?? $user->activity_level,
            'goal' => $validated['goal'] ?? $user->goal,
            'phone' => $validated['phone'] ?? $user->phone,
            // profile_picture already handled
        ]);

        $user->save();

        return response()->json([
            'success' => true,
            'user' => $user,
            'message' => 'Profile updated successfully'
        ]);
    }

    public function changePassword(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        // Check if current password is correct
        if (!Hash::check($validated['current_password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect'
            ], 422);
        }

        // Check if new password is same as current
        if (Hash::check($validated['new_password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'New password must be different from current password'
            ], 422);
        }

        // Update password
        $user->password = Hash::make($validated['new_password']);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully'
        ]);
    }
}
