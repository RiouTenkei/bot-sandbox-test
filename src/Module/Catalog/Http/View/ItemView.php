<?php

declare(strict_types=1);

namespace App\Module\Catalog\Http\View;

use App\Module\Catalog\Domain\Item;
use App\Module\Catalog\Domain\Page;

final class ItemView
{
    /** @return array{id: string, name: string, price: array{amount: float, currency: string}, createdAt: string, category: array{id: string, name: string}, supplier: array{name: string}} */
    public static function one(Item $item): array
    {
        return [
            'id' => $item->id,
            'name' => $item->name,
            'price' => ['amount' => $item->priceMinor / 100, 'currency' => $item->currency],
            'createdAt' => $item->createdAt->format(\DATE_ATOM),
            'category' => ['id' => $item->category->id, 'name' => $item->category->name],
            'supplier' => ['name' => $item->supplier->name],
        ];
    }

    /**
     * @param iterable<Item> $items
     *
     * @return list<array<string, mixed>>
     */
    public static function many(iterable $items): array
    {
        $out = [];
        foreach ($items as $item) {
            $out[] = self::one($item);
        }

        return $out;
    }

    /** @return array{items: list<array<string, mixed>>, page: int, perPage: int, total: int} */
    public static function page(Page $page): array
    {
        return [
            'items' => self::many($page->items),
            'page' => $page->page,
            'perPage' => $page->perPage,
            'total' => $page->total,
        ];
    }
}
