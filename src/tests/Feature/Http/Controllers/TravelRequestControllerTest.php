<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\User;
use App\Models\TravelRequest;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Abstracts\TravelRequest\AbstractTravelRequestTest;

class TravelRequestControllerTest extends AbstractTravelRequestTest
{
    use RefreshDatabase;

    protected int $userId;
    protected string $token;

    /**
     * Setup executado antes de cada teste.
     * Cria um usuário e obtém um token JWT válido para autenticação.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        $this->token = auth('api')->login($user);
        $this->userId = $user->id;
    }

    /**
     * Testa se um usuário autenticado pode criar um pedido de viagem.
     */
    public function test_usuario_pode_criar_pedido_de_viagem(): void
    {
        $payload = [
            'destination'    => 'Roma',
            'departure_date' => '2025-08-01',
            'return_date'    => '2025-08-15',
        ];

        $response = $this->withToken($this->token)->postJson($this->getUrl(), $payload);

        $response->assertCreated()
                 ->assertJsonFragment(['destination' => 'Roma']);
    }

    /**
     * Testa se um usuário autenticado pode editar um pedido de viagem.
     */
    public function test_usuario_pode_editar_pedido_de_viagem(): void
    {
        $travel_request = TravelRequest::factory()->create(['destination' => 'Roma', 'user_id' => $this->userId]);

        $payload = [
            'destination' => 'Orlando',
        ];

        $response = $this->withToken($this->token)->patchJson($this->getUrlWithId($travel_request->id), $payload);

        $response->assertOk()
                 ->assertJsonFragment(['result' => true]);
    }

    /**
     * Testa se um usuário autenticado pode remover um pedido de viagem.
     */
    public function test_usuario_pode_remover_pedido_de_viagem(): void
    {
        $travel_request = TravelRequest::factory()->create(['user_id' => $this->userId]);

        $response = $this->withToken($this->token)->delete($this->getUrlWithId($travel_request->id));

        $response->assertNoContent();
    }

    /**
     * Testa se um admin pode aprovar um pedido de viagem.
     */
    public function test_admin_pode_aprovar_pedido_de_viagem(): void
    {
        $travel_request = TravelRequest::factory()->create(['status' => 'S', 'user_id' => $this->userId]);

        $admin = User::factory()->create(['admin' => true]);

        $token = auth('api')->login($admin);
        $response = $this->withToken($token)->post($this->getUrlWithId($travel_request->id) . '/approve');

        $response->assertOk();
    }

    /**
     * Testa se um admin pode cancelar um pedido de viagem.
     */
    public function test_admin_pode_cancelar_pedido_de_viagem(): void
    {
        $travel_request = TravelRequest::factory()->create(['status' => 'S', 'user_id' => $this->userId]);

        $admin = User::factory()->create(['admin' => true]);

        $token = auth('api')->login($admin);
        $response = $this->withToken($token)->post($this->getUrlWithId($travel_request->id) . '/cancel');

        $response->assertOk();
    }

    /**
     * Testa se um admin não pode cancelar um pedido de viagem aprovado.
     */
    public function test_admin_nao_pode_cancelar_pedido_de_viagem_aprovado(): void
    {
        $travel_request = TravelRequest::factory()->create(['status' => 'A', 'user_id' => $this->userId]);

        $admin = User::factory()->create(['admin' => true]);

        $token = auth('api')->login($admin);
        $response = $this->withToken($token)->post($this->getUrlWithId($travel_request->id) . '/cancel');

        $response->assertUnprocessable()
                 ->assertJsonFragment(['message' => 'O pedido de viagem não pode ser cancelado porque já foi aprovado.']);
    }

    /**
     * Testa se um usuário autenticado pode listar os próprios pedidos de viagem.
     */
    public function test_usuario_pode_listar_pedidos_de_viagem(): void
    {
        TravelRequest::factory()->count(2)->create(['user_id' => $this->userId]);

        $response = $this->withToken($this->token)->getJson($this->getUrl());

        $response->assertOk()
                 ->assertJsonCount(2, 'data');
    }

    /**
     * Testa se um usuário comum vê apenas seus próprios pedidos.
     */
    public function test_usuario_comum_pode_ver_apenas_seus_pedidos_viagem()
    {
        $otherUser = User::factory()->create();

        TravelRequest::factory()->count(2)->create(['user_id' => $this->userId]);
        TravelRequest::factory()->count(3)->create(['user_id' => $otherUser->id]);

        $response = $this->withToken($this->token)->getJson($this->getBaseUrl());

        $response->assertOk();
        $this->assertCount(2, $response->json('data'));
    }

    /**
     * Testa se um usuário admin pode ver todos os pedidos.
     */
    public function test_admin_pode_ver_todos_os_pedidos_viagem()
    {
        $admin = User::factory()->create(['admin' => true]);

        TravelRequest::factory()->count(5)->create();

        $token = auth('api')->login($admin);

        $response = $this->withToken($token)->getJson($this->getBaseUrl());

        $response->assertOk();
        $this->assertCount(5, $response->json('data'));
    }

    /**
     * Testa filtro de pedidos de viagem por status.
     */
    public function test_filtrar_pedidos_de_viagem_por_status()
    {
        TravelRequest::factory()->create(['status' => 'S', 'user_id' => $this->userId]);
        TravelRequest::factory()->create(['status' => 'A', 'user_id' => $this->userId]);

        $response = $this->withToken($this->token)->getJson($this->getUrl('?status=A'));

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
        $this->assertEquals('A', $response->json('data')[0]['status']);
    }

