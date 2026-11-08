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

    private const WORKSHOP_NAMES = [
        'Anvil', 'Bellows', 'Chisel', 'Drill', 'Easel', 'File', 'Gouge', 'Hammer',
    ];

    private const WRITING_NAMES = [
        'Inkwell', 'Quill',
    ];

    /** @var list<Item>|null */
    private ?array $items = null;

    /** @return list<Item> */
    public function all(): array
    {
        if (null === $this->items) {
            $this->items = [];
            foreach (self::NAMES as $i => $name) {
                $this->items[] = new Item(
                    id: sprintf('item-%02d', $i + 1),
                    name: $name,
                    category: $this->resolveCategory($name),
                    priceMinor: 500 + $i * 125,
                    currency: 'GBP',
                    createdAt: new \DateTimeImmutable(sprintf('2026-01-%02dT09:00:00+00:00', $i + 1)),
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

    /** @return array<string, int> category id => item count */
    public function categoryCounts(): array
    {
        $counts = [];
        foreach (Category::all() as $category) {
            $counts[$category->id] = 0;
        }

        foreach ($this->all() as $item) {
            ++$counts[$item->category->id];
        }

        return $counts;
    }

    private function resolveCategory(string $name): Category
    {
        if (\in_array($name, self::WORKSHOP_NAMES, true)) {
            return Category::workshop();
        }

        if (\in_array($name, self::WRITING_NAMES, true)) {
            return Category::writing();
        }

        return Category::garden();
    }
}
