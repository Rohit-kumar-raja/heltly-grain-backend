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
        Schema::create('user_health_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('app_users')->onDelete('cascade');

            // Health Data Categories stored as JSON for flexibility
            $table->json('vitals')->nullable(); // HR, BP, SpO2, RespRate, Temp, HRV
            $table->json('body_composition')->nullable(); // Body Fat, Muscle Mass, etc.
            $table->json('lifestyle')->nullable(); // Steps, Exercise, Sleep, Water
            $table->json('nutrition_stats')->nullable(); // Daily Calorie Intake, Macros
            $table->json('medical_history')->nullable(); // Conditions, Meds, Allergies
            $table->json('lab_results')->nullable(); // Sugar, Cholesterol, Vitamins
            $table->json('mental_health')->nullable(); // Stress, Mood

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_health_profiles');
    }
};
