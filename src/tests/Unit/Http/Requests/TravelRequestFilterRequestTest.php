<?php

namespace Tests\Unit\Http\Requests;

use App\Http\Requests\TravelRequestFilterRequest;
use Illuminate\Support\Facades\Validator;
use Tests\Abstracts\Http\Requests\AbstractFormRequestTest;

/**
 * Testes unitários para validação da classe TravelRequestFilterRequest.
 *
 * Este teste verifica as regras de validação sem depender de requisições HTTP,
 * utilizando diretamente o validador do Laravel.
 */
class TravelRequestFilterRequestTest extends AbstractFormRequestTest
{
    /**
     * Setup executado antes de cada teste.
     * Define qual request será testada.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->setFormRequest(new TravelRequestFilterRequest());
    }

    /**
     * Testa se dados válidos com operador 'equal' para data de ida são aceitos.
     */
    public function test_valida_dados_corretos_com_departure_date_equal(): void
    {
        $data = [
            'departure_date_operator' => 'equal',
            'departure_date' => '2025-08-01',
        ];

        $this->assertValid($data);
    }

    /**
     * Testa falha de validação quando operador 'equal' é informado sem a data de ida.
     */
    public function test_falha_se_departure_date_nao_estiver_presente_com_operador_equal(): void
    {
        $data = [
            'departure_date_operator' => 'equal',
            // 'departure_date' ausente
        ];

        $this->assertInvalid($data, ['departure_date']);
    }

    /**
     * Testa se intervalo de datas de ida com operador 'between' é aceito.
     */
    public function test_valida_departure_date_between_com_datas_inicio_e_fim(): void
    {
        $data = [
            'departure_date_operator' => 'between',
            'departure_date_start' => '2025-08-01',
            'departure_date_end' => '2025-08-31',
        ];

        $this->assertValid($data);
    }

    /**
     * Testa falha de validação ao omitir uma das datas do intervalo.
     */
    public function test_falha_departure_date_between_se_faltar_data_inicio_ou_fim(): void
    {
        $data = [
            'departure_date_operator' => 'between',
            'departure_date_start' => '2025-08-01',
            // 'departure_date_end' ausente
        ];

        $this->assertInvalid($data, ['departure_date_end']);
    }

    /**
     * Testa se status inválido gera erro de validação.
     */
    public function test_falha_status_invalido(): void
    {
        $data = [
            'status' => 'X', // inválido
        ];

        $this->assertInvalid($data, ['status']);
    }

        /**
     * Testa que um destino válido (texto com menos de 255 caracteres) é aceito pela validação.
     */
    public function test_valida_destino_valido(): void
    {
        $this->assertValid(['destination' => 'Lisboa']);
    }

    /**
     * Testa que um destino com mais de 255 caracteres é rejeitado pela validação.
     */
    public function test_valida_destino_invalido(): void
    {
        $this->assertInvalid(['destination' => str_repeat('X', 256)], ['destination']);
    }

    /**
     * Testa que a combinação de operador "equal" e campo created_at com uma data válida passa na validação.
     */
    public function test_valida_created_at_equal_valido(): void
    {
        $this->assertValid([
            'created_at_operator' => 'equal',
            'created_at' => '2025-07-01',
        ]);
    }

    /**
     * Testa que a ausência do campo created_at quando o operador é "equal" gera erro de validação.
     */
    public function test_valida_created_at_equal_invalido(): void
    {
        $this->assertInvalid([
            'created_at_operator' => 'equal',
        ], ['created_at']);
    }

    /**
     * Testa que o operador "between" para return_date com início e fim válidos passa na validação.
     */
    public function test_valida_return_date_between_valido(): void
    {
        $this->assertValid([
            'return_date_operator' => 'between',
            'return_date_start' => '2025-07-10',
            'return_date_end' => '2025-07-20',
        ]);
    }

    /**
     * Testa que ao usar "between" sem o campo return_date_end, a validação falha.
     */
    public function test_valida_return_date_between_invalido(): void
    {
        $this->assertInvalid([
            'return_date_operator' => 'between',
            'return_date_start' => '2025-07-10',
        ], ['return_date_end']);
    }

    /**
     * Testa que o operador "equal" com uma data válida para departure_date passa na validação.
     */
    public function test_valida_departure_date_equal_valido(): void
    {
        $this->assertValid([
            'departure_date_operator' => 'equal',
            'departure_date' => '2025-08-01',
        ]);
    }

    /**
     * Testa que ao usar "equal" sem o campo departure_date, a validação falha.
     */
    public function test_valida_departure_date_equal_invalido(): void
    {
        $this->assertInvalid([
            'departure_date_operator' => 'equal',
        ], ['departure_date']);
    }
}
