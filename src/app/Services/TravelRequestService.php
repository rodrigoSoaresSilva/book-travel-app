<?php

namespace App\Services;

use App\Interfaces\Repository\TravelRequestRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

/**
 * Serviço responsável por encapsular a lógica de negócio relacionada aos pedidos de viagem.
 * 
 * Este serviço fornece filtros dinâmicos para consulta, respeitando o perfil do usuário (admin ou não).
 */
class TravelRequestService extends BaseService
{
    /**
     * Construtor da classe.
     *
     * Injeta a interface do repositório de pedidos de viagem no serviço base.
     *
     * @param TravelRequestRepositoryInterface $repository Repositório responsável pelas operações com a entidade TravelRequest.
     */
    public function __construct(TravelRequestRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    /**
     * Aplica filtros à consulta de pedidos de viagem com base nos parâmetros da requisição.
     *
     * Se o usuário autenticado não for administrador, retorna apenas seus próprios pedidos.
     * Permite filtrar por status, período de ida, período de volta e destino.
     *
     * @param Request $request Requisição HTTP contendo os filtros opcionais.
     * @return LengthAwarePaginator Lista paginada dos pedidos filtrados.
     */
    public function filterQuery(Request $request): LengthAwarePaginator
    {
        // Inicia uma nova consulta baseada no model
        $query = $this->getNewQuery();

        // Filtra para exibir apenas os pedidos do próprio usuário, caso não seja admin
        if (!auth()->user()->is_admin) {
            $query->where('user_id', auth()->id());
        }

        // Filtro por status (S, A, C)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filtro por data de ida (intervalo)
        if ($request->filled('departure_start')) {
            $query->where('departure_date', '>=', $request->departure_start);
        }
        if ($request->filled('departure_end')) {
            $query->where('departure_date', '<=', $request->departure_end);
        }

        // Filtro por data de volta (intervalo)
        if ($request->filled('return_start')) {
            $query->where('return_date', '>=', $request->return_start);
        }
        if ($request->filled('return_end')) {
            $query->where('return_date', '<=', $request->return_end);
        }

        // Filtro por destino (busca parcial)
        if ($request->filled('destination')) {
            $query->where('destination', 'like', '%' . $request->destination . '%');
        }

        // Retorna os resultados de forma paginada
        return $this->getPaginated($query, $request);
    }
}
