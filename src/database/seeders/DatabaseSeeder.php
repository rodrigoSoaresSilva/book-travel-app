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
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@exemplo.com',
            'admin' => true,
            'password' => bcrypt('123456'),
        ]);


        User::factory()->create([
            'email' => 'teste@exemplo.com',
            'password' => bcrypt('123456'),
        ]);

        User::factory()->create([
            'email' => 'teste2@exemplo.com',
            'password' => bcrypt('123456'),
        ]);
    }
}
