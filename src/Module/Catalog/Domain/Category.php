<?php

declare(strict_types=1);

namespace App\Module\Catalog\Domain;

final readonly class Category
{
    public function __construct(
        public string $id,
        public string $name,
    ) {
    }
}
