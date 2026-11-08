<?php

declare(strict_types=1);

namespace App\Module\Catalog\Http\Controller;

use App\Module\Catalog\Http\View\ItemView;
use App\Module\Catalog\Repository\ItemRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class ItemController extends AbstractController
{
    public function __construct(private readonly ItemRepository $items)
    {
    }

    #[Route('/api/items', methods: ['GET'])]
    public function list(): JsonResponse
    {
        return new JsonResponse(ItemView::many($this->items->all()));
    }

    #[Route('/api/items/count', methods: ['GET'])]
    public function count(): JsonResponse
    {
        return new JsonResponse(['count' => $this->items->count()]);
    }

    #[Route('/api/items/{id}', methods: ['GET'])]
    public function get(string $id): JsonResponse
    {
        $item = $this->items->find($id) ?? throw $this->createNotFoundException();

        return new JsonResponse(ItemView::one($item));
    }
}
