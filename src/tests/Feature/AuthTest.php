<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

/**
 * Testes relacionados à autenticação de usuários via JWT.
 */
class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa se um usuário pode realizar login com credenciais válidas.
     *
     * @return void
     */
    public function test_usuario_pode_logar_com_credenciais_corretas(): void
    {
        // Cria um usuário com e-mail e senha definidos
        $user = User::factory()->create([
            'email' => 'teste@exemplo.com',
            'password' => bcrypt('password'),
        ]);

        // Realiza uma requisição de login com as credenciais corretas
        $response = $this->postJson(self::getAuthUrl('login'), [
            'email' => 'teste@exemplo.com',
            'password' => 'password',
        ]);

        // Verifica se a resposta está correta e contém os dados esperados
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'access_token',
                     'token_type',
                     'expires_in',
                 ]);
    }

    /**
     * Testa se o sistema impede login com credenciais inválidas.
     *
     * @return void
     */
    public function test_usuario_nao_pode_logar_com_credenciais_incorretas(): void
    {
        // Tenta fazer login com e-mail e senha inexistentes
        $response = $this->postJson(self::getAuthUrl('login'), [
            'email' => 'errado@example.com',
            'password' => 'senhaerrada',
        ]);

        // Espera receber status 401 e mensagem de erro apropriada
        $response->assertStatus(401)
                 ->assertJson(['error' => 'Não autorizado']);
    }

    /**
     * Testa se um usuário autenticado pode acessar seus próprios dados.
     *
     * @return void
     */
    public function test_usuario_autenticado_pode_ver_seus_dados(): void
    {
        // Cria um usuário e gera um token JWT
        $user = User::factory()->create();
        $token = auth('api')->login($user);

        // Realiza requisição autenticada ao endpoint "me"
        $response = $this->withHeader('Authorization', "Bearer $token")
                         ->postJson(self::getAuthUrl('me'));

        // Verifica se os dados retornados correspondem ao usuário logado
        $response->assertStatus(200)
                 ->assertJson([
                     'id' => $user->id,
                     'email' => $user->email,
                 ]);
    }

    /**
     * Testa se um usuário autenticado pode realizar logout.
     *
     * @return void
     */
    public function test_usuario_pode_fazer_logout(): void
    {
        // Cria um usuário e autentica
        $user = User::factory()->create();
        $token = auth('api')->login($user);

        // Realiza a requisição de logout
        $response = $this->withHeader('Authorization', "Bearer $token")
                         ->postJson(self::getAuthUrl('logout'));

        // Verifica se a resposta indica sucesso
        $response->assertStatus(200)
                 ->assertJson(['message' => 'Desconectado com sucesso!']);
    }
}
