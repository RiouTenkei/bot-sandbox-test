<?php

declare(strict_types=1);

namespace App\Module\Catalog\Repository;

use App\Module\Catalog\Domain\Item;

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
                );
            }
        }

        return $this->items;
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

    /** @return list<Item> */
    public function findByMaxPrice(int $maxPriceMinor): array
    {
        $items = [];
        foreach ($this->all() as $item) {
            if ($item->priceMinor <= $maxPriceMinor) {
                $items[] = $item;
            }
        }

        return $items;
    }
}
