<?php

declare(strict_types=1);

namespace App\Module\Catalog\Http\View;

use App\Module\Catalog\Domain\Category;

final class CategoryView
{
    /**
     * @param iterable<Category> $categories
     * @param array<string, int> $counts
     *
     * @return list<array{id: string, name: string, itemCount: int}>
     */
    public static function many(iterable $categories, array $counts): array
    {
        $out = [];
        foreach ($categories as $category) {
            $out[] = self::one($category, $counts[$category->id] ?? 0);
        }

        return $out;
    }

    /** @return array{id: string, name: string, itemCount: int} */
    public static function one(Category $category, int $count): array
    {
        return [
            'id' => $category->id,
            'name' => $category->name,
            'itemCount' => $count,
        ];
    }
}
