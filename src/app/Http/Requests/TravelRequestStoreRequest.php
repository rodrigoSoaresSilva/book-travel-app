<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TravelRequestStoreRequest extends FormRequest
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
     * Regras de validação para criação de pedido de viagem.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'destination'     => 'required|string|max:255',
            'departure_date'  => 'required|date|after_or_equal:today',
            'return_date'     => 'required|date|after_or_equal:departure_date',
        ];
    }

    /**
     * Mensagens personalizadas de erro de validação.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'destination.required'          => 'O destino é obrigatório.',
            'departure_date.required'       => 'A data de ida é obrigatória.',
            'departure_date.after_or_equal' => 'A data de ida deve ser igual ou posterior a hoje.',
            'return_date.required'          => 'A data de volta é obrigatória.',
            'return_date.after_or_equal'    => 'A data de volta deve ser igual ou posterior à data de ida.',
        ];
    }
}