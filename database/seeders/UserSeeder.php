<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create main test user
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@healthy-grain.com',
            'password' => Hash::make('Admin@555#'),
            'age' => 30,
            'gender' => 'male',
            'height' => 175,
            'weight' => 70,
            'activity_level' => 'moderate',
            'goal' => 'maintain',
        ]);

        // Create 10 random users
        User::factory(10)->create();
    }
}
