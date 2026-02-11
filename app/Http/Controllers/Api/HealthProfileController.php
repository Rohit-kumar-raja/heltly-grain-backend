<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserHealthProfile;
use Illuminate\Http\Request;

class HealthProfileController extends Controller
{
    /**
     * Get the user's health profile.
     */
    public function show(Request $request)
    {
        $profile = UserHealthProfile::firstOrCreate(
            ['user_id' => $request->user()->id],
            [
                'vitals' => [],
                'body_composition' => [],
                'lifestyle' => [],
                'nutrition_stats' => [],
                'medical_history' => [],
                'lab_results' => [],
                'mental_health' => [],
            ]
        );

        return response()->json($profile);
    }

    /**
     * Update the user's health profile.
     */
    public function update(Request $request)
    {
        $profile = UserHealthProfile::firstOrCreate(['user_id' => $request->user()->id]);

        $validKeys = [
            'vitals',
            'body_composition',
            'lifestyle',
            'nutrition_stats',
            'medical_history',
            'lab_results',
            'mental_health'
        ];

        $data = $request->only($validKeys);

        // We can do a simple update or a deep merge if required.
        // For now, let's assume the frontend sends the complete object for a section if it wants to update it.
        // Or we can merge. Let's do a simple update for now to keep it clean.

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $profile->$key = $value;
            }
        }

        $profile->save();

        return response()->json([
            'message' => 'Health profile updated successfully',
            'profile' => $profile
        ]);
    }
}
