<?php

namespace Tests\Unit\Http\Requests;

use App\Http\Requests\TravelRequestUpdateRequest;
use Tests\Abstracts\Http\Requests\AbstractFormRequestTest;
use Carbon\Carbon;

/**
 * Testes unitários para validação da request de atualização de pedidos de viagem.
 */
class TravelRequestUpdateRequestTest extends AbstractFormRequestTest
{
    /**
     * Setup executado antes de cada teste.
     * Define qual request será testada e fixa a data atual.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->setFormRequest(new TravelRequestUpdateRequest());
        Carbon::setTestNow(Carbon::parse('2025-08-01'));
    }

    /**
     * Testa se o campo destination, quando enviado, deve ser obrigatório e válido.
     */
    public function test_valida_campos_obrigatorios_se_enviados(): void
    {
        $this->assertInvalid([
            'destination' => '',
        ], ['destination']);

        $this->assertInvalid([
            'departure_date' => '',
        ], ['departure_date']);

        $this->assertInvalid([
            'return_date' => '',
        ], ['return_date']);
    }

    /**
     * Testa que apenas o campo 'destination' válido é aceito.
     */
    public function test_valida_campo_destination(): void
    {
        $this->assertInvalid([
            'destination' => str_repeat('a', 256),
        ], ['destination']);

        $this->assertValid([
            'destination' => 'Lisboa',
        ]);
    }

    /**
     * Testa que apenas o campo 'departure_date' válido é aceito.
     */
    public function test_valida_campo_departure_date(): void
    {
        $this->assertInvalid([
            'departure_date' => '2025-01-31',
        ], ['departure_date']);

        $this->assertValid([
            'departure_date' => now()->toDateString(),
        ]);
    }

    /**
     * Testa que apenas o campo 'return_date' válido é aceito.
     */
    public function test_valida_campo_return_date(): void
    {
        $this->assertInvalid([
            'departure_date' => '2025-08-10',
            'return_date'    => '2025-08-05',
        ], ['return_date']);

        $this->assertValid([
            'departure_date' => now()->toDateString(),
            'return_date' => now()->addDay()->toDateString(),
        ]);
    }

    /**
     * Testa que o campo 'return_date' inválido (anterior à 'departure_date') falha.
     */
    public function test_valida_se_payload_completo_valido(): void
    {
        $this->assertValid([
            'destination'    => 'Florença',
            'departure_date' => '2025-08-10',
            'return_date'    => '2025-08-15',
        ]);
    }
}
