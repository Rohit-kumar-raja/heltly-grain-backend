<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppUser;
use App\Models\Meal;
use App\Models\MealPlanRule;
use App\Models\MealPlanSetting;
use App\Models\UserHealthProfile;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Yajra\DataTables\Facades\DataTables;

class AppUserController extends Controller
{
    public function index()
    {
        return Inertia::render('AppUsers/Index');
    }

    public function getData()
    {
        return DataTables::of(AppUser::query())->toJson();
    }

    public function show($id)
    {
        $user = AppUser::with('healthProfile')->findOrFail($id);
        return Inertia::render('AppUsers/Show', [
            'user' => $user
        ]);
    }

    /**
     * Generate a diet plan preview for a specific app user (admin view).
     */
    public function dietPreview($id)
    {
        $user = AppUser::findOrFail($id);
        $healthProfile = UserHealthProfile::where('user_id', $user->id)->first();

        // 1. Calculate BMR (Mifflin-St Jeor)
        $weight = $user->weight ?? 70;
        $height = $user->height ?? 170;
        $age = $user->age ?? 25;
        $gender = strtolower($user->gender ?? 'male');

        if ($gender === 'female') {
            $bmr = (10 * $weight) + (6.25 * $height) - (5 * $age) - 161;
        } else {
            $bmr = (10 * $weight) + (6.25 * $height) - (5 * $age) + 5;
        }

        // 2. TDEE
        $activityMultipliers = [
            'sedentary' => 1.2,
            'light' => 1.375,
            'moderate' => 1.55,
            'active' => 1.725,
            'very_active' => 1.9,
        ];
        $activity = strtolower($user->activity_level ?? 'moderate');
        $tdee = $bmr * ($activityMultipliers[$activity] ?? 1.55);

        // 3. Goal adjustment
        $goal = strtolower($user->goal ?? 'maintain');
        $settings = MealPlanSetting::all()->pluck('value', 'key');
        $targetCalories = $tdee;

        if (str_contains($goal, 'lose') || str_contains($goal, 'weight loss')) {
            $targetCalories -= ($settings['cal_deficit_weight_loss'] ?? 500);
        } elseif (str_contains($goal, 'gain') || str_contains($goal, 'muscle')) {
            $targetCalories += ($settings['cal_surplus_muscle_gain'] ?? 300);
        }

        // 4. Rules engine
        $rules = MealPlanRule::with('product')->orderBy('priority', 'asc')->get();
        $tips = [];
        $recommendations = [];
        $triggeredRules = [];

        foreach ($rules as $rule) {
            if ($this->evaluateRule($rule, $user, $healthProfile)) {
                $triggeredRules[] = [
                    'name' => $rule->name,
                    'action_type' => $rule->action_type,
                    'action_value' => $rule->action_value,
                ];

                if ($rule->action_type === 'calorie_adjustment') {
                    $targetCalories += (int) $rule->action_value;
                } elseif ($rule->action_type === 'tip') {
                    $tips[] = $rule->action_value;
                }

                if ($rule->product) {
                    $recommendations[] = [
                        'id' => $rule->product->id,
                        'name' => $rule->product->name,
                        'price' => $rule->product->price,
                    ];
                }
            }
        }

        if ($targetCalories < 1200) $targetCalories = 1200;

        // 5. Select meals by calorie budget
        $budgets = [
            'breakfast' => round($targetCalories * 0.25),
            'lunch'     => round($targetCalories * 0.35),
            'dinner'    => round($targetCalories * 0.30),
            'snack'     => round($targetCalories * 0.10),
        ];

        $meals = [];
        foreach ($budgets as $type => $budget) {
            $meal = Meal::where('type', $type)
                ->orderByRaw('ABS(calories - ?) ASC', [$budget])
                ->first();
            $meals[$type] = $meal ? [
                'name' => $meal->name,
                'calories' => $meal->calories,
                'protein' => $meal->protein,
                'carbs' => $meal->carbs,
                'fats' => $meal->fats,
            ] : ['name' => "Suggested " . ucfirst($type), 'calories' => $budget, 'protein' => 30, 'carbs' => 40, 'fats' => 15];
        }

        $planTotals = [
            'calories' => collect($meals)->sum('calories'),
            'protein'  => collect($meals)->sum('protein'),
            'carbs'    => collect($meals)->sum('carbs'),
            'fats'     => collect($meals)->sum('fats'),
        ];

        return response()->json([
            'user_stats' => [
                'bmr' => round($bmr),
                'tdee' => round($tdee),
                'target_calories' => round($targetCalories),
                'goal' => $goal,
            ],
            'plan_totals' => $planTotals,
            'meals' => $meals,
            'tips' => $tips,
            'recommendations' => $recommendations,
            'triggered_rules' => $triggeredRules,
        ]);
    }

    /**
     * Evaluate a single rule against a user and their health profile.
     */
    private function evaluateRule($rule, $user, $healthProfile)
    {
        $field = $rule->condition_field;
        $value = null;

        if (str_contains($field, '.')) {
            if (!$healthProfile) return false;
            $parts = explode('.', $field);
            $data = $healthProfile->{$parts[0]};
            $key = $parts[1] ?? null;
            if (isset($data[$key])) $value = $data[$key];
        } elseif (in_array($field, ['goal', 'age', 'gender', 'weight', 'height'])) {
            $value = $user->$field;
        } elseif ($field === 'bmi') {
            $hM = ($user->height ?? 170) / 100;
            $value = ($user->weight ?? 70) / ($hM * $hM);
        }

        if ($value === null) return false;

        $op = $rule->operator;
        $target = $rule->condition_value;

        if ($op === 'contains') {
            if (is_array($value)) {
                foreach ($value as $v) {
                    if (strtolower((string)$v) === strtolower((string)$target)) return true;
                }
                return false;
            }
            return str_contains(strtolower((string)$value), strtolower((string)$target));
        }

        if (is_numeric($value) && is_numeric($target)) {
            $value = (float)$value;
            $target = (float)$target;
        }

        return match ($op) {
            '='  => $value == $target,
            '>'  => $value > $target,
            '<'  => $value < $target,
            '>=' => $value >= $target,
            '<=' => $value <= $target,
            default => false,
        };
    }
}
