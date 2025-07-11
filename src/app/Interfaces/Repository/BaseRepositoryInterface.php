<?php

namespace App\Interfaces\Repository;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Interface BaseRepositoryInterface
 *
 * Define um contrato genérico para operações básicas de CRUD
 * que qualquer repositório deve implementar.
 */
interface BaseRepositoryInterface
{
    /**
     * Busca um registro pelo ID
     *
     * @param int $id
     * @return Model|null
     */
    public function getById(int $id): ?Model;

    /**
     * Retorna todos os registros
     *
     * @return Collection
     */
    public function getAll(): Collection;

    /**
     * Retorna os registros paginados
     *
     * @param int $perPage Quantidade de itens por página
     * @return LengthAwarePaginator
     */
    public function getPaginated(int $perPage): LengthAwarePaginator;

    /**
     * Cria um novo registro
     *
     * @param array $data Dados para criação
     * @return Model
     */
    public function create(array $data): Model;

    /**
     * Atualiza um registro existente
     *
     * @param int $id ID do registro
     * @param array $data Dados para atualização
     * @return bool
     */
    public function update(int $id, array $data): bool;

    /**
     * Remove um registro pelo ID
     *
     * @param int $id ID do registro
     * @return bool
     */
    public function remove(int $id): bool;
}
