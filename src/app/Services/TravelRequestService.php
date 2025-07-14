<?php

namespace App\Services;

use App\Interfaces\Repository\TravelRequestRepositoryInterface;
use App\Models\TravelRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Carbon\Carbon;

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
     * Aprova um pedido de viagem. Apenas administradores têm permissão.
     *
     * @param TravelRequest $travelRequest Pedido de viagem a ser aprovado.
     * @return JsonResponse Pedido atualizado em JSON.
     *
     * @throws AuthorizationException Se o usuário não estiver autorizado.
     */
    public function approveTravelRequest(TravelRequest $travelRequest): JsonResponse
    {
        if ($travelRequest->status === TravelRequest::STATUS_CANCELADO) {
            return response()->json([
                'message' => 'O pedido de viagem não pode ser aprovado porque já foi cancelado.'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if ($travelRequest->status === TravelRequest::STATUS_APROVADO) {
            return response()->json([
                'message' => 'O pedido de viagem já foi aprovado.'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->repository->update(
            $travelRequest->id,
            [
            'status' => TravelRequest::STATUS_APROVADO,
            'approved_by' => auth()->id(),
            ]
        );

        return response()->json(['message' => 'Pedido de viagem aprovado com sucesso!'], Response::HTTP_OK);
    }

    /**
     * Cancela um pedido de viagem. Pode ser cancelado pelo próprio usuário ou por um administrador.
     *
     * @param TravelRequest $travelRequest Pedido de viagem a ser cancelado.
     * @return JsonResponse Pedido atualizado em JSON.
     *
     * @throws AuthorizationException Se o usuário não estiver autorizado.
     */
    public function cancelTravelRequest(TravelRequest $travelRequest): JsonResponse
    {
        if ($travelRequest->status === TravelRequest::STATUS_APROVADO) {
            return response()->json([
                'message' => 'O pedido de viagem não pode ser cancelado porque já foi aprovado.'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if ($travelRequest->status === TravelRequest::STATUS_CANCELADO) {
            return response()->json([
                'message' => 'O pedido de viagem já foi cancelado.'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $travelRequest = $this->repository->update(
            $travelRequest->id,
            [
            'status' => TravelRequest::STATUS_CANCELADO,
            'canceled_by' => auth()->id(),
            ]
        );

        return response()->json(['message' => 'Pedido de viagem cancelado com sucesso!'], Response::HTTP_OK);
    }

    /**
     * Aplica filtros à consulta de pedidos de viagem com base nos parâmetros da requisição.
     *
     * Se o usuário autenticado não for administrador, retorna apenas seus próprios pedidos.
     * Permite filtrar por status, período de ida, período de volta e destino.
     *
     * @param Request $request Requisição HTTP contendo os filtros opcionais.
     * @return JsonResponse Lista paginada dos pedidos filtrados.
     */
    public function filterQuery(Request $request): JsonResponse
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

        // Aplica filtros por data de criação
        if ($request->filled('created_at_operator')) {
            $query = $this->setFilterByDate($request, 'created_at', $query);
        }

        // Aplica filtros por data de ida
        if ($request->filled('departure_date_operator')) {
            $query = $this->setFilterByDate($request, 'departure_date', $query);
        }

        // Aplica filtros por data de volta
        if ($request->filled('return_date_operator')) {
            $query = $this->setFilterByDate($request, 'return_date', $query);
        }

        // Filtro por destino (busca parcial)
        if ($request->filled('destination')) {
            $query->where('destination', 'like', '%' . $request->destination . '%');
        }

        // Retorna os resultados de forma paginada
        return response()->json(
            $this->getPaginated($query, $request),
            Response::HTTP_OK
        );
    }

    /**
     * Aplica filtros de data personalizados ao campo informado.
     *
     * @param Request $request Objeto da requisição contendo os filtros.
     * @param string $field_name Nome do campo de data a ser filtrado (ex: departure_date).
     * @param Builder $query Instância da query Eloquent.
     * @return Builder Query com os filtros aplicados.
     */
    private function setFilterByDate(Request $request, string $field_name, Builder $query): Builder
    {
        $operator = $request->input("{$field_name}_operator");

        switch ($operator) {
            case 'equal':
                // Filtra por data exata
                if ($request->filled($field_name)) {
                    if($field_name != 'created_at'){
                        $query->where($field_name, '=', $request->input($field_name));
                    } else {
                        $query->whereDate($field_name, '=', Carbon::parse($request->input($field_name))->toDateString());
                    }
                }
                break;

            case 'between':
                // Filtra por intervalo entre duas datas
                $start = $request->input("{$field_name}_start");
                $end   = $request->input("{$field_name}_end");

                if ($start && $end) {
                    $query->whereBetween($field_name, [$start, $end]);
                }
                break;

            case 'from':
                // Filtra registros a partir de uma data
                if ($request->filled("{$field_name}_start")) {
                    $query->where($field_name, '>=', $request->input("{$field_name}_start"));
                }
                break;

            case 'until':
                // Filtra registros até uma data
                if ($request->filled("{$field_name}_end")) {
                    $query->where($field_name, '<=', $request->input("{$field_name}_end"));
                }
                break;
        }

        return $query;
    }
}
