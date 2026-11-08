<?php

declare(strict_types=1);

namespace App\Module\Catalog\Http\Controller;

use App\Module\Catalog\Http\View\ItemView;
use App\Module\Catalog\Repository\ItemRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class ItemController extends AbstractController
{
    private const int DEFAULT_PER_PAGE = 10;
    private const int MAX_PER_PAGE = 50;

    public function __construct(private readonly ItemRepository $items)
    {
    }

    #[Route('/api/items', methods: ['GET'])]
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

        return new JsonResponse(ItemView::page($this->items->paginate($page, $perPage)));
    }

    #[Route('/api/items/{id}', methods: ['GET'])]
    public function get(string $id): JsonResponse
    {
        $item = $this->items->find($id) ?? throw $this->createNotFoundException();

        return new JsonResponse(ItemView::one($item));
    }
}
