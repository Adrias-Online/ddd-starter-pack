<?php

declare(strict_types=1);

namespace DDDStarterPack\Infrastructure\Domain\Aggregate\Doctrine\Repository;

use DDDStarterPack\Domain\Aggregate\Repository\Filter\FilterParams;
use DDDStarterPack\Domain\Aggregate\Repository\Paginator\Paginator;
use Doctrine\ORM\QueryBuilder;
use Webmozart\Assert\Assert;

abstract class DoctrineFilterParamsRepository extends DoctrineRepository
{
    protected function doByFilterParams(FilterParams $filterParams): Paginator
    {
        $qb = $this->em->createQueryBuilder();

        $qb->select($this->getEntityAliasName())
            ->from($this->getEntityClassName(), $this->getEntityAliasName());

        $filterParams->applyTo($qb);

        [$offset, $limit] = $this->calculatePagination($filterParams, $qb);

        return $this->createPaginator($qb, $offset, $limit);
    }

    /**
     * @param FilterParams $filterParams
     * @param QueryBuilder $qb
     *
     * @return list<int>
     */
    protected function calculatePagination(FilterParams $filterParams, QueryBuilder $qb): array
    {
        $result = $qb->getQuery()->getResult();

        Assert::true(is_countable($result));

        $totalResult = count($result);

        $offset = 0;
        $limit = 0 != $totalResult ? $totalResult : 1;

        if (-1 != $filterParams->get('per_page')) {
            $offset = $this->calculateOffset($filterParams);

            /** @var int $limit */
            $limit = $filterParams->get('per_page');
        }

        return [intval($offset), intval($limit)];
    }

    /**
     * @param QueryBuilder $qb
     * @param int          $offset
     * @param int          $limit
     *
     * @return Paginator
     */
    abstract protected function createPaginator(QueryBuilder $qb, int $offset, int $limit): Paginator;

    private function calculateOffset(FilterParams $filterParams): int
    {
        /** @var int $page */
        $page = $filterParams->get('page');

        /** @var int $perPage */
        $perPage = $filterParams->get('per_page');

        return ($page - 1) * $perPage;
    }
}
