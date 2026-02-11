<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MealPlanRule;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Yajra\DataTables\Facades\DataTables;

class MealPlanRuleController extends Controller
{
    public function index()
    {
        return Inertia::render('MealPlan/Rules', [
            'conditionOptions' => [
                // Core
                'goal' => 'Personal Goal',
                'age' => 'Age',
                'gender' => 'Gender',
                'bmi' => 'BMI',

                // Vitals
                'vitals.heart_rate' => 'Heart Rate (BPM)',
                'vitals.bp_systolic' => 'Blood Pressure (Systolic)',
                'vitals.bp_diastolic' => 'Blood Pressure (Diastolic)',
                'vitals.spo2' => 'Blood Oxygen (SpO2)',
                'vitals.respiratory_rate' => 'Respiratory Rate',
                'vitals.temp' => 'Body Temperature',
                'vitals.hrv' => 'Heart Rate Variability',

                // Body Composition
                'body_composition.body_fat' => 'Body Fat %',
                'body_composition.muscle_mass' => 'Muscle Mass',

                // Lifestyle
                'lifestyle.daily_steps' => 'Daily Steps',
                'lifestyle.sleep_hours' => 'Sleep Duration (Hrs)',
                'lifestyle.water_intake' => 'Water Intake (L)',
                'lifestyle.exercise_minutes' => 'Exercise Duration (Min)',

                // Nutrition Stats (Avg)
                'nutrition_stats.sugar_intake' => 'Sugar Intake',
                'nutrition_stats.salt_intake' => 'Salt Intake',
                'nutrition_stats.fiber_intake' => 'Fiber Intake',

                // Medical History
                'medical_history.conditions' => 'Medical Conditions (Contains)',
                'medical_history.allergies' => 'Allergies (Contains)',
                'medical_history.medications' => 'Medications (Contains)',

                // Lab Results
                'lab_results.fasting_sugar' => 'Fasting Blood Sugar',
                'lab_results.pp_sugar' => 'PP Blood Sugar',
                'lab_results.hba1c' => 'HbA1c',
                'lab_results.cholesterol_total' => 'Total Cholesterol',
                'lab_results.ldl' => 'LDL Cholesterol',
                'lab_results.hdl' => 'HDL Cholesterol',
                'lab_results.vitamin_d' => 'Vitamin D',
                'lab_results.vitamin_b12' => 'Vitamin B12',
                'lab_results.hemoglobin' => 'Hemoglobin',
                'lab_results.tsh' => 'Thyroid (TSH)',

                // Mental Health
                'mental_health.stress_level' => 'Stress Level (1-10)',
                'mental_health.mood' => 'Mood',
                'mental_health.screen_time' => 'Screen Time (Hrs)',
            ],
            'actionOptions' => [
                'calorie_adjustment' => 'Adjust Calories',
                'tip' => 'Add Health Tip',
            ],
            'products' => \App\Models\Product::select('id', 'name')->get()
        ]);
    }

    public function getData()
    {
        return DataTables::of(MealPlanRule::query())->toJson();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'condition_field' => 'required',
            'operator' => 'required',
            'condition_value' => 'required',
            'action_type' => 'required',
            'action_value' => 'required',
            'priority' => 'required|integer',
            'product_id' => 'nullable|exists:products,id',
        ]);

        MealPlanRule::create($request->all());

        return redirect()->back()->with('success', 'Rule created successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'condition_field' => 'required',
            'operator' => 'required',
            'condition_value' => 'required',
            'action_type' => 'required',
            'action_value' => 'required',
            'priority' => 'required|integer',
            'product_id' => 'nullable|exists:products,id',
        ]);

        $rule = MealPlanRule::findOrFail($id);
        $rule->update($request->all());

        return redirect()->back()->with('success', 'Rule updated successfully.');
    }

    public function destroy($id)
    {
        MealPlanRule::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Rule deleted successfully.');
    }
}
