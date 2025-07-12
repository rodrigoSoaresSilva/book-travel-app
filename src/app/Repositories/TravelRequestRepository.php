<?php

namespace App\Repositories;

use App\Models\TravelRequest;
use App\Interfaces\Repository\TravelRequestRepositoryInterface;
use App\Repositories\BaseRepository;

class TravelRequestRepository extends BaseRepository implements TravelRequestRepositoryInterface
{
    /**
     * Injeta o model TravelRequest no repositório base.
     *
     * @param TravelRequest $model
     */
    public function __construct(TravelRequest $model)
    {
        parent::__construct($model);
    }
}
