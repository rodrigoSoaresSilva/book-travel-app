<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TravelRequestUpdateRequest extends FormRequest
{
    /**
     * Determina se o usuário está autorizado a fazer esta requisição.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Define as regras de validação aplicáveis à atualização de um pedido de viagem.
     *
     * Os campos são opcionais, pois o usuário pode atualizar apenas alguns deles.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'destination'     => 'sometimes|required|string|max:255',
            'departure_date'  => 'sometimes|required|date|after_or_equal:today',
            'return_date'     => 'sometimes|required|date|after_or_equal:departure_date',
        ];
    }

    /**
     * Mensagens personalizadas para os erros de validação.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'destination.required' => 'O destino é obrigatório.',
            'departure_date.required' => 'A data de ida é obrigatória.',
            'departure_date.after_or_equal' => 'A data de ida deve ser igual ou posterior a hoje.',
            'return_date.required' => 'A data de volta é obrigatória.',
            'return_date.after_or_equal' => 'A data de volta deve ser igual ou posterior à data de ida.',
        ];
    }
}