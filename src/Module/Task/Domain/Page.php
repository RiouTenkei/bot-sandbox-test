<?php

declare(strict_types=1);

namespace App\Module\Task\Domain;

final readonly class Page
{
    /**
     * @param list<Task> $items
     */
    public function __construct(
        public array $items,
        public int $page,
        public int $perPage,
        public int $total,
    ) {
    }
}
