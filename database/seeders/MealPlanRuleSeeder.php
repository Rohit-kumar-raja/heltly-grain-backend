<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MealPlanRule;
use App\Models\Product;

class MealPlanRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Fetch product IDs for linking recommendations
        $products = Product::pluck('id', 'name');

        $rules = [
            // ── Goal-based Rules ──────────────────────────────
            [
                'name'            => 'Weight Loss Calorie Reduction',
                'condition_field' => 'goal',
                'operator'        => 'contains',
                'condition_value' => 'lose',
                'action_type'     => 'calorie_adjustment',
                'action_value'    => -300,
                'priority'        => 10,
                'product'         => 'Rolled Oats',
            ],
            [
                'name'            => 'Weight Loss Diet Tip',
                'condition_field' => 'goal',
                'operator'        => 'contains',
                'condition_value' => 'lose',
                'action_type'     => 'tip',
                'action_value'    => 'Focus on high-protein, low-calorie meals. Include plenty of vegetables and lean protein sources.',
                'priority'        => 10,
                'product'         => null,
            ],
            [
                'name'            => 'Muscle Gain Calorie Boost',
                'condition_field' => 'goal',
                'operator'        => 'contains',
                'condition_value' => 'gain',
                'action_type'     => 'calorie_adjustment',
                'action_value'    => 200,
                'priority'        => 10,
                'product'         => 'Quinoa Seeds',
            ],
            [
                'name'            => 'Muscle Gain Protein Tip',
                'condition_field' => 'goal',
                'operator'        => 'contains',
                'condition_value' => 'gain',
                'action_type'     => 'tip',
                'action_value'    => 'Increase protein intake to 1.6-2.2g per kg body weight. Have a protein-rich snack post-workout.',
                'priority'        => 10,
                'product'         => 'Protein Muesli',
            ],

            // ── BMI-based Rules ──────────────────────────────
            [
                'name'            => 'High BMI Calorie Adjustment',
                'condition_field' => 'bmi',
                'operator'        => '>',
                'condition_value' => '30',
                'action_type'     => 'calorie_adjustment',
                'action_value'    => -200,
                'priority'        => 20,
                'product'         => 'Brown Rice',
            ],
            [
                'name'            => 'Underweight Calorie Boost',
                'condition_field' => 'bmi',
                'operator'        => '<',
                'condition_value' => '18.5',
                'action_type'     => 'calorie_adjustment',
                'action_value'    => 300,
                'priority'        => 20,
                'product'         => 'Granola Crunch',
            ],

            // ── Age-based Rules ──────────────────────────────
            [
                'name'            => 'Senior Nutrition Tip',
                'condition_field' => 'age',
                'operator'        => '>',
                'condition_value' => '50',
                'action_type'     => 'tip',
                'action_value'    => 'Ensure adequate calcium and Vitamin D intake to maintain bone health. Include dairy or fortified foods.',
                'priority'        => 15,
                'product'         => null,
            ],
            [
                'name'            => 'Young Adult Balanced Diet Tip',
                'condition_field' => 'age',
                'operator'        => '<',
                'condition_value' => '25',
                'action_type'     => 'tip',
                'action_value'    => 'Maintain a balanced diet with whole grains, lean protein, and plenty of fruits and vegetables for long-term health.',
                'priority'        => 15,
                'product'         => 'Mixed Millets',
            ],

            // ── Gender-based Rules ──────────────────────────
            [
                'name'            => 'Iron-Rich Foods for Women',
                'condition_field' => 'gender',
                'operator'        => '=',
                'condition_value' => 'female',
                'action_type'     => 'tip',
                'action_value'    => 'Include iron-rich foods like spinach, lentils, and fortified grains to prevent iron deficiency.',
                'priority'        => 15,
                'product'         => 'Mixed Millets',
            ],

            // ── Health Profile: Vitals ──────────────────────
            [
                'name'            => 'High Blood Pressure Advisory',
                'condition_field' => 'vitals.bp_systolic',
                'operator'        => '>',
                'condition_value' => '140',
                'action_type'     => 'tip',
                'action_value'    => 'Limit sodium intake to under 2300mg/day. Choose fresh foods over processed ones and avoid adding extra salt.',
                'priority'        => 30,
                'product'         => 'Broken Wheat',
            ],

            // ── Health Profile: Medical History ──────────────
            [
                'name'            => 'Diabetes Calorie Management',
                'condition_field' => 'medical_history.conditions',
                'operator'        => 'contains',
                'condition_value' => 'diabetes',
                'action_type'     => 'calorie_adjustment',
                'action_value'    => -100,
                'priority'        => 30,
                'product'         => 'Brown Rice',
            ],
            [
                'name'            => 'Diabetes Diet Tip',
                'condition_field' => 'medical_history.conditions',
                'operator'        => 'contains',
                'condition_value' => 'diabetes',
                'action_type'     => 'tip',
                'action_value'    => 'Choose low glycemic index foods. Monitor carbohydrate intake and spread it evenly across meals.',
                'priority'        => 30,
                'product'         => null,
            ],

            // ── Health Profile: Lab Results ──────────────────
            [
                'name'            => 'High Cholesterol Advisory',
                'condition_field' => 'lab_results.cholesterol_total',
                'operator'        => '>',
                'condition_value' => '200',
                'action_type'     => 'tip',
                'action_value'    => 'Increase fiber intake and include omega-3 rich foods. Limit saturated fats and avoid trans fats.',
                'priority'        => 25,
                'product'         => 'Chia Seeds',
            ],

            // ── Health Profile: Lifestyle ────────────────────
            [
                'name'            => 'Poor Sleep Recovery Tip',
                'condition_field' => 'lifestyle.sleep_hours',
                'operator'        => '<',
                'condition_value' => '6',
                'action_type'     => 'tip',
                'action_value'    => 'Include magnesium-rich foods like nuts, seeds, and whole grains. Avoid caffeine after 2 PM for better sleep.',
                'priority'        => 20,
                'product'         => 'Flax Seeds',
            ],
            [
                'name'            => 'Low Water Intake Reminder',
                'condition_field' => 'lifestyle.water_intake',
                'operator'        => '<',
                'condition_value' => '2',
                'action_type'     => 'tip',
                'action_value'    => 'Drink at least 2-3 liters of water daily. Include hydrating foods like cucumbers, watermelon, and soups.',
                'priority'        => 20,
                'product'         => null,
            ],
        ];

        foreach ($rules as $ruleData) {
            $productName = $ruleData['product'];
            unset($ruleData['product']);

            $ruleData['product_id'] = $productName ? ($products[$productName] ?? null) : null;

            MealPlanRule::updateOrCreate(
                ['name' => $ruleData['name']],
                $ruleData
            );
        }
    }
}
