<?php

namespace Tests\Unit\Policies;

use App\Models\TravelRequest;
use App\Models\User;
use App\Policies\TravelRequestPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Testes unitários para a classe TravelRequestPolicy.
 */
class TravelRequestPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected TravelRequestPolicy $policy;

    /**
     * Setup executado antes de cada teste.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->policy = new TravelRequestPolicy();
    }

    /**
     * Testa se um usuário comum vê seus próprios pedidos ou qualquer pedido se for admin.
     */
    public function test_usuario_dono_ou_admin_pode_ver_o_pedido_de_viagem()
    {
        $user = User::factory()->create();
        $admin = User::factory()->create(['admin' => true]);
        $request = TravelRequest::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($this->policy->view($user, $request));
        $this->assertTrue($this->policy->view($admin, $request));
    }

    /**
     * Testa se um usuário comum não tem acesso aos pedidos alheios.
     */
    public function test_usuario_nao_dono_nao_pode_ver_o_pedido_de_viagem()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $request = TravelRequest::factory()->create(['user_id' => $user1->id]);

        $this->assertFalse($this->policy->view($user2, $request));
    }

    /**
     * Testa se um usuário pode atualizar seus próprios pedidos.
     */
    public function test_apenas_o_dono_pode_atualizar_pedido_de_viagem()
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $request = TravelRequest::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($this->policy->update($user, $request));
        $this->assertFalse($this->policy->update($other, $request));
    }

    /**
     * Testa se um usuário pode excluir seus próprios pedidos.
     */
    public function test_apenas_o_dono_pode_excluir_pedido_de_viagem()
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $request = TravelRequest::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($this->policy->delete($user, $request));
        $this->assertFalse($this->policy->delete($other, $request));
    }

    /**
     * Testa se um usuário admin pode aprovar pedidos.
     */
    public function test_apenas_admin_pode_aprovar_pedido_de_viagem()
    {
        $admin = User::factory()->create(['admin' => true]);
        $user = User::factory()->create();

        $this->assertTrue($this->policy->approve($admin));
        $this->assertFalse($this->policy->approve($user));
    }

    /**
     * Testa se um usuário admin pode cancelar pedidos.
     */
    public function test_apenas_admin_pode_cancelar_pedido_de_viagem()
    {
        $admin = User::factory()->create(['admin' => true]);
        $user = User::factory()->create();

        $this->assertTrue($this->policy->cancel($admin));
        $this->assertFalse($this->policy->cancel($user));
    }
}
