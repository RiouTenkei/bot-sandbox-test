<?php

declare(strict_types=1);

namespace App\Module\Task\Repository;

use App\Module\Task\Domain\Page;
use App\Module\Task\Domain\Task;

/**
 * Fixed in-memory task list, so the sandbox needs no database.
 */
final class TaskRepository
{
    private const TITLES = [
        'Plan the sprint',
        'Write the tests',
        'Build the controller',
        'Wire the repository',
        'Add the view layer',
        'Run the suite',
        'Refactor the edge cases',
        'Document the contract',
        'Ship to staging',
        'Verify in production',
        'Write the retrospective',
        'Close the ticket',
    ];

    /** @var list<Task>|null */
    private ?array $tasks = null;

    /** @return list<Task> */
    public function all(): array
    {
        if (null === $this->tasks) {
            $this->tasks = [];
            foreach (self::TITLES as $i => $title) {
                $this->tasks[] = new Task(
                    id: sprintf('task-%02d', $i + 1),
                    title: $title,
                    isDone: $i < 3,
                    createdAt: new \DateTimeImmutable(sprintf('2026-02-%02dT10:00:00+00:00', $i + 1)),
                );
            }
        }

        return $this->tasks;
    }

    public function paginate(int $page, int $perPage, ?array $tasks = null): Page
    {
        $tasks ??= $this->all();
        $offset = ($page - 1) * $perPage;

        return new Page(
            items: array_slice($tasks, $offset, $perPage),
            page: $page,
            perPage: $perPage,
            total: count($tasks),
        );
    }

    public function find(string $id): ?Task
    {
        foreach ($this->all() as $task) {
            if ($task->id === $id) {
                return $task;
            }
        }

        return null;
    }
}
