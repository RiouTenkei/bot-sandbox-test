<?php

declare(strict_types=1);

namespace App\Module\Catalog\Http\View;

use App\Module\Catalog\Domain\Category;

final class CategoryView
{
    /**
     * @param iterable<array{category: Category, itemCount: int}> $summaries
     *
     * @return list<array{id: string, name: string, itemCount: int}>
     */
    public static function many(iterable $summaries): array
    {
        $out = [];
        foreach ($summaries as $summary) {
            $out[] = [
                'id' => $summary['category']->id,
                'name' => $summary['category']->name,
                'itemCount' => $summary['itemCount'],
            ];
        }

        return $out;
    }
}
