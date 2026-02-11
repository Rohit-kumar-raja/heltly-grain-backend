<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Meal;
use App\Models\MealPlanSetting;

class MealSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Settings
        $settings = [
            ['key' => 'cal_deficit_weight_loss', 'value' => '500', 'description' => 'Calories to subtract from TDEE for weight loss'],
            ['key' => 'cal_surplus_muscle_gain', 'value' => '300', 'description' => 'Calories to add to TDEE for muscle gain'],
            ['key' => 'macro_protein_ratio', 'value' => '30', 'description' => 'Percentage of calories from protein'],
            ['key' => 'macro_carbs_ratio', 'value' => '40', 'description' => 'Percentage of calories from carbs'],
            ['key' => 'macro_fats_ratio', 'value' => '30', 'description' => 'Percentage of calories from fats'],
        ];

        foreach ($settings as $setting) {
            MealPlanSetting::updateOrCreate(['key' => $setting['key']], $setting);
        }

        // Meals
        $meals = [
            // Breakfast
            [
                'name' => 'Oatmeal with Berries',
                'description' => 'Rolled oats topped with fresh mixed berries and honey.',
                'calories' => 350,
                'protein' => 12,
                'carbs' => 60,
                'fats' => 6,
                'type' => 'breakfast',
            ],
            [
                'name' => 'Scrambled Eggs & Toast',
                'description' => 'Two eggs scrambled with whole wheat toast.',
                'calories' => 450,
                'protein' => 22,
                'carbs' => 30,
                'fats' => 25,
                'type' => 'breakfast',
            ],
            // Lunch
            [
                'name' => 'Grilled Chicken Salad',
                'description' => 'Grilled chicken breast on mixed greens with vinaigrette.',
                'calories' => 500,
                'protein' => 40,
                'carbs' => 10,
                'fats' => 20,
                'type' => 'lunch',
            ],
            [
                'name' => 'Quinoa Bowl',
                'description' => 'Quinoa with roasted vegetables and lemon dressing.',
                'calories' => 600,
                'protein' => 20,
                'carbs' => 80,
                'fats' => 18,
                'type' => 'lunch',
            ],
            // Dinner
            [
                'name' => 'Salmon with Asparagus',
                'description' => 'Baked salmon fillet with steamed asparagus.',
                'calories' => 550,
                'protein' => 35,
                'carbs' => 8,
                'fats' => 30,
                'type' => 'dinner',
            ],
            [
                'name' => 'Turkey Stir Fry',
                'description' => 'Lean ground turkey with mixed veggies over rice.',
                'calories' => 500,
                'protein' => 30,
                'carbs' => 50,
                'fats' => 15,
                'type' => 'dinner',
            ],
            // Snack
            [
                'name' => 'Greek Yogurt & Honey',
                'description' => 'Low-fat Greek yogurt with a drizzle of honey.',
                'calories' => 150,
                'protein' => 15,
                'carbs' => 20,
                'fats' => 0,
                'type' => 'snack',
            ],
            [
                'name' => 'Almonds & Apple',
                'description' => 'Handful of almonds and a fresh apple.',
                'calories' => 200,
                'protein' => 4,
                'carbs' => 25,
                'fats' => 10,
                'type' => 'snack',
            ],
        ];

        foreach ($meals as $meal) {
            Meal::create($meal);
        }
    }
}
