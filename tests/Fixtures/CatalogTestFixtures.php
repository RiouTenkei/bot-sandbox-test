<?php

declare(strict_types=1);

namespace App\Tests\Fixtures;

use App\Module\Catalog\Domain\Category;
use App\Module\Catalog\Domain\Item;
use Doctrine\DBAL\Connection;

final class CatalogTestFixtures
{
    /** @param Connection $connection */
    public function __construct(private readonly Connection $connection)
    {
    }

    public function resetToDefault(): void
    {
        $this->connection->executeStatement('TRUNCATE TABLE items, categories RESTART IDENTITY CASCADE');

        foreach ($this->defaultCategories() as $category) {
            $this->connection->insert('categories', [
                'id' => $category->id(),
                'name' => $category->name(),
            ]);
        }

        foreach ($this->defaultItems() as $item) {
            $this->connection->insert('items', [
                'id' => $item->id(),
                'name' => $item->name(),
                'price_minor' => $item->priceMinor(),
                'currency' => $item->currency(),
                'created_at' => $item->createdAt()->format(\DATE_ATOM),
                'category_id' => $item->category()->id(),
            ]);
        }
    }

    public function resetToEmpty(): void
    {
        $this->connection->executeStatement('TRUNCATE TABLE items, categories RESTART IDENTITY CASCADE');
    }

    /**
     * @param list<Item> $items
     */
    public function replaceItems(array $items): void
    {
        $this->connection->executeStatement('TRUNCATE TABLE items RESTART IDENTITY CASCADE');

        foreach ($items as $item) {
            $this->ensureCategory($item->category());
            $this->connection->insert('items', [
                'id' => $item->id(),
                'name' => $item->name(),
                'price_minor' => $item->priceMinor(),
                'currency' => $item->currency(),
                'created_at' => $item->createdAt()->format(\DATE_ATOM),
                'category_id' => $item->category()->id(),
            ]);
        }
    }

    private function ensureCategory(Category $category): void
    {
        $exists = $this->connection->fetchOne(
            'SELECT 1 FROM categories WHERE id = :id',
            ['id' => $category->id()],
        );

        if (false === $exists) {
            $this->connection->insert('categories', [
                'id' => $category->id(),
                'name' => $category->name(),
            ]);
        }
    }

    /**
     * @return list<Category>
     */
    private function defaultCategories(): array
    {
        return [
            Category::workshop(),
            Category::writing(),
            Category::garden(),
        ];
    }

    /**
     * @return list<Item>
     */
    private function defaultItems(): array
    {
        return [
            new Item('item-01', 'Anvil', 500, 'GBP', new \DateTimeImmutable('2026-01-01T09:00:00+00:00'), Category::workshop()),
            new Item('item-02', 'Bellows', 625, 'GBP', new \DateTimeImmutable('2026-01-02T09:00:00+00:00'), Category::workshop()),
            new Item('item-03', 'Chisel', 750, 'GBP', new \DateTimeImmutable('2026-01-03T09:00:00+00:00'), Category::workshop()),
            new Item('item-04', 'Drill', 875, 'GBP', new \DateTimeImmutable('2026-01-04T09:00:00+00:00'), Category::workshop()),
            new Item('item-05', 'Easel', 1000, 'GBP', new \DateTimeImmutable('2026-01-05T09:00:00+00:00'), Category::workshop()),
            new Item('item-06', 'File', 1125, 'GBP', new \DateTimeImmutable('2026-01-06T09:00:00+00:00'), Category::workshop()),
            new Item('item-07', 'Gouge', 1250, 'GBP', new \DateTimeImmutable('2026-01-07T09:00:00+00:00'), Category::workshop()),
            new Item('item-08', 'Hammer', 1375, 'GBP', new \DateTimeImmutable('2026-01-08T09:00:00+00:00'), Category::workshop()),
            new Item('item-09', 'Inkwell', 1500, 'GBP', new \DateTimeImmutable('2026-01-09T09:00:00+00:00'), Category::writing()),
            new Item('item-10', 'Jigsaw', 1625, 'GBP', new \DateTimeImmutable('2026-01-10T09:00:00+00:00'), Category::garden()),
            new Item('item-11', 'Kettle', 1750, 'GBP', new \DateTimeImmutable('2026-01-11T09:00:00+00:00'), Category::garden()),
            new Item('item-12', 'Ladder', 1875, 'GBP', new \DateTimeImmutable('2026-01-12T09:00:00+00:00'), Category::garden()),
            new Item('item-13', 'Mallet', 2000, 'GBP', new \DateTimeImmutable('2026-01-13T09:00:00+00:00'), Category::garden()),
            new Item('item-14', 'Needle', 2125, 'GBP', new \DateTimeImmutable('2026-01-14T09:00:00+00:00'), Category::garden()),
            new Item('item-15', 'Oilcan', 2250, 'GBP', new \DateTimeImmutable('2026-01-15T09:00:00+00:00'), Category::garden()),
            new Item('item-16', 'Plane', 2375, 'GBP', new \DateTimeImmutable('2026-01-16T09:00:00+00:00'), Category::garden()),
            new Item('item-17', 'Quill', 2500, 'GBP', new \DateTimeImmutable('2026-01-17T09:00:00+00:00'), Category::writing()),
            new Item('item-18', 'Rasp', 2625, 'GBP', new \DateTimeImmutable('2026-01-18T09:00:00+00:00'), Category::garden()),
            new Item('item-19', 'Saw', 2750, 'GBP', new \DateTimeImmutable('2026-01-19T09:00:00+00:00'), Category::garden()),
            new Item('item-20', 'Trowel', 2875, 'GBP', new \DateTimeImmutable('2026-01-20T09:00:00+00:00'), Category::garden()),
            new Item('item-21', 'Umbrella', 3000, 'GBP', new \DateTimeImmutable('2026-01-21T09:00:00+00:00'), Category::garden()),
            new Item('item-22', 'Vice', 3125, 'GBP', new \DateTimeImmutable('2026-01-22T09:00:00+00:00'), Category::garden()),
            new Item('item-23', 'Wrench', 3250, 'GBP', new \DateTimeImmutable('2026-01-23T09:00:00+00:00'), Category::garden()),
        ];
    }
}
