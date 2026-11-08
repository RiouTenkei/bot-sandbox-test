<?php

declare(strict_types=1);

namespace App\Module\Catalog\Http\Controller;

use App\Module\Catalog\Http\View\CategoryView;
use App\Module\Catalog\Repository\ItemRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class CategoryController extends AbstractController
{
    public function __construct(private readonly ItemRepository $items)
    {
    }

    #[Route('/api/categories', methods: ['GET'])]
    public function list(): JsonResponse
    {
        return new JsonResponse(CategoryView::many($this->items->categorySummaries()));
    }
}
