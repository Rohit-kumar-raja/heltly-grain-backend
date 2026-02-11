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
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            // Since we use AppUser, we need to decide if we use foreign key constraint directly or just bigInteger to avoid issues if tables are different.
            // But we know 'app_users' table exists.
            $table->foreignId('user_id')->constrained('app_users')->onDelete('cascade');
            $table->string('status')->default('active'); // active, abandoned, converted
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
