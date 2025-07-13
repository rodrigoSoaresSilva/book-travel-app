<?php

namespace App\Services;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Classe abstrata BaseService
 *
 * Fornece funcionalidades genéricas de serviço base como listagem, paginação, etc.
 * Classes que estendem essa base devem injetar um repositório específico.
 */
abstract class BaseService
{
    /**
     * Instância do repositório base que será utilizado pelo serviço.
     *
     * @var BaseRepository
     */
    public $repository;

    /**
     * Construtor do serviço base.
     *
     * Injeta o repositório que será utilizado pelas operações do serviço.
     *
     * @param BaseRepository $repository Repositório a ser injetado
     */
    public function __construct(BaseRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Retorna todos os registros do repositório sem paginação
     *
     * @return Collection
     */
    public function getAll(): Collection
    {
        return $this->repository->getAll();
    }

    /**
     * Retorna resultados paginados a partir de uma query e do request
     *
     * @param Builder $query Query base do Eloquent
     * @param Request $request Request com possíveis parâmetros como `per_page`
     * 
     * @return LengthAwarePaginator
     */
    public function getPaginated(Builder $query, Request $request): LengthAwarePaginator
    {
        $perPage = $request->input('per_page', 10);
        return $query->paginate($perPage);
    }

    /**
     * Retorna uma nova instância de query para o model do repositório
     *
     * @return Builder
     */
    public function getNewQuery(): Builder
    {
        return $this->repository->model->newQuery();
    }

    /**
     * Cria um novo registro no repositório.
     *
     * @param array $data Dados a serem inseridos.
     * @return Model
     */
    public function create(array $data): Model
    {
        return $this->repository->create($data);
    }

    /**
     * Atualiza um registro existente.
     *
     * @param int $id ID do registro.
     * @param array $data Dados para atualização.
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        return $this->repository->update($id, $data);
    }

    /**
     * Remove um registro com base no ID.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        return $this->repository->remove($id);
    }

    /**
     * Retorna um registro específico por ID.
     *
     * @param int $id
     * @return Model|null
     */
    public function findById(int $id): ?Model
    {
        return $this->repository->getById($id);
    }
}