    /**
     * Testa filtro de pedidos de viagem por destino.
     */
    public function test_filtrar_pedidos_de_viagem_por_destino()
    {
        TravelRequest::factory()->create(['destination' => 'São Paulo', 'user_id' => $this->userId]);
        TravelRequest::factory()->create(['destination' => 'Rio de Janeiro', 'user_id' => $this->userId]);

        $response = $this->withToken($this->token)->getJson($this->getUrl('?destination=paulo'));

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
        $this->assertStringContainsString('Paulo', $response->json('data')[0]['destination']);
    }

    /**
     * Testa filtro de pedidos de viagem por data de criação do pedido.
     */
    public function test_filtrar_pedidos_de_viagem_por_data_criacao()
    {
        Carbon::setTestNow('2025-07-01');
        TravelRequest::factory()->create(['created_at' => now(), 'user_id' => $this->userId]);

        Carbon::setTestNow('2025-07-10');
        TravelRequest::factory()->create(['created_at' => now(), 'user_id' => $this->userId]);

        Carbon::setTestNow();

        $filters = [
            'created_at_operator' => 'equal',
            'created_at'          => '2025-07-01',
        ];

        $query_params = '?' . http_build_query($filters);

        $response = $this->withToken($this->token)->getJson($this->getUrl($query_params));

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
    }

    /**
     * Testa filtro de pedidos de viagem por data de ida.
     */
    public function test_filtrar_pedidos_de_viagem_por_data_ida()
    {
        TravelRequest::factory()->create(['departure_date' => '2025-08-01', 'user_id' => $this->userId]);
        TravelRequest::factory()->create(['departure_date' => '2025-09-01', 'user_id' => $this->userId]);

        $filters = [
            'departure_date_operator' => 'equal',
            'departure_date'          => '2025-09-01',
        ];

        $query_params = '?' . http_build_query($filters);

        $response = $this->withToken($this->token)->getJson($this->getUrl($query_params));

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
    }

    /**
     * Testa filtro de pedidos de viagem por data de volta.
     */
    public function test_filtrar_pedidos_de_viagem_por_data_volta()
    {
        TravelRequest::factory()->create(['return_date' => '2025-08-01', 'user_id' => $this->userId]);
        TravelRequest::factory()->create(['return_date' => '2025-09-01', 'user_id' => $this->userId]);

        $filters = [
            'return_date_operator' => 'equal',
            'return_date'          => '2025-08-01',
        ];

        $query_params = '?' . http_build_query($filters);

        $response = $this->withToken($this->token)->getJson($this->getUrl($query_params));

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
    }

    /**
     * Testa filtro de pedidos de viagem por intervalo de data de criação do pedido.
     */
    public function test_filtrar_pedidos_de_viagem_por_intervalo_data_criacao()
    {
        Carbon::setTestNow('2025-07-01');
        TravelRequest::factory()->create(['created_at' => now(), 'user_id' => $this->userId]);

        Carbon::setTestNow('2025-07-10');
        TravelRequest::factory()->create(['created_at' => now(), 'user_id' => $this->userId]);

        Carbon::setTestNow('2025-07-20');
        TravelRequest::factory()->create(['created_at' => now(), 'user_id' => $this->userId]);

        Carbon::setTestNow();

        $filters = [
            'created_at_operator' => 'between',
            'created_at_start'    => '2025-07-05',
            'created_at_end'      => '2025-07-15',
        ];

        $query_params = '?' . http_build_query($filters);

        $response = $this->withToken($this->token)->getJson($this->getUrl($query_params));

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
    }

    /**
     * Testa filtro de pedidos de viagem por intervalo de data de ida.
     */
    public function test_filtrar_pedidos_de_viagem_por_intervalo_data_ida(): void
    {
        TravelRequest::factory()->create(['departure_date' => '2025-08-01', 'user_id' => $this->userId]);
        TravelRequest::factory()->create(['departure_date' => '2025-08-15', 'user_id' => $this->userId]);
        TravelRequest::factory()->create(['departure_date' => '2025-09-01', 'user_id' => $this->userId]);

        $filters = [
            'departure_date_operator' => 'between',
            'departure_date_start'    => '2025-08-01',
            'departure_date_end'      => '2025-08-31',
        ];

        $query_params = '?' . http_build_query($filters);

        $response = $this->withToken($this->token)->getJson($this->getUrl($query_params));

        $response->assertOk();
        $this->assertCount(2, $response->json('data'));
    }

    /**
     * Testa filtro de pedidos de viagem por intervalo de data de volta.
     */
    public function test_filtrar_pedidos_de_viagem_por_intervalo_data_volta(): void
    {
        TravelRequest::factory()->create(['return_date' => '2025-08-01', 'user_id' => $this->userId]);
        TravelRequest::factory()->create(['return_date' => '2025-08-15', 'user_id' => $this->userId]);
        TravelRequest::factory()->create(['return_date' => '2025-09-01', 'user_id' => $this->userId]);

        $filters = [
            'return_date_operator' => 'between',
            'return_date_start'    => '2025-08-10',
            'return_date_end'      => '2025-08-31',
        ];

        $query_params = '?' . http_build_query($filters);

        $response = $this->withToken($this->token)->getJson($this->getUrl($query_params));

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
    }
}
