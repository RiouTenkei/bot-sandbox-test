<?php

declare(strict_types=1);

namespace App\Module\Task\Domain;

final class Task
{
    public ?string $parentId = null;

    /** @var list<Task> */
    public array $children = [];

    public function __construct(
        public readonly string $id,
        public readonly string $title,
        public readonly bool $isDone,
        public readonly \DateTimeImmutable $createdAt,
    ) {
    }

    /**
     * @param list<Task> $children
     */
    public function attachChildren(array $children): void
    {
        $this->children = $children;
        foreach ($children as $child) {
            $child->parentId = $this->id;
        }
    }
}
