<?php

declare(strict_types=1);

namespace App\Module\Catalog\Http\Controller;

use App\Module\Catalog\Http\View\ItemView;
use App\Module\Catalog\Repository\ItemRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;

final class ItemController extends AbstractController
{
    public function __construct(private readonly ItemRepository $items)
    {
    }

    #[Route('/api/items', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $maxPrice = $request->query->get('maxPrice');
        if (null === $maxPrice) {
            return new JsonResponse(ItemView::many($this->items->all()));
        }

        if (!is_numeric($maxPrice) || (float) $maxPrice < 0) {
            throw new BadRequestHttpException('maxPrice must be a non-negative number.');
        }

        $maxPriceMinor = (int) round(((float) $maxPrice) * 100);

        return new JsonResponse(ItemView::many($this->items->findByMaxPrice($maxPriceMinor)));
    }

    #[Route('/api/items/count', methods: ['GET'], priority: 10)]
    public function count(): JsonResponse
    {
        return new JsonResponse(['count' => count($this->items->all())]);
    }

    #[Route('/api/items/{id}', methods: ['GET'])]
    public function get(string $id): JsonResponse
    {
        $item = $this->items->find($id) ?? throw $this->createNotFoundException();

        return new JsonResponse(ItemView::one($item));
    }
}
