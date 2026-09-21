<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->editor()->create([
            'name' => 'Course Editor',
            'email' => 'editor@example.com',
        ]);

        User::factory()->create([
            'name' => 'Course Writer',
            'email' => 'writer@example.com',
        ]);
    }
}
