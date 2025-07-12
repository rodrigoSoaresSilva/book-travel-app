<?php

namespace App\Services;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
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

}