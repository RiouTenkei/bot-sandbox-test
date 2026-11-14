<?php

declare(strict_types=1);

namespace App\Module\Task\Http\Controller;

use App\Module\Task\Http\View\TaskView;
use App\Module\Task\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class TaskController extends AbstractController
{
    private const int DEFAULT_PER_PAGE = 20;
    private const int MAX_PER_PAGE = 50;
    private const array ALLOWED_SORTS = ['id', 'title', '-title'];

    public function __construct(private readonly TaskRepository $tasks)
    {
    }

    #[Route('/api/tasks', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $page = (int) $request->query->get('page', 1);
        if ($page < 1) {
            $page = 1;
        }

        $perPage = (int) $request->query->get('perPage', self::DEFAULT_PER_PAGE);
        if ($perPage < 1) {
            $perPage = self::DEFAULT_PER_PAGE;
        }
        if ($perPage > self::MAX_PER_PAGE) {
            $perPage = self::MAX_PER_PAGE;
        }

        $sort = (string) $request->query->get('sort', 'id');
        if (!in_array($sort, self::ALLOWED_SORTS, true)) {
            return new JsonResponse([
                'error' => sprintf('Unknown sort "%s"; allowed values are: id, title, -title', $sort),
            ], 400);
        }

        $done = false;
        $doneParam = $request->query->get('done');
        if (null !== $doneParam && '' !== $doneParam) {
            $lower = strtolower((string) $doneParam);
            if ('true' !== $lower && 'false' !== $lower) {
                return new JsonResponse(['error' => 'done must be true or false'], 400);
            }
            $done = 'true' === $lower;
        }

        return new JsonResponse(TaskView::page($this->tasks->paginate($page, $perPage, $done, $sort)));
    }

    #[Route('/api/tasks/count', methods: ['GET'])]
    public function count(): JsonResponse
    {
        return new JsonResponse($this->tasks->count());
    }

    #[Route('/api/tasks/{id}', methods: ['GET'])]
    public function get(string $id): JsonResponse
    {
        $task = $this->tasks->find($id);

        if (null === $task) {
            return new JsonResponse(['error' => 'Task not found'], 404);
        }

        return new JsonResponse(TaskView::one($task));
    }
}
