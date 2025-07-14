<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Classe responsável por validar os parâmetros de filtro para a listagem de pedidos de viagem.
 *
 * Esta classe é utilizada para garantir que os filtros aplicados na busca por pedidos de viagem
 * estejam corretos, validando campos como status, destino, data de ida e data de volta.
 *
 * Ideal para uso em requisições do tipo GET, especialmente quando há necessidade
 * de paginação, busca ou filtros avançados.
 *
 * Exemplo de uso:
 * GET /api/v1/travel-requests?status=S&destination=Paris&departure_date=2025-08-01
 */
class TravelRequestFilterRequest extends FormRequest
{
    /**
     * Determina se o usuário está autorizado a fazer esta request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Regras de validação para filtros da listagem de pedidos de viagem.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status'                  => 'nullable|in:S,A,C',
            'destination'             => 'nullable|string|max:255',
            'departure_date_operator' => 'nullable|in:equal,between,from,until',
            'departure_date'          => 'required_if:departure_date_operator,equal|date',
            'departure_date_start'    => 'required_if:departure_date_operator,between,from|date',
            'departure_date_end'      => 'required_if:departure_date_operator,between,until|date',
            'return_date_operator'    => 'nullable|in:equal,between,from,until',
            'return_date'             => 'required_if:return_date_operator,equal|date',
            'return_date_start'       => 'required_if:return_date_operator,between,from|date',
            'return_date_end'         => 'required_if:return_date_operator,between,until|date',
            'created_at_operator'     => 'nullable|in:equal,between,from,until',
            'created_at'              => 'required_if:created_at_operator,equal|date',
            'created_at_start'        => 'required_if:created_at_operator,between,from|date',
            'created_at_end'          => 'required_if:created_at_operator,between,until|date',
        ];
    }

    /**
     * Mensagens personalizadas para erros de validação.
     * 
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'status.in'                         => 'O status deve ser S (Solicitado), A (Aprovado) ou C (Cancelado).',
            'destination.string'                => 'O destino deve ser um texto.',
            'destination.max'                   => 'O destino não pode ter mais que 255 caracteres.',
            'departure_date_operator.in'        => 'Operador de data de ida inválido.',
            'departure_date.required_if'        => 'Informe a data de ida quando o operador for "equal".',
            'departure_date_start.required_if'  => 'Informe a data de início da ida.',
            'departure_date_end.required_if'    => 'Informe a data final da ida.',
            'return_date_operator.in'           => 'Operador de data de volta inválido.',
            'return_date.required_if'           => 'Informe a data de volta quando o operador for "equal".',
            'return_date_start.required_if'     => 'Informe a data de início da volta.',
            'return_date_end.required_if'       => 'Informe a data final da volta.',
            'created_at_operator.in'            => 'Operador de data de criação inválido.',
            'created_at.required_if'            => 'Informe a data de criação quando o operador for "equal".',
            'created_at_start.required_if'      => 'Informe a data de início da criação.',
            'created_at_end.required_if'        => 'Informe a data final da criação.',
        ];
    }
}
