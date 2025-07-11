<?php

namespace App\Repositories;

use App\Interfaces\Repository\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Classe abstrata BaseRepository
 *
 * Implementa um repositório genérico com operações básicas de CRUD.
 * Todas as classes filhas devem fornecer um model específico via injeção.
 */
abstract class BaseRepository implements BaseRepositoryInterface
{
    /**
     * Instância do model Eloquent
     *
     * @var Model
     */
    public Model $model;

    /**
     * Construtor base com injeção do model
     *
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Retorna um registro pelo ID
     *
     * @param int $id
     * @return Model|null
     */
    public function getById(int $id): ?Model
    {
        return $this->model->find($id);
    }

    /**
     * Cria um novo registro com os dados fornecidos
     *
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * Atualiza um registro existente pelo ID
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        return $this->model->whereId($id)->update($data);
    }

    /**
     * Remove um registro pelo ID
     *
     * @param int $id
     * @return bool
     */
    public function remove(int $id): bool
    {
        return $this->model->whereId($id)->delete();
    }

    /**
     * Retorna todos os registros do model
     *
     * @return Collection
     */
    public function getAll(): Collection
    {
        return $this->model->all();
    }

    /**
     * Retorna os registros paginados
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPaginated(int $perPage): LengthAwarePaginator
    {
        return $this->model->paginate($perPage);
    }
}
