<?php

namespace Tests\Abstracts\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

/**
 * Classe abstrata base para testes unitários de validação de regras em classes do tipo FormRequest.
 * Permite reutilização para diferentes requests (ex: Store, Update, Filter).
 */
abstract class AbstractFormRequestTest extends TestCase
{
    /**
     * Instância da classe de request a ser testada.
     *
     * @var FormRequest
     */
    protected FormRequest $formRequest;

    /**
     * Define a instância da request que será usada nos testes.
     *
     * @param FormRequest $request
     */
    protected function setFormRequest(FormRequest $request): void
    {
        $this->formRequest = $request;
    }

    /**
     * Retorna as regras de validação da request.
     */
    protected function getRules(): array
    {
        return $this->formRequest->rules();
    }

    /**
     * Retorna as mensagens customizadas da request.
     */
    protected function getMessages(): array
    {
        return method_exists($this->formRequest, 'messages')
            ? $this->formRequest->messages()
            : [];
    }

    /**
     * Executa a validação com os dados fornecidos.
     *
     * @param array $data
     * @return Illuminate\Contracts\Validation\Validator
     */
    protected function validate(array $data): \Illuminate\Validation\Validator
    {
        return Validator::make($data, $this->getRules(), $this->getMessages());
    }

    /**
     * Valida se os dados fornecidos são válidos.
     *
     * @param array $data
     */
    protected function assertValid(array $data): void
    {
        $validator = $this->validate($data);
        $this->assertTrue(
            $validator->passes(),
            'Esperado que os dados fossem válidos, mas falharam: ' . json_encode($validator->errors()->toArray())
        );
    }

    /**
     * Valida se os dados fornecidos são inválidos e verifica os campos com erro.
     *
     * @param array $data
     * @param array $expectedInvalidFields
     */
    protected function assertInvalid(array $data, array $expectedInvalidFields): void
    {
        $validator = $this->validate($data);
        $this->assertFalse(
            $validator->passes(),
            'Esperado que os dados fossem inválidos, mas passaram.'
        );

        foreach ($expectedInvalidFields as $field) {
            $this->assertArrayHasKey(
                $field,
                $validator->errors()->toArray(),
                "Campo inválido esperado: $field"
            );
        }
    }
}
