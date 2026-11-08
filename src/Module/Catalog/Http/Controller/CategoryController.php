<?php

declare(strict_types=1);

namespace App\Module\Catalog\Http\Controller;

use App\Module\Catalog\Domain\Category;
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
        $summaries = [];
        foreach ($this->items->all() as $item) {
            $id = $item->category->id;
            $summaries[$id]['category'] ??= new Category($id, $item->category->name);
            $summaries[$id]['itemCount'] = ($summaries[$id]['itemCount'] ?? 0) + 1;
        }

        usort($summaries, static fn (array $a, array $b): int => $a['category']->name <=> $b['category']->name);

        return new JsonResponse(CategoryView::many($summaries));
    }
}
