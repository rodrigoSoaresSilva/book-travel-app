<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TravelRequestFilterRequest;
use App\Http\Requests\TravelRequestStoreRequest;
use App\Http\Requests\TravelRequestUpdateRequest;
use App\Services\TravelRequestService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

/**
 * Controller responsável por lidar com as requisições relacionadas a pedidos de viagem.
 */
class TravelRequestController extends Controller
{
    use AuthorizesRequests;

    private $notFoundMessage = 'Pedido de viagem não encontrado.';

    /**
     * Instância do serviço responsável pela lógica de negócio de pedidos de viagem.
     *
     * @var TravelRequestService
     */
    private $service;

    /**
     * Construtor do controlador.
     *
     * @param TravelRequestService $service Instância do serviço de pedidos de viagem injetada automaticamente pelo container do Laravel.
     */
    public function __construct(TravelRequestService $service)
    {
        $this->service = $service;
    }

    /**
     * Retorna uma listagem de pedidos de viagem com base nos filtros fornecidos na requisição.
     *
     * @param TravelRequestFilterRequest $request Objeto de request validado com possíveis filtros (status, datas, destino, etc.).
     * @return JsonResponse Retorna os pedidos de viagem filtrados em formato JSON.
     */
    public function index(TravelRequestFilterRequest $request): JsonResponse
    {
        return $this->service->filterQuery($request);
    }

    /**
     * Armazena um novo pedido de viagem.
     *
     * @param TravelRequestStoreRequest $request Dados validados da requisição.
     * @return JsonResponse Pedido de viagem recém-criado.
     * 
     * @throws AuthorizationException Se o usuário não estiver autorizado.
     */
    public function store(TravelRequestStoreRequest $request): JsonResponse
    {
        $travelRequest = $this->service->create(array_merge(
            $request->validated(),
            ['user_id' => auth()->id()]
        ));

        return response()->json($travelRequest, Response::HTTP_CREATED);
    }

    /**
     * Exibe os detalhes de um pedido de viagem específico.
     *
     * @param int $id ID do pedido de viagem.
     * @return JsonResponse Dados do pedido ou mensagem de erro.
     * 
     * @throws AuthorizationException Se o usuário não estiver autorizado.
     */
    public function show(int $id): JsonResponse
    {
        $travelRequest = $this->service->findById($id);

        if (! $travelRequest) {
            return response()->json(['message' => $this->notFoundMessage], Response::HTTP_NOT_FOUND);
        }

        $this->authorize('view', $travelRequest);

        return response()->json($travelRequest, Response::HTTP_OK);
    }

    /**
     * Atualiza um pedido de viagem existente.
     *
     * @param TravelRequestUpdateRequest $request Dados validados da requisição.
     * @param int $id ID do pedido de viagem a ser atualizado.
     * @return JsonResponse Mensagem de sucesso ou erro.
     * 
     * @throws AuthorizationException Se o usuário não estiver autorizado.
     */
    public function update(TravelRequestUpdateRequest $request, int $id): JsonResponse
    {
        $travelRequest = $this->service->findById($id);

        if (! $travelRequest) {
            return response()->json(['message' => $this->notFoundMessage], Response::HTTP_NOT_FOUND);
        }

        $this->authorize('update', $travelRequest);

        $updated = $this->service->update($id, $request->validated());

        if (! $updated) {
            return response()->json(['message' => 'Falha ao atualizar pedido de viagem'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(['message' => 'Pedido de viagem atualizado com sucesso', 'result' => $travelRequest->fresh()], Response::HTTP_OK);
    }

    /**
     * Remove um pedido de viagem existente.
     *
     * @param int $id ID do pedido de viagem.
     * @return JsonResponse Resposta com status HTTP apropriado.
     * 
     * @throws AuthorizationException Se o usuário não estiver autorizado.
     */
    public function destroy(int $id): JsonResponse
    {
        $travelRequest = $this->service->findById($id);

        if (! $travelRequest) {
            return response()->json(['message' => $this->notFoundMessage], Response::HTTP_NOT_FOUND);
        }

        $this->authorize('delete', $travelRequest);

        $deleted = $this->service->delete($id);

        if (! $deleted) {
            return response()->json(['message' => 'Falha ao remover pedido de viagem'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Aprova um pedido de viagem. Apenas administradores têm permissão.
     *
     * @param int $id ID do pedido de viagem.
     * @return JsonResponse Pedido atualizado.
     * 
     * @throws AuthorizationException Se o usuário não estiver autorizado.
     */
    public function approve(int $id): JsonResponse
    {
        $travelRequest = $this->service->findById($id);

        if (! $travelRequest) {
            return response()->json(['message' => $this->notFoundMessage], Response::HTTP_NOT_FOUND);
        }

        $this->authorize('approve', $travelRequest);

        return $this->service->approveTravelRequest($travelRequest);
    }

    /**
     * Cancela um pedido de viagem. Pode ser cancelado pelo próprio usuário ou por um administrador.
     *
     * @param int $id ID do pedido de viagem.
     * @return JsonResponse Pedido atualizado.
     * 
     * @throws AuthorizationException Se o usuário não estiver autorizado.
     */
    public function cancel(int $id): JsonResponse
    {
        $travelRequest = $this->service->findById($id);

        if (! $travelRequest) {
            return response()->json(['message' => $this->notFoundMessage], Response::HTTP_NOT_FOUND);
        }

        $this->authorize('cancel', $travelRequest);

        return $this->service->cancelTravelRequest($travelRequest);
    }
}
