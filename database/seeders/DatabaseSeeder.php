<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ShieldSeeder::class,
        ]);

        User::factory()->create([
            'name' => 'José Carlos',
            'email' => 'admin@admin.com',
            'password' => bcrypt('123Mudar'),
            'email_verified_at' => now(),
        ]);
    }
}
