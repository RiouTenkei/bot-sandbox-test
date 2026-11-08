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

        $maxPrice = $request->query->get('maxPrice');
        $maxPriceMinor = null;

        if (null !== $maxPrice && '' !== $maxPrice) {
            if (!is_numeric($maxPrice) || (float) $maxPrice < 0) {
                return new JsonResponse(['error' => 'maxPrice must be a non-negative number'], 400);
            }

            $maxPriceMinor = (int) round((float) $maxPrice * 100);
        }

        return new JsonResponse(ItemView::page($this->items->paginate($page, $perPage, $maxPriceMinor)));
    }

    #[Route('/api/items/count', methods: ['GET'], priority: 10)]
    public function count(): JsonResponse
    {
        return new JsonResponse(['count' => $this->items->totalCount()]);
    }

    #[Route('/api/items/cheapest', methods: ['GET'], priority: 10)]
    public function cheapest(): JsonResponse
    {
        $item = $this->items->findCheapest();

        if (null === $item) {
            return new JsonResponse(['error' => 'Item not found'], 404);
        }

        return new JsonResponse(ItemView::one($item));
    }

    #[Route('/api/items/{id}', methods: ['GET'])]
    public function get(string $id): JsonResponse
    {
        $item = $this->items->findOne($id);

        if (null === $item) {
            return new JsonResponse(['error' => 'Item not found'], 404);
        }

        return new JsonResponse(ItemView::one($item));
    }
}
