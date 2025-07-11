<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\TravelRequest;
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

        // Criar um usuário admin
        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@exemplo.com',
            'admin' => true,
        ]);

        // Criar 5 usuários comuns
        $users = User::factory()->count(5)->create();

        // Para cada usuário comum, criar pedidos de viagem variados
        foreach ($users as $user) {
            // 3 pedidos solicitados
            TravelRequest::factory()->count(3)->solicitado()->create([
                'user_id' => $user->id,
            ]);

            // 2 pedidos aprovados (por admin)
            TravelRequest::factory()->count(2)->aprovado($admin->id)->create([
                'user_id' => $user->id,
            ]);

            // 1 pedido cancelado (por admin)
            TravelRequest::factory()->cancelado($admin->id)->create([
                'user_id' => $user->id,
            ]);
        }
    }
}
