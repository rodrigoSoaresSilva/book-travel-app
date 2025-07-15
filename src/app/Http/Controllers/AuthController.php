<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * Controller responsável por lidar com as requisições relacionadas a autenticação.
 */
class AuthController extends Controller
{
    /**
     * Realiza o login do usuário e retorna um token JWT.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        $credentials = $request->only('email', 'password');

        if (! $token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Não autorizado'], 401);
        }

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }

    /**
     * Retorna os dados do usuário autenticado.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Gera um novo token JWT para o usuário autenticado.
     *
     * Este método é utilizado para renovar o token de autenticação
     * antes que ele expire, mantendo a sessão ativa sem exigir novo login.
     *
     * @return \Illuminate\Http\JsonResponse Retorna um novo token JWT.
     */
    public function refresh()
    {
        $token = auth('api')->refresh();
        return response()->json(['token' => $token]);
    }

    /**
     * Realiza o logout do usuário invalidando o token atual.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth('api')->logout();
        return response()->json(['message' => 'Desconectado com sucesso!']);
    }
}
