<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Meal;
use App\Models\MealPlanSetting;
use Illuminate\Http\Request;

class MealPlanController extends Controller
{
    public function suggest(Request $request)
    {
        $user = $request->user();

        // 1. Calculate BMR (Mifflin-St Jeor)
        $weight = $user->weight ?? 70; // fallback
        $height = $user->height ?? 170; // fallback
        $age = $user->age ?? 25; // fallback
        $gender = strtolower($user->gender ?? 'male');

        if ($gender === 'female') {
            $bmr = (10 * $weight) + (6.25 * $height) - (5 * $age) - 161;
        } else {
            $bmr = (10 * $weight) + (6.25 * $height) - (5 * $age) + 5;
        }

        // 2. TDEE based on activity level
        $activityMultipliers = [
            'sedentary' => 1.2,
            'light' => 1.375,
            'moderate' => 1.55,
            'active' => 1.725,
            'very_active' => 1.9,
        ];

        $activity = strtolower($user->activity_level ?? 'moderate');
        $multiplier = $activityMultipliers[$activity] ?? 1.55;
        $tdee = $bmr * $multiplier;

        // 3. Adjust for Goal
        $goal = strtolower($user->goal ?? 'maintain');
        $settings = MealPlanSetting::all()->pluck('value', 'key');

        $targetCalories = $tdee;

        if (str_contains($goal, 'lose') || str_contains($goal, 'weight loss')) {
            $deficit = $settings['cal_deficit_weight_loss'] ?? 500;
            $targetCalories -= $deficit;
        } elseif (str_contains($goal, 'gain') || str_contains($goal, 'muscle')) {
            $surplus = $settings['cal_surplus_muscle_gain'] ?? 300;
            $targetCalories += $surplus;
        }

        // Enjoy more personalized logic reading from HealthProfile
        $healthProfile = \App\Models\UserHealthProfile::where('user_id', $user->id)->first();

        // --- Rules Engine Execution ---
        // Eager load product to avoid N+1 if many rules trigger
        $rules = \App\Models\MealPlanRule::with('product')->orderBy('priority', 'asc')->get();
        $tips = [];
        $recommendations = [];

        foreach ($rules as $rule) {
            if ($this->evaluateRule($rule, $user, $healthProfile)) {
                // Apply Action
                if ($rule->action_type === 'calorie_adjustment') {
                    $adj = (int) $rule->action_value;
                    $targetCalories += $adj;
                } elseif ($rule->action_type === 'tip') {
                    $tips[] = $rule->action_value;
                }

                // Collect Product Recommendation if exists
                if ($rule->product) {
                    $recommendations[] = $rule->product;
                }
            }
        }

        // Ensure safety lower limit
        if ($targetCalories < 1200) $targetCalories = 1200;

        // 4. Calculate per-meal calorie budgets
        $budgets = [
            'breakfast' => round($targetCalories * 0.25),
            'lunch'     => round($targetCalories * 0.35),
            'dinner'    => round($targetCalories * 0.30),
            'snack'     => round($targetCalories * 0.10),
        ];

        // 5. Select meals closest to their calorie budget
        $selectedMeals = [];
        foreach ($budgets as $type => $budget) {
            $meal = Meal::where('type', $type)
                ->orderByRaw('ABS(calories - ?) ASC', [$budget])
                ->first();

            $selectedMeals[$type] = $meal ?? $this->dummyMeal(ucfirst($type), $budget);
        }

        $breakfast = $selectedMeals['breakfast'];
        $lunch     = $selectedMeals['lunch'];
        $dinner    = $selectedMeals['dinner'];
        $snack     = $selectedMeals['snack'];

        $totalCals = $breakfast->calories + $lunch->calories + $dinner->calories + $snack->calories;

        return response()->json([
            'success' => true,
            'user_stats' => [
                'bmr' => round($bmr),
                'tdee' => round($tdee),
                'target_calories' => round($targetCalories),
                'goal' => $goal
            ],
            'plan_totals' => [
                'calories' => $totalCals,
                'protein' => $breakfast->protein + $lunch->protein + $dinner->protein + $snack->protein,
                'carbs' => $breakfast->carbs + $lunch->carbs + $dinner->carbs + $snack->carbs,
                'fats' => $breakfast->fats + $lunch->fats + $dinner->fats + $snack->fats,
            ],
            'meals' => [
                'breakfast' => $breakfast,
                'lunch' => $lunch,
                'dinner' => $dinner,
                'snack' => $snack,
            ],
            'tips' => $tips ?? [],
            'recommendations' => $recommendations ?? [] // Pass recommendations
        ]);
    }

    private function evaluateRule($rule, $user, $healthProfile)
    {
        $field = $rule->condition_field;
        $value = null;

        // Check if field is nested (contains dot)
        if (str_contains($field, '.')) {
            if (!$healthProfile) return false;

            $parts = explode('.', $field);
            $category = $parts[0];
            $key = $parts[1] ?? null;

            $data = $healthProfile->$category; // This is an array cast

            if (isset($data[$key])) {
                $value = $data[$key];
            }
            // For medical_history.conditions which is an array itself
            elseif (($key === 'conditions' || $key === 'allergies' || $key === 'medications') && isset($data[$key])) {
                $value = $data[$key];
            }
        } elseif (in_array($field, ['goal', 'age', 'gender', 'weight', 'height'])) {
            $value = $user->$field;
        } elseif ($field === 'bmi') {
            $hM = ($user->height ?? 170) / 100;
            $value = ($user->weight ?? 70) / ($hM * $hM);
        }

        if ($value === null) return false;

        $op = $rule->operator;
        $target = $rule->condition_value;

        // Handling 'contains' for Array values (e.g. Conditions list)
        if ($op === 'contains') {
            if (is_array($value)) {
                foreach ($value as $v) {
                    if (strtolower((string)$v) === strtolower((string)$target)) return true;
                }
                return false;
            } else {
                return str_contains(strtolower((string)$value), strtolower((string)$target));
            }
        }

        // Numeric normalization
        if (is_numeric($value) && is_numeric($target)) {
            $value = (float)$value;
            $target = (float)$target;
        }

        switch ($op) {
            case '=':
                return $value == $target;
            case '>':
                return $value > $target;
            case '<':
                return $value < $target;
            case '>=':
                return $value >= $target;
            case '<=':
                return $value <= $target;
            default:
                return false;
        }
    }

    private function dummyMeal($type, $cals)
    {
        $m = new Meal();
        $m->name = "Suggested $type";
        $m->description = "A healthy $type option";
        $m->calories = $cals;
        $m->protein = 30;
        $m->carbs = 40;
        $m->fats = 15;
        $m->type = strtolower($type);
        return $m;
    }
}
