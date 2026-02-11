<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProgressLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ProgressController extends Controller
{
    /**
     * Get progress logs for the current user
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Get all logs ordered by date
        $logs = ProgressLog::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->get();

        // Calculate statistics
        $latestLog = $logs->first();
        $firstLog = $logs->last();

        // Weekly data (last 7 days)
        $weeklyLogs = ProgressLog::where('user_id', $user->id)
            ->where('date', '>=', Carbon::now()->subDays(7))
            ->orderBy('date')
            ->get();

        // Calculate averages
        $avgCalories = $weeklyLogs->avg('calories') ?? 0;
        $avgProtein = $weeklyLogs->avg('protein') ?? 0;
        $avgCarbs = $weeklyLogs->avg('carbs') ?? 0;
        $avgFats = $weeklyLogs->avg('fats') ?? 0;

        // Weight change
        $weightChange = 0;
        if ($latestLog && $firstLog && $latestLog->weight && $firstLog->weight) {
            $weightChange = $firstLog->weight - $latestLog->weight;
        }

        // Calculate streak (consecutive days with logs)
        $streak = 0;
        $checkDate = Carbon::today();
        while (true) {
            $hasLog = ProgressLog::where('user_id', $user->id)
                ->whereDate('date', $checkDate)
                ->exists();
            if ($hasLog) {
                $streak++;
                $checkDate->subDay();
            } else {
                break;
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'logs' => $logs,
                'weekly_logs' => $weeklyLogs,
                'current_weight' => $latestLog?->weight ?? null,
                'weight_change' => round($weightChange, 1),
                'streak' => $streak,
                'averages' => [
                    'calories' => round($avgCalories),
                    'protein' => round($avgProtein),
                    'carbs' => round($avgCarbs),
                    'fats' => round($avgFats),
                ]
            ]
        ]);
    }

    /**
     * Log daily progress
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'weight' => 'nullable|numeric|min:20|max:500',
            'calories' => 'nullable|integer|min:0',
            'protein' => 'nullable|integer|min:0',
            'carbs' => 'nullable|integer|min:0',
            'fats' => 'nullable|integer|min:0',
        ]);

        $user = $request->user();

        // Update or create log for this date
        $log = ProgressLog::updateOrCreate(
            ['user_id' => $user->id, 'date' => $validated['date']],
            $validated
        );

        return response()->json([
            'success' => true,
            'message' => 'Progress logged successfully',
            'data' => $log
        ]);
    }
}
