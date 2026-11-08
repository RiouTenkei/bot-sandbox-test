<?php

declare(strict_types=1);

namespace App\Module\Catalog\Domain;

final readonly class Item
{
    public function __construct(
        public string $id,
        public string $name,
        public Category $category,
        public int $priceMinor,
        public string $currency,
        public \DateTimeImmutable $createdAt,
    ) {
    }
}
