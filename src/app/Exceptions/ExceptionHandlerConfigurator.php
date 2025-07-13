<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Classe responsável por configurar os tratadores de exceções personalizados da aplicação.
 */
class ExceptionHandlerConfigurator
{
    /**
     * Registra os tratadores personalizados de exceções.
     *
     * @param Exceptions $exceptions Instância do gerenciador de exceções do Laravel.
     * @return void
     */
    public static function configure(Exceptions $exceptions): void
    {
        /**
         * Trata exceções de autorização (usuário autenticado, mas sem permissão).
         */
        $exceptions->render(function (AccessDeniedHttpException $e): JsonResponse {
            return response()->json(['message' => 'Ação não autorizada.'], Response::HTTP_FORBIDDEN);
        });

        /**
         * Trata exceções de autenticação (usuário não autenticado).
         */
        $exceptions->render(function (AuthenticationException $e): JsonResponse {
            return response()->json(['message' => 'Não autenticado.'], Response::HTTP_UNAUTHORIZED);
        });

        /**
         * Trata exceções de autenticação (token não pôde ser verificado).
         */
        $exceptions->render(function (UnauthorizedHttpException $e): JsonResponse {
            return response()->json(['message' => 'A assinatura do Token não pôde ser verificada.'], Response::HTTP_UNAUTHORIZED);
        });

        /**
         * Trata exceções de validação de dados.
         */
        $exceptions->render(function (ValidationException $e): JsonResponse {
            return response()->json([
                'message' => 'Erro de validação.',
                'errors' => $e->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        });

        /**
         * Trata exceções de modelo não encontrado no banco de dados.
         */
        $exceptions->render(function (ModelNotFoundException $e): JsonResponse {
            return response()->json(['message' => 'Recurso não encontrado.'], Response::HTTP_NOT_FOUND);
        });

        /**
         * Trata exceções de métodos HTTP não implementados.
         */
        $exceptions->render(function (MethodNotAllowedHttpException $e): JsonResponse {
            return response()->json([
                'message' => 'Método HTTP não suportado.', 'errors' => $e->getMessage(),
            ], Response::HTTP_METHOD_NOT_ALLOWED);
        });
    }
}
