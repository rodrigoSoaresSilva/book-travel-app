<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

/**
 * Classe base para todos os testes.
 *
 * Fornece constantes e métodos auxiliares para facilitar
 * a construção de URLs da API durante os testes.
 */
abstract class TestCase extends BaseTestCase
{
    /**
     * Prefixo base para rotas da API v1.
     */
    protected const API_V1 = '/api/v1/';

    /**
     * Prefixo base para rotas de autenticação.
     */
    protected const API_AUTH = '/api/auth/';

    /**
     * Retorna a URL completa para rotas da API v1.
     *
     * @param string $url  Caminho adicional após o prefixo da API.
     *                     Exemplo: 'travel-requests' resultará em '/api/v1/travel-requests'.
     * @return string
     */
    protected static function getApiV1Url(string $url = ''): string
    {
        return self::API_V1 . ltrim($url, '/');
    }

    /**
     * Retorna a URL completa para rotas de autenticação.
     *
     * @param string $url  Caminho adicional após o prefixo de autenticação.
     *                     Exemplo: 'login' resultará em '/api/auth/login'.
     * @return string
     */
    protected static function getAuthUrl(string $url = ''): string
    {
        return self::API_AUTH . ltrim($url, '/');
    }
}
