<?php

declare(strict_types=1);

namespace App\Module\Catalog\Repository;

use App\Module\Catalog\Domain\Category;
use App\Module\Catalog\Domain\Item;
use App\Module\Catalog\Domain\Page;
use App\Module\Catalog\Domain\Supplier;

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

    /** @var list<array{name: string, email: string, phone: string}> */
    private const SUPPLIERS = [
        ['name' => 'Able Tools Ltd', 'email' => 'able@example.com', 'phone' => '+44 20 7946 0001'],
        ['name' => 'Baker Supplies Co', 'email' => 'baker@example.com', 'phone' => '+44 20 7946 0002'],
        ['name' => 'Charlie Hardware Ltd', 'email' => 'charlie@example.com', 'phone' => '+44 20 7946 0003'],
        ['name' => 'Delta Equipment Co', 'email' => 'delta@example.com', 'phone' => '+44 20 7946 0004'],
        ['name' => 'Echo Industrial Ltd', 'email' => 'echo@example.com', 'phone' => '+44 20 7946 0005'],
        ['name' => 'Foxtrot Tools Co', 'email' => 'foxtrot@example.com', 'phone' => '+44 20 7946 0006'],
        ['name' => 'Golf Gear Ltd', 'email' => 'golf@example.com', 'phone' => '+44 20 7946 0007'],
        ['name' => 'Hotel Supplies Co', 'email' => 'hotel@example.com', 'phone' => '+44 20 7946 0008'],
        ['name' => 'India Hardware Ltd', 'email' => 'india@example.com', 'phone' => '+44 20 7946 0009'],
        ['name' => 'Juliett Tools Co', 'email' => 'juliett@example.com', 'phone' => '+44 20 7946 0010'],
        ['name' => 'Kilo Equipment Ltd', 'email' => 'kilo@example.com', 'phone' => '+44 20 7946 0011'],
        ['name' => 'Lima Supplies Co', 'email' => 'lima@example.com', 'phone' => '+44 20 7946 0012'],
        ['name' => 'Mike Tools Ltd', 'email' => 'mike@example.com', 'phone' => '+44 20 7946 0013'],
        ['name' => 'November Hardware Co', 'email' => 'november@example.com', 'phone' => '+44 20 7946 0014'],
        ['name' => 'Oscar Gear Ltd', 'email' => 'oscar@example.com', 'phone' => '+44 20 7946 0015'],
        ['name' => 'Papa Tools Co', 'email' => 'papa@example.com', 'phone' => '+44 20 7946 0016'],
        ['name' => 'Quebec Equipment Ltd', 'email' => 'quebec@example.com', 'phone' => '+44 20 7946 0017'],
        ['name' => 'Romeo Supplies Co', 'email' => 'romeo@example.com', 'phone' => '+44 20 7946 0018'],
        ['name' => 'Sierra Tools Ltd', 'email' => 'sierra@example.com', 'phone' => '+44 20 7946 0019'],
        ['name' => 'Tango Hardware Co', 'email' => 'tango@example.com', 'phone' => '+44 20 7946 0020'],
        ['name' => 'Uniform Gear Ltd', 'email' => 'uniform@example.com', 'phone' => '+44 20 7946 0021'],
        ['name' => 'Victor Tools Co', 'email' => 'victor@example.com', 'phone' => '+44 20 7946 0022'],
        ['name' => 'Whiskey Supplies Ltd', 'email' => 'whiskey@example.com', 'phone' => '+44 20 7946 0023'],
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
                    category: $this->categoryFor($i, $name),
                    supplier: $this->supplierFor($i),
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

    private function supplierFor(int $index): Supplier
    {
        $data = self::SUPPLIERS[$index];

        return new Supplier($data['name'], $data['email'], $data['phone']);
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
