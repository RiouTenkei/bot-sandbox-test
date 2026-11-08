<?php

declare(strict_types=1);

namespace App\Module\Catalog\Http\View;

use App\Module\Catalog\Domain\Category;

final class CategoryView
{
    /**
     * @param iterable<array{category: Category, count: int}> $stats
     *
     * @return list<array{id: string, name: string, itemCount: int}>
     */
    public static function list(iterable $stats): array
    {
        $out = [];
        foreach ($stats as $stat) {
            $out[] = [
                'id' => $stat['category']->id(),
                'name' => $stat['category']->name(),
                'itemCount' => $stat['count'],
            ];
        }

        usort($out, static fn (array $a, array $b): int => $a['name'] <=> $b['name']);

        return $out;
    }
}
