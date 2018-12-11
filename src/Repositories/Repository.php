<?php

namespace Epsoftware\Laravel\Doctrine\Mongo\Repositories;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Epsoftware\Laravel\Doctrine\Mongo\Pagination\PaginatesFromParams;
use Exception;

class Repository extends DocumentRepository
{
    use PaginatesFromParams;

    /**
     * @param int $limit
     * @param int $page
     *
     * @return LengthAwarePaginator
    */
    public function all(int $limit = 10, int $page = 1): LengthAwarePaginator
    {
        return $this->paginateAll($limit, $page);
    }

    /**
     * @param string $id
     * @throws ModelNotFoundException
     *
     * @return $document
    */
    public function findOrFail(string $id)
    {
        $document = $this->findOneBy(['id' => $id]);

        if (!$document) {
            $exception = (new ModelNotFoundException())->setModel($this->getClassName(), [$id]);
            throw $exception;
        }

        return $document;
    }
}