<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    // public function setUp(): void
    // {
    //     parent::setUp();
    //     $this->seed();
    // }

    public function test_usuario_pode_logar_com_credenciais_corretas()
    {
        $user = User::factory()->create([
            'email' => 'teste@exemplo.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'teste@exemplo.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['access_token', 'token_type', 'expires_in']);
    }

    public function test_usuario_nao_pode_logar_com_credenciais_incorretas()
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'errado@example.com',
            'password' => 'senhaerrada',
        ]);

        $response->assertStatus(401)
                 ->assertJson(['error' => 'Não autorizado']);
    }

    public function test_usuario_autenticado_pode_ver_seus_dados()
    {
        $user = User::factory()->create();

        $token = auth('api')->login($user);

        $response = $this->withHeader('Authorization', "Bearer $token")
                         ->postJson('/api/auth/me');

        $response->assertStatus(200)
                 ->assertJson([
                     'id' => $user->id,
                     'email' => $user->email,
                 ]);
    }

    public function test_usuario_pode_fazer_logout()
    {
        $user = User::factory()->create();
        $token = auth('api')->login($user);

        $response = $this->withHeader('Authorization', "Bearer $token")
                         ->postJson('/api/auth/logout');

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Desconectado com sucesso!']);
    }
}
