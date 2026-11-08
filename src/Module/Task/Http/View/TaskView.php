<?php

declare(strict_types=1);

namespace App\Module\Task\Http\View;

use App\Module\Task\Domain\Page;
use App\Module\Task\Domain\Task;

final class TaskView
{
    /** @return array{id: string, title: string, isDone: bool, createdAt: string, parentId: string|null, children: list<array<string, mixed>>} */
    public static function one(Task $task): array
    {
        return [
            'id' => $task->id,
            'title' => $task->title,
            'isDone' => $task->isDone,
            'createdAt' => $task->createdAt->format(\DATE_ATOM),
            'parentId' => $task->parentId,
            'children' => self::many($task->children),
        ];
    }

    /**
     * @param iterable<Task> $tasks
     *
     * @return list<array<string, mixed>>
     */
    public static function many(iterable $tasks): array
    {
        $out = [];
        foreach ($tasks as $task) {
            $out[] = self::one($task);
        }

        return $out;
    }

    /** @return array{items: list<array<string, mixed>>, page: int, perPage: int, total: int} */
    public static function page(Page $page): array
    {
        return [
            'items' => self::many($page->items),
            'page' => $page->page,
            'perPage' => $page->perPage,
            'total' => $page->total,
        ];
    }
}
