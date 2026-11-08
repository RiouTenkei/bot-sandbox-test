<?php

declare(strict_types=1);

namespace App\Tests\Module\Catalog;

use App\Tests\Fixtures\CatalogTestFixtures;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class CategoryControllerTest extends WebTestCase
{
    protected function setUp(): void
    {
        self::bootKernel();
        $this->fixtures()->resetToDefault();
        self::ensureKernelShutdown();
        parent::setUp();
    }

    private function fixtures(): CatalogTestFixtures
    {
        $fixtures = self::getContainer()->get(CatalogTestFixtures::class);
        \assert($fixtures instanceof CatalogTestFixtures);

        return $fixtures;
    }

    public function testCategoriesReturnsCategoriesSortedByNameWithCounts(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/categories');

        self::assertResponseIsSuccessful();
        $body = json_decode((string) $client->getResponse()->getContent(), true);

        self::assertSame(
            [
                ['id' => 'garden', 'name' => 'Garden', 'itemCount' => 13],
                ['id' => 'workshop', 'name' => 'Workshop', 'itemCount' => 8],
                ['id' => 'writing', 'name' => 'Writing', 'itemCount' => 2],
            ],
            $body,
        );
    }

    public function testCategoryItemCountsSumToTotalItems(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/categories');

        self::assertResponseIsSuccessful();
        $body = json_decode((string) $client->getResponse()->getContent(), true);

        $total = 0;
        foreach ($body as $category) {
            $total += $category['itemCount'];
        }

        self::assertSame(23, $total);
    }
}
