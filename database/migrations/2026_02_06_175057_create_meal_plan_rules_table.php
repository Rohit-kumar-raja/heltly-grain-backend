<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('meal_plan_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            // Condition
            $table->string('condition_field'); // e.g., 'vitals.bp_systolic'
            $table->string('operator'); // e.g., '>', '<', '=', 'contains'
            $table->string('condition_value'); // e.g., '140'

            // Action
            $table->string('action_type'); // e.g., 'calorie_adjustment', 'macro_distribution', 'tip'
            $table->json('action_value'); // e.g., '-500', '{"p":40,"c":30,"f":30}', 'Limit Sodium'

            $table->integer('priority')->default(0); // Higher runs later (overrides) or first depending on logic
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meal_plan_rules');
    }
};
