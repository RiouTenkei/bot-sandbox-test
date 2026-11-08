<?php

declare(strict_types=1);

namespace App\Tests\Module\Catalog;

use App\Module\Catalog\Domain\Category;
use App\Module\Catalog\Domain\Item;
use App\Module\Catalog\Repository\ItemRepository;
use App\Tests\Fixtures\CatalogTestFixtures;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ItemControllerTest extends WebTestCase
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

    public function testListReturnsFirstPageByDefault(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/items');

        self::assertResponseIsSuccessful();
        $body = json_decode((string) $client->getResponse()->getContent(), true);

        self::assertCount(10, $body['items']);
        self::assertSame(1, $body['page']);
        self::assertSame(10, $body['perPage']);
        self::assertSame(23, $body['total']);
        self::assertSame(
            [
                'id' => 'item-01',
                'name' => 'Anvil',
                'price' => ['amount' => 5, 'currency' => 'GBP'],
                'createdAt' => '2026-01-01T09:00:00+00:00',
                'category' => ['id' => 'workshop', 'name' => 'Workshop'],
            ],
            $body['items'][0],
        );
    }

    public function testListWithPageThreeAndPerPageTenReturnsThreeItems(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/items?page=3&perPage=10');

        self::assertResponseIsSuccessful();
        $body = json_decode((string) $client->getResponse()->getContent(), true);

        self::assertCount(3, $body['items']);
        self::assertSame(3, $body['page']);
        self::assertSame(10, $body['perPage']);
        self::assertSame(23, $body['total']);
        self::assertSame(
            [
                'id' => 'item-21',
                'name' => 'Umbrella',
                'price' => ['amount' => 30, 'currency' => 'GBP'],
                'createdAt' => '2026-01-21T09:00:00+00:00',
                'category' => ['id' => 'garden', 'name' => 'Garden'],
            ],
            $body['items'][0],
        );
    }

    public function testListCapsPerPageAtFifty(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/items?perPage=100');

        self::assertResponseIsSuccessful();
        $body = json_decode((string) $client->getResponse()->getContent(), true);

        self::assertCount(23, $body['items']);
        self::assertSame(50, $body['perPage']);
    }

    public function testListTreatsPageBelowOneAsOne(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/items?page=0');

        self::assertResponseIsSuccessful();
        $body = json_decode((string) $client->getResponse()->getContent(), true);

        self::assertSame(1, $body['page']);
        self::assertSame('item-01', $body['items'][0]['id']);
    }

    public function testGetReturnsOneItem(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/items/item-08');

        self::assertResponseIsSuccessful();
        $item = json_decode((string) $client->getResponse()->getContent(), true);
        self::assertSame('Hammer', $item['name']);
        self::assertSame(13.75, $item['price']['amount']);
        self::assertSame(['id' => 'workshop', 'name' => 'Workshop'], $item['category']);
    }

    public function testGetUnknownItemIs404(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/items/nope');

        self::assertResponseStatusCodeSame(404);
        self::assertResponseHeaderSame('content-type', 'application/json');
        self::assertSame(['error' => 'Item not found'], json_decode((string) $client->getResponse()->getContent(), true));
    }

    public function testCountReturnsTotal(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/items/count');

        self::assertResponseIsSuccessful();
        self::assertSame(['count' => 23], json_decode((string) $client->getResponse()->getContent(), true));
    }

    public function testGetItem01StillWorks(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/items/item-01');

        self::assertResponseIsSuccessful();
        $item = json_decode((string) $client->getResponse()->getContent(), true);
        self::assertSame('Anvil', $item['name']);
        self::assertSame(['id' => 'workshop', 'name' => 'Workshop'], $item['category']);
    }

    public function testCheapestReturnsLowestPriceItem(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/items/cheapest');

        self::assertResponseIsSuccessful();
        $item = json_decode((string) $client->getResponse()->getContent(), true);

        self::assertSame(
            [
                'id' => 'item-01',
                'name' => 'Anvil',
                'price' => ['amount' => 5, 'currency' => 'GBP'],
                'createdAt' => '2026-01-01T09:00:00+00:00',
                'category' => ['id' => 'workshop', 'name' => 'Workshop'],
            ],
            $item,
        );
    }

    public function testCheapestTieBreaksByLowestId(): void
    {
        $client = static::createClient();
        $items = $this->createTiedPriceCatalogue();
        $this->replaceRepositoryItems($client, $items);

        $client->request('GET', '/api/items/cheapest');

        self::assertResponseIsSuccessful();
        $item = json_decode((string) $client->getResponse()->getContent(), true);
        self::assertSame('item-a', $item['id']);
    }

    public function testCheapestWithNoItemsReturns404(): void
    {
        $client = static::createClient();
        $this->replaceRepositoryItems($client, []);

        $client->request('GET', '/api/items/cheapest');

        self::assertResponseStatusCodeSame(404);
        self::assertResponseHeaderSame('content-type', 'application/json');
        self::assertSame(['error' => 'Item not found'], json_decode((string) $client->getResponse()->getContent(), true));
    }

    /**
     * @return list<Item>
     */
    private function createTiedPriceCatalogue(): array
    {
        $category = Category::workshop();
        $createdAt = new \DateTimeImmutable('2026-01-01T09:00:00+00:00');

        return [
            new Item(id: 'item-b', name: 'B', priceMinor: 100, currency: 'GBP', createdAt: $createdAt, category: $category),
            new Item(id: 'item-a', name: 'A', priceMinor: 100, currency: 'GBP', createdAt: $createdAt, category: $category),
        ];
    }

    /**
     * @param list<Item> $items
     */
    private function replaceRepositoryItems(\Symfony\Bundle\FrameworkBundle\KernelBrowser $client, array $items): void
    {
        $this->fixtures()->replaceItems($items);
    }

    public function testListFiltersByMaxPrice(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/items?maxPrice=6');

        self::assertResponseIsSuccessful();
        $body = json_decode((string) $client->getResponse()->getContent(), true);
        self::assertCount(1, $body['items']);
        self::assertSame(1, $body['total']);
        self::assertSame(
            [
                'id' => 'item-01',
                'name' => 'Anvil',
                'price' => ['amount' => 5, 'currency' => 'GBP'],
                'createdAt' => '2026-01-01T09:00:00+00:00',
                'category' => ['id' => 'workshop', 'name' => 'Workshop'],
            ],
            $body['items'][0],
        );
    }

    public function testListWithMaxPriceZeroReturnsEmpty(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/items?maxPrice=0');

        self::assertResponseIsSuccessful();
        $body = json_decode((string) $client->getResponse()->getContent(), true);
        self::assertSame([], $body['items']);
        self::assertSame(0, $body['total']);
    }

    public function testListWithInvalidMaxPriceReturns400(): void
    {
        $client = static::createClient();

        foreach (['abc', '-1'] as $maxPrice) {
            $client->request('GET', '/api/items?maxPrice=' . $maxPrice);

            self::assertResponseStatusCodeSame(400);
            $body = json_decode((string) $client->getResponse()->getContent(), true);
            self::assertSame('maxPrice must be a non-negative number', $body['error']);
        }
    }
}
