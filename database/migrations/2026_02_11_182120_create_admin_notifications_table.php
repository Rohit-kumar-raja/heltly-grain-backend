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
        Schema::create('admin_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // order, user, system
            $table->string('title');
            $table->string('message');
            $table->string('icon')->default('pi pi-bell');
            $table->string('color')->default('bg-blue-500');
            $table->string('link')->nullable(); // route name or URL
            $table->unsignedBigInteger('reference_id')->nullable(); // order_id, user_id, etc.
            $table->boolean('read')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_notifications');
    }
};
