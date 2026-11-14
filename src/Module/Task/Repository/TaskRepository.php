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

    /** @var array<string, list<string>> */
    private const PARENT_CHILDREN = [
        'task-01' => ['task-02', 'task-03'],
        'task-04' => ['task-05', 'task-06'],
    ];

    /** @var list<Task>|null */
    private ?array $tasks = null;

    /** @return list<Task> */
    public function all(): array
    {
        if (null === $this->tasks) {
            $tasksById = [];
            foreach (self::TITLES as $i => $title) {
                $id = sprintf('task-%02d', $i + 1);
                $tasksById[$id] = new Task(
                    id: $id,
                    title: $title,
                    isDone: $i < 3,
                    createdAt: new \DateTimeImmutable(sprintf('2026-02-%02dT10:00:00+00:00', $i + 1)),
                );
            }

            foreach (self::PARENT_CHILDREN as $parentId => $childrenIds) {
                $parent = $tasksById[$parentId];
                $children = [];
                foreach ($childrenIds as $childId) {
                    $children[] = $tasksById[$childId];
                }
                $parent->attachChildren($children);
            }

            $this->tasks = array_values($tasksById);
        }

        return $this->tasks;
    }

    public function paginate(int $page, int $perPage, ?array $tasks = null, string $sort = 'id'): Page
    {
        $tasks ??= $this->all();
        $tasks = $this->sort($tasks, $sort);
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

    /** @param list<Task> $tasks
     *  @return list<Task>
     */
    private function sort(array $tasks, string $sort): array
    {
        if ('id' === $sort) {
            return $tasks;
        }

        usort($tasks, static function (Task $a, Task $b) use ($sort): int {
            return '-' === $sort[0]
                ? strcmp($b->title, $a->title)
                : strcmp($a->title, $b->title);
        });

        return $tasks;
    }
}
