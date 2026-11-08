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
        $maxPriceMinor = $this->maxPriceMinor($request);

        return new JsonResponse(ItemView::many($this->items->all($maxPriceMinor)));
    }

    private function maxPriceMinor(Request $request): ?int
    {
        $maxPrice = $request->query->get('maxPrice');
        if (null === $maxPrice || '' === $maxPrice) {
            return null;
        }

        if (!is_numeric($maxPrice) || (float) $maxPrice < 0) {
            throw new BadRequestHttpException('maxPrice must be a non-negative numeric value');
        }

        return (int) round((float) $maxPrice * 100);
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
