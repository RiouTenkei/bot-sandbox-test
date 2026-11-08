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
}
