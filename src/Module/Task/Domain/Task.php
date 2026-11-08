<?php

declare(strict_types=1);

namespace App\Module\Task\Domain;

final readonly class Task
{
    public function __construct(
        public string $id,
        public string $title,
        public bool $isDone,
        public \DateTimeImmutable $createdAt,
    ) {
    }
}
