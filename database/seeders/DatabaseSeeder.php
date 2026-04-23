<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
                'role' => 'viewer',
                'is_active' => true,
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'admin@birliktekardeslik.org'],
            [
                'name' => 'Admin',
                'password' => bcrypt('Birlikte123!'),
                'role' => 'super_admin',
                'is_active' => true,
            ]
        );

        $this->call([
            DemoContentSeeder::class,
        ]);
    }
}
