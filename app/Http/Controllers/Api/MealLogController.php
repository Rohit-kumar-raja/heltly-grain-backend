<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MealLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MealLogController extends Controller
{
    /**
     * Get meal logs for the current user
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $date = $request->query('date', Carbon::today()->toDateString());

        // Get logs for the specific date
        $logs = MealLog::where('user_id', $user->id)
            ->where('logged_date', $date)
            ->orderBy('logged_time')
            ->get();

        // Calculate daily totals
        $totals = [
            'calories' => $logs->sum('calories'),
            'protein' => $logs->sum('protein'),
            'carbs' => $logs->sum('carbs'),
            'fats' => $logs->sum('fats'),
        ];

        // Get logged meal types for today
        $loggedMealTypes = $logs->pluck('meal_type')->unique()->values();

        return response()->json([
            'success' => true,
            'data' => [
                'logs' => $logs,
                'totals' => $totals,
                'logged_meal_types' => $loggedMealTypes,
                'date' => $date
            ]
        ]);
    }

    /**
     * Log a meal
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'meal_type' => 'required|string|in:breakfast,lunch,dinner,snack',
            'meal_name' => 'required|string|max:255',
            'calories' => 'required|integer|min:0',
            'protein' => 'nullable|integer|min:0',
            'carbs' => 'nullable|integer|min:0',
            'fats' => 'nullable|integer|min:0',
            'logged_date' => 'nullable|date',
            'logged_time' => 'nullable|date_format:H:i',
            'notes' => 'nullable|string|max:500',
        ]);

        $user = $request->user();

        // Set defaults
        $validated['user_id'] = $user->id;
        $validated['logged_date'] = $validated['logged_date'] ?? Carbon::today()->toDateString();
        $validated['logged_time'] = $validated['logged_time'] ?? Carbon::now()->format('H:i');
        $validated['protein'] = $validated['protein'] ?? 0;
        $validated['carbs'] = $validated['carbs'] ?? 0;
        $validated['fats'] = $validated['fats'] ?? 0;

        $mealLog = MealLog::create($validated);

        // Also update progress log with the totals for the day
        $this->updateDailyProgress($user->id, $validated['logged_date']);

        return response()->json([
            'success' => true,
            'message' => 'Meal logged successfully',
            'data' => $mealLog
        ], 201);
    }

    /**
     * Delete a meal log
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();

        $mealLog = MealLog::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$mealLog) {
            return response()->json([
                'success' => false,
                'message' => 'Meal log not found'
            ], 404);
        }

        $date = $mealLog->logged_date->toDateString();
        $mealLog->delete();

        // Update progress log
        $this->updateDailyProgress($user->id, $date);

        return response()->json([
            'success' => true,
            'message' => 'Meal log deleted'
        ]);
    }

    /**
     * Update the daily progress based on meal logs
     */
    private function updateDailyProgress($userId, $date)
    {
        $logs = MealLog::where('user_id', $userId)
            ->where('logged_date', $date)
            ->get();

        $totals = [
            'calories' => $logs->sum('calories'),
            'protein' => $logs->sum('protein'),
            'carbs' => $logs->sum('carbs'),
            'fats' => $logs->sum('fats'),
        ];

        // Update or create progress log for this date
        \App\Models\ProgressLog::updateOrCreate(
            ['user_id' => $userId, 'date' => $date],
            $totals
        );
    }

    /**
     * Get meal log history
     */
    public function history(Request $request)
    {
        $user = $request->user();
        $days = $request->query('days', 7);

        $startDate = Carbon::today()->subDays($days);

        $logs = MealLog::where('user_id', $user->id)
            ->where('logged_date', '>=', $startDate)
            ->orderBy('logged_date', 'desc')
            ->orderBy('logged_time', 'desc')
            ->get()
            ->groupBy(function ($log) {
                return $log->logged_date->toDateString();
            });

        return response()->json([
            'success' => true,
            'data' => $logs
        ]);
    }
}
