<?php

declare(strict_types=1);

namespace App\Module\Catalog\Domain;

final readonly class Category
{
    public const string WORKSHOP_ID = 'workshop';
    public const string WRITING_ID = 'writing';
    public const string GARDEN_ID = 'garden';

    public function __construct(
        public string $id,
        public string $name,
    ) {
    }

    public static function workshop(): self
    {
        return new self(self::WORKSHOP_ID, 'Workshop');
    }

    public static function writing(): self
    {
        return new self(self::WRITING_ID, 'Writing');
    }

    public static function garden(): self
    {
        return new self(self::GARDEN_ID, 'Garden');
    }
}
