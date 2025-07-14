<?php

namespace App\Policies;

use App\Models\TravelRequest;
use App\Models\User;

/**
 * Define as regras de autorização para operações relacionadas ao modelo TravelRequest.
 */
class TravelRequestPolicy
{
    /**
     * Determina se o usuário pode visualizar um pedido de viagem específico.
     *
     * @param User $user Usuário autenticado.
     * @param TravelRequest $travelRequest Pedido de viagem.
     * @return bool Verdadeiro se o usuário for o dono ou um administrador.
     */
    public function view(User $user, TravelRequest $travelRequest): bool
    {
        return $user->id === $travelRequest->user_id || $user->is_admin;
    }

    /**
     * Determina se o usuário pode atualizar um pedido de viagem.
     *
     * @param User $user Usuário autenticado.
     * @param TravelRequest $travelRequest Pedido de viagem.
     * @return bool Verdadeiro apenas se o usuário for o dono.
     */
    public function update(User $user, TravelRequest $travelRequest): bool
    {
        return $user->id === $travelRequest->user_id;
    }

    /**
     * Determina se o usuário pode excluir um pedido de viagem.
     *
     * @param User $user Usuário autenticado.
     * @param TravelRequest $travelRequest Pedido de viagem.
     * @return bool Verdadeiro apenas se o usuário for o dono.
     */
    public function delete(User $user, TravelRequest $travelRequest): bool
    {
        return $user->id === $travelRequest->user_id;
    }

    /**
     * Determina se o usuário pode aprovar um pedido de viagem.
     *
     * @param User $user Usuário autenticado.
     * @return bool Verdadeiro se o usuário for administrador.
     */
    public function approve(User $user): bool
    {
        return $user->is_admin;
    }

    /**
     * Determina se o usuário pode cancelar um pedido de viagem.
     * Pode ser o próprio solicitante ou um administrador.
     *
     * @param User $user Usuário autenticado.
     * @return bool Verdadeiro se for administrador.
     */
    public function cancel(User $user): bool
    {
        return $user->is_admin;
    }
}