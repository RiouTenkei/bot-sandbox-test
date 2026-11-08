<?php

declare(strict_types=1);

namespace App\Module\Health\Http\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class HealthController extends AbstractController
{
    /**
     * Return a simple ok status for monitoring checks.
     */
    #[Route('/api/health', methods: ['GET'])]
    public function check(): JsonResponse
    {
        return new JsonResponse(['status' => 'ok']);
    }
}
