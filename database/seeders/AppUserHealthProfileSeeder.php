<?php

namespace Database\Seeders;

use App\Models\AppUser;
use App\Models\UserHealthProfile;
use Illuminate\Database\Seeder;

class AppUserHealthProfileSeeder extends Seeder
{
    public function run(): void
    {
        $profiles = [
            // Profile 1: A fitness-focused male
            [
                'vitals' => ['heart_rate' => 68, 'bp_systolic' => 118, 'bp_diastolic' => 76, 'spo2' => 98, 'respiratory_rate' => 16, 'temp' => 36.6, 'hrv' => 55],
                'body_composition' => ['body_fat' => 18, 'muscle_mass' => 42],
                'lifestyle' => ['daily_steps' => 8500, 'sleep_hours' => 7.5, 'water_intake' => 3.2, 'exercise_minutes' => 45],
                'nutrition_stats' => ['sugar_intake' => 22, 'salt_intake' => 4.5, 'fiber_intake' => 28],
                'medical_history' => ['conditions' => [], 'allergies' => ['dust'], 'medications' => []],
                'lab_results' => ['fasting_sugar' => 92, 'pp_sugar' => 120, 'hba1c' => 5.4, 'cholesterol_total' => 185, 'ldl' => 110, 'hdl' => 55, 'vitamin_d' => 35, 'vitamin_b12' => 450, 'hemoglobin' => 14.5, 'tsh' => 2.8],
                'mental_health' => ['stress_level' => 4, 'mood' => 'good', 'screen_time' => 6],
            ],
            // Profile 2: Sedentary female with pre-diabetes concerns
            [
                'vitals' => ['heart_rate' => 78, 'bp_systolic' => 130, 'bp_diastolic' => 85, 'spo2' => 97, 'respiratory_rate' => 18, 'temp' => 36.8, 'hrv' => 38],
                'body_composition' => ['body_fat' => 32, 'muscle_mass' => 28],
                'lifestyle' => ['daily_steps' => 3200, 'sleep_hours' => 5.5, 'water_intake' => 1.5, 'exercise_minutes' => 10],
                'nutrition_stats' => ['sugar_intake' => 45, 'salt_intake' => 7, 'fiber_intake' => 12],
                'medical_history' => ['conditions' => ['pre-diabetes', 'PCOS'], 'allergies' => ['gluten'], 'medications' => ['metformin']],
                'lab_results' => ['fasting_sugar' => 115, 'pp_sugar' => 165, 'hba1c' => 6.2, 'cholesterol_total' => 220, 'ldl' => 145, 'hdl' => 42, 'vitamin_d' => 18, 'vitamin_b12' => 280, 'hemoglobin' => 11.5, 'tsh' => 4.2],
                'mental_health' => ['stress_level' => 7, 'mood' => 'anxious', 'screen_time' => 9],
            ],
            // Profile 3: Active senior
            [
                'vitals' => ['heart_rate' => 72, 'bp_systolic' => 135, 'bp_diastolic' => 82, 'spo2' => 96, 'respiratory_rate' => 17, 'temp' => 36.5, 'hrv' => 30],
                'body_composition' => ['body_fat' => 25, 'muscle_mass' => 35],
                'lifestyle' => ['daily_steps' => 6000, 'sleep_hours' => 6.5, 'water_intake' => 2.5, 'exercise_minutes' => 30],
                'nutrition_stats' => ['sugar_intake' => 18, 'salt_intake' => 5, 'fiber_intake' => 22],
                'medical_history' => ['conditions' => ['hypertension'], 'allergies' => [], 'medications' => ['amlodipine']],
                'lab_results' => ['fasting_sugar' => 98, 'pp_sugar' => 135, 'hba1c' => 5.8, 'cholesterol_total' => 200, 'ldl' => 125, 'hdl' => 50, 'vitamin_d' => 22, 'vitamin_b12' => 350, 'hemoglobin' => 13.2, 'tsh' => 3.5],
                'mental_health' => ['stress_level' => 3, 'mood' => 'calm', 'screen_time' => 4],
            ],
            // Profile 4: Young athlete
            [
                'vitals' => ['heart_rate' => 58, 'bp_systolic' => 110, 'bp_diastolic' => 70, 'spo2' => 99, 'respiratory_rate' => 14, 'temp' => 36.4, 'hrv' => 72],
                'body_composition' => ['body_fat' => 12, 'muscle_mass' => 52],
                'lifestyle' => ['daily_steps' => 15000, 'sleep_hours' => 8, 'water_intake' => 4, 'exercise_minutes' => 90],
                'nutrition_stats' => ['sugar_intake' => 15, 'salt_intake' => 3.5, 'fiber_intake' => 35],
                'medical_history' => ['conditions' => [], 'allergies' => [], 'medications' => []],
                'lab_results' => ['fasting_sugar' => 82, 'pp_sugar' => 105, 'hba1c' => 4.9, 'cholesterol_total' => 165, 'ldl' => 90, 'hdl' => 65, 'vitamin_d' => 48, 'vitamin_b12' => 620, 'hemoglobin' => 15.8, 'tsh' => 2.1],
                'mental_health' => ['stress_level' => 2, 'mood' => 'great', 'screen_time' => 3],
            ],
            // Profile 5: Office worker with vitamin deficiencies
            [
                'vitals' => ['heart_rate' => 82, 'bp_systolic' => 125, 'bp_diastolic' => 80, 'spo2' => 97, 'respiratory_rate' => 17, 'temp' => 36.7, 'hrv' => 42],
                'body_composition' => ['body_fat' => 28, 'muscle_mass' => 32],
                'lifestyle' => ['daily_steps' => 4000, 'sleep_hours' => 6, 'water_intake' => 2, 'exercise_minutes' => 15],
                'nutrition_stats' => ['sugar_intake' => 35, 'salt_intake' => 6, 'fiber_intake' => 15],
                'medical_history' => ['conditions' => ['vitamin deficiency'], 'allergies' => ['lactose'], 'medications' => ['vitamin D supplements']],
                'lab_results' => ['fasting_sugar' => 95, 'pp_sugar' => 130, 'hba1c' => 5.5, 'cholesterol_total' => 195, 'ldl' => 120, 'hdl' => 48, 'vitamin_d' => 12, 'vitamin_b12' => 180, 'hemoglobin' => 12.8, 'tsh' => 3.8],
                'mental_health' => ['stress_level' => 6, 'mood' => 'tired', 'screen_time' => 10],
            ],
        ];

        $users = AppUser::all();

        foreach ($users as $index => $user) {
            $profileData = $profiles[$index % count($profiles)];
            UserHealthProfile::updateOrCreate(
                ['user_id' => $user->id],
                $profileData
            );
        }
    }
}
