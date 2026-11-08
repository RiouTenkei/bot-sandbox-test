<?php

declare(strict_types=1);

namespace App\Module\Catalog\Domain;

enum Category: string
{
    case WORKSHOP = 'workshop';
    case WRITING = 'writing';
    case GARDEN = 'garden';

    public function id(): string
    {
        return $this->value;
    }

    public function name(): string
    {
        return match ($this) {
            self::WORKSHOP => 'Workshop',
            self::WRITING => 'Writing',
            self::GARDEN => 'Garden',
        };
    }

    public static function forItemName(string $name): self
    {
        return match ($name) {
            'Anvil', 'Bellows', 'Chisel', 'Drill', 'Easel', 'File', 'Gouge', 'Hammer' => self::WORKSHOP,
            'Inkwell', 'Quill' => self::WRITING,
            default => self::GARDEN,
        };
    }
}
