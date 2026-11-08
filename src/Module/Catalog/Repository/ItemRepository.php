<?php

declare(strict_types=1);

namespace App\Module\Catalog\Repository;

use App\Module\Catalog\Domain\Category;
use App\Module\Catalog\Domain\Item;
use App\Module\Catalog\Domain\Page;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Item>
 */
final class ItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Item::class);
    }

    /**
     * Counts, filters by maximum price (minor units, converted by the caller),
     * sorts by id and paginates, entirely in SQL.
     */
    public function paginate(int $page, int $perPage, ?int $maxPriceMinor = null): Page
    {
        $total = (int) $this->filteredQueryBuilder($maxPriceMinor)
            ->select('COUNT(i.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $items = $this->filteredQueryBuilder($maxPriceMinor)
            ->orderBy('i.id', 'ASC')
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage)
            ->getQuery()
            ->getResult();

        return new Page(
            items: $items,
            page: $page,
            perPage: $perPage,
            total: $total,
        );
    }

    public function totalCount(): int
    {
        return (int) $this->createQueryBuilder('i')
            ->select('COUNT(i.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findOne(string $id): ?Item
    {
        return $this->createQueryBuilder('i')
            ->where('i.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findCheapest(): ?Item
    {
        return $this->createQueryBuilder('i')
            ->orderBy('i.priceMinor', 'ASC')
            ->addOrderBy('i.id', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return list<array{category: Category, count: int}>
     */
    public function categoriesWithCounts(): array
    {
        $rows = $this->createQueryBuilder('i')
            ->select('c.id AS categoryId, c.name AS categoryName, COUNT(i.id) AS count')
            ->join('i.category', 'c')
            ->groupBy('c.id')
            ->addGroupBy('c.name')
            ->orderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult();

        return array_map(
            static fn (array $row): array => [
                'category' => new Category((string) $row['categoryId'], (string) $row['categoryName']),
                'count' => (int) $row['count'],
            ],
            $rows,
        );
    }

    private function filteredQueryBuilder(?int $maxPriceMinor): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('i');

        if (null !== $maxPriceMinor) {
            $queryBuilder
                ->where('i.priceMinor <= :maxPrice')
                ->setParameter('maxPrice', $maxPriceMinor);
        }

        return $queryBuilder;
    }
}
