<?php

namespace Tests\Abstracts\TravelRequest;

use Tests\TestCase;

/**
 * Classe abstrata base para testes de funcionalidades relacionadas a pedidos de viagem.
 */
abstract class AbstractTravelRequestTest extends TestCase
{
    protected const BASE_ENDPOINT = 'travel-requests';

    /**
     * Retorna a URL base da API.
     */
    protected function getBaseUrl(): string
    {
        return self::getApiV1Url(self::BASE_ENDPOINT);
    }

    /**
     * Retorna a URL base com ID.
     *
     * @param $id ID do registro.
     * @return string
     */
    protected function getUrlWithId(int $id): string
    {
        return $this->getBaseUrl() . "/$id";
    }

    /**
     * Retorna a URL completa do endpoint testado.
     *
     * @param string $url Complemento opcional da URL.
     * @return string URL
     */
    protected function getUrl(string $url = ''): string
    {
        return self::getBaseUrl() . '/' . ltrim($url, '/');
    }
}
