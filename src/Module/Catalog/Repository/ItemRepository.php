<?php

declare(strict_types=1);

namespace App\Module\Catalog\Repository;

use App\Module\Catalog\Domain\Category;
use App\Module\Catalog\Domain\Item;
use App\Module\Catalog\Domain\Page;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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

    public function paginate(int $page, int $perPage, ?array $items = null): Page
    {
        if (null !== $items) {
            $total = count($items);
            $offset = ($page - 1) * $perPage;

            return new Page(
                items: array_slice($items, $offset, $perPage),
                page: $page,
                perPage: $perPage,
                total: $total,
            );
        }

        $total = (int) $this->createQueryBuilder('i')
            ->select('COUNT(i.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $items = $this->createQueryBuilder('i')
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

    /** @return list<Item> */
    public function all(): array
    {
        return $this->createQueryBuilder('i')
            ->orderBy('i.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /** @return list<Item> */
    public function allByMaxPrice(int $maxPriceMinor): array
    {
        return $this->createQueryBuilder('i')
            ->where('i.priceMinor <= :maxPrice')
            ->setParameter('maxPrice', $maxPriceMinor)
            ->orderBy('i.id', 'ASC')
            ->getQuery()
            ->getResult();
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
}
