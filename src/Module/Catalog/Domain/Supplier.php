<?php

declare(strict_types=1);

namespace App\Module\Catalog\Domain;

final readonly class Supplier
{
    public function __construct(
        public string $name,
        public string $email,
        public string $phone,
    ) {
    }
}
