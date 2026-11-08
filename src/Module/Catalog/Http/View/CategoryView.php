<?php

declare(strict_types=1);

namespace App\Module\Catalog\Http\View;

use App\Module\Catalog\Domain\Category;

final class CategoryView
{
    /** @return array{id: string, name: string} */
    public static function one(Category $category): array
    {
        return [
            'id' => $category->id,
            'name' => $category->name,
        ];
    }

    /**
     * @param array<string, int> $counts
     *
     * @return list<array{id: string, name: string, itemCount: int}>
     */
    public static function withCounts(array $counts): array
    {
        $out = [];
        foreach (Category::all() as $category) {
            $out[] = [
                'id' => $category->id,
                'name' => $category->name,
                'itemCount' => $counts[$category->id] ?? 0,
            ];
        }

        usort($out, static fn (array $a, array $b): int => $a['name'] <=> $b['name']);

        return $out;
    }
}
