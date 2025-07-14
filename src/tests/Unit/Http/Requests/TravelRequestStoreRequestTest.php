<?php

namespace Tests\Unit\Http\Requests;

use App\Http\Requests\TravelRequestStoreRequest;
use Tests\Abstracts\Http\Requests\AbstractFormRequestTest;
use Carbon\Carbon;

/**
 * Testes unitários para validação da request de criação de pedidos de viagem.
 */
class TravelRequestStoreRequestTest extends AbstractFormRequestTest
{
    /**
     * Setup executado antes de cada teste.
     * Define qual request será testada e fixa a data atual.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->setFormRequest(new TravelRequestStoreRequest());
        Carbon::setTestNow(Carbon::parse('2025-08-01')); // fixando a data atual para testes
    }

    /**
     * Testa se os dados mínimos obrigatórios válidos são aceitos.
     */
    public function test_valida_payload_valido(): void
    {
        $this->assertValid([
            'destination'    => 'Roma',
            'departure_date' => '2025-08-02',
            'return_date'    => '2025-08-10',
        ]);
    }

    /**
     * Testa se a ausência do campo destination falha na validação.
     */
    public function test_valida_destination_required(): void
    {
        $this->assertInvalid([
            'departure_date' => '2025-08-02',
            'return_date'    => '2025-08-10',
        ], ['destination']);
    }

    /**
     * Testa se um destino muito longo (acima de 255 caracteres) falha na validação.
     */
    public function test_valida_destination_longo_demais(): void
    {
        $this->assertInvalid([
            'destination'    => str_repeat('x', 256),
            'departure_date' => '2025-08-02',
            'return_date'    => '2025-08-10',
        ], ['destination']);
    }

    /**
     * Testa se a ausência da data de ida falha na validação.
     */
    public function test_valida_departure_date_required(): void
    {
        $this->assertInvalid([
            'destination' => 'Roma',
            'return_date' => '2025-08-10',
        ], ['departure_date']);
    }

    /**
     * Testa se a data de ida anterior à hoje falha na validação.
     */
    public function test_valida_departure_date_deve_ser_hoje_ou_futuro(): void
    {
        $this->assertInvalid([
            'destination'    => 'Roma',
            'departure_date' => '2025-07-31', // ontem
            'return_date'    => '2025-08-10',
        ], ['departure_date']);
    }

    /**
     * Testa se a ausência da data de volta falha na validação.
     */
    public function test_valida_return_date_required(): void
    {
        $this->assertInvalid([
            'destination'    => 'Roma',
            'departure_date' => '2025-08-02',
        ], ['return_date']);
    }

    /**
     * Testa se a data de volta anterior à data de ida falha na validação.
     */
    public function test_valida_return_date_deve_ser_igual_ou_depois_de_departure_date(): void
    {
        $this->assertInvalid([
            'destination'    => 'Roma',
            'departure_date' => '2025-08-10',
            'return_date'    => '2025-08-05',
        ], ['return_date']);
    }
}
