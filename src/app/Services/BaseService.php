<?php

namespace App\Services;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

abstract class BaseService
{
    /**
     * Repositório base associado a este serviço
     *
     * @var BaseRepository
     */
    public $repository;

    /**
     * Injeta o repositório a ser utilizado pelo serviço
     *
     * @param BaseRepository $repository
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
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
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

}