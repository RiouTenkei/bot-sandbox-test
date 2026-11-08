<?php

declare(strict_types=1);

namespace App\Module\Catalog\Repository;

use App\Module\Catalog\Domain\Category;
use App\Module\Catalog\Domain\Item;
use App\Module\Catalog\Domain\Page;

/**
 * Fixed in-memory catalogue, so the sandbox needs no database.
 */
final class ItemRepository
{
    private const NAMES = [
        'Anvil', 'Bellows', 'Chisel', 'Drill', 'Easel', 'File', 'Gouge', 'Hammer',
        'Inkwell', 'Jigsaw', 'Kettle', 'Ladder', 'Mallet', 'Needle', 'Oilcan',
        'Plane', 'Quill', 'Rasp', 'Saw', 'Trowel', 'Umbrella', 'Vice', 'Wrench',
    ];

    /** @var list<Item>|null */
    private ?array $items = null;

    private ?Item $cheapest = null;
    private bool $cheapestLoaded = false;

    /** @return list<Item> */
    public function all(): array
    {
        if (null === $this->items) {
            $this->items = [];
            foreach (self::NAMES as $i => $name) {
                $this->items[] = new Item(
                    id: sprintf('item-%02d', $i + 1),
                    name: $name,
                    priceMinor: 500 + $i * 125,
                    currency: 'GBP',
                    createdAt: new \DateTimeImmutable(sprintf('2026-01-%02dT09:00:00+00:00', $i + 1)),
                    category: $this->categoryFor($i, $name),
                );
            }
        }

        return $this->items;
    }

    public function paginate(int $page, int $perPage, ?array $items = null): Page
    {
        $items ??= $this->all();
        $offset = ($page - 1) * $perPage;

        return new Page(
            items: array_slice($items, $offset, $perPage),
            page: $page,
            perPage: $perPage,
            total: count($items),
        );
    }

    /** @return list<Item> */
    public function allByMaxPrice(int $maxPriceMinor): array
    {
        return array_values(
            array_filter(
                $this->all(),
                static fn (Item $item): bool => $item->priceMinor <= $maxPriceMinor,
            ),
        );
    }

    public function find(string $id): ?Item
    {
        foreach ($this->all() as $item) {
            if ($item->id === $id) {
                return $item;
            }
        }

        return null;
    }

    public function findCheapest(): ?Item
    {
        if ($this->cheapestLoaded) {
            return $this->cheapest;
        }

        $cheapest = null;
        foreach ($this->all() as $item) {
            if (null === $cheapest || $item->priceMinor < $cheapest->priceMinor) {
                $cheapest = $item;
                continue;
            }

            if ($item->priceMinor === $cheapest->priceMinor && $item->id < $cheapest->id) {
                $cheapest = $item;
            }
        }

        $this->cheapest = $cheapest;
        $this->cheapestLoaded = true;

        return $cheapest;
    }

    private function categoryFor(int $index, string $name): Category
    {
        if ($index < 8) {
            return Category::workshop();
        }

        if ('Inkwell' === $name || 'Quill' === $name) {
            return Category::writing();
        }

        return Category::garden();
    }

    /**
     * @return list<array{category: Category, count: int}>
     */
    public function categoriesWithCounts(): array
    {
        $counts = [];
        foreach ($this->all() as $item) {
            $id = $item->category->id;
            if (!isset($counts[$id])) {
                $counts[$id] = ['category' => $item->category, 'count' => 0];
            }
            ++$counts[$id]['count'];
        }

        usort(
            $counts,
            static fn (array $a, array $b): int => $a['category']->name <=> $b['category']->name,
        );

        return array_values($counts);
    }
}
