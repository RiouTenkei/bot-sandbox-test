<?php

declare(strict_types=1);

namespace App\Module\Catalog\Http\View;

use App\Module\Catalog\Domain\Category;

final class CategoryView
{
    /**
     * @param iterable<array{category: Category, count: int}> $categories
     *
     * @return list<array{id: string, name: string, itemCount: int}>
     */
    public static function many(iterable $categories): array
    {
        $out = [];
        foreach ($categories as ['category' => $category, 'count' => $count]) {
            $out[] = [
                'id' => $category->id(),
                'name' => $category->name(),
                'itemCount' => $count,
            ];
        }

        return $out;
    }
}
