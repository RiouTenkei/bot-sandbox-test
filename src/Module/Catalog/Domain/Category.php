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

    public static function workshop(): self
    {
        return new self('workshop', 'Workshop');
    }

    public static function writing(): self
    {
        return new self('writing', 'Writing');
    }

    public static function garden(): self
    {
        return new self('garden', 'Garden');
    }
}
