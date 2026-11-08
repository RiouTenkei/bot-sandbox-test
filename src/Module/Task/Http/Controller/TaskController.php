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
    private const int DEFAULT_PER_PAGE = 10;
    private const int MAX_PER_PAGE = 50;

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

        return new JsonResponse(TaskView::page($this->tasks->paginate($page, $perPage)));
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
