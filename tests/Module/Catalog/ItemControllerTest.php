<?php

declare(strict_types=1);

namespace App\Tests\Module\Catalog;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ItemControllerTest extends WebTestCase
{
    public function testListReturnsAllItems(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/items');

        self::assertResponseIsSuccessful();
        $items = json_decode((string) $client->getResponse()->getContent(), true);
        self::assertCount(23, $items);
        self::assertSame(['id' => 'item-01', 'name' => 'Anvil', 'price' => ['amount' => 5, 'currency' => 'GBP'], 'createdAt' => '2026-01-01T09:00:00+00:00'], $items[0]);
    }

    public function testGetReturnsOneItem(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/items/item-08');

        self::assertResponseIsSuccessful();
        $item = json_decode((string) $client->getResponse()->getContent(), true);
        self::assertSame('Hammer', $item['name']);
        self::assertSame(13.75, $item['price']['amount']);
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
    }

    public function testListFiltersByMaxPrice(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/items?maxPrice=6');

        self::assertResponseIsSuccessful();
        $items = json_decode((string) $client->getResponse()->getContent(), true);
        self::assertCount(1, $items);
        self::assertSame(['id' => 'item-01', 'name' => 'Anvil', 'price' => ['amount' => 5, 'currency' => 'GBP'], 'createdAt' => '2026-01-01T09:00:00+00:00'], $items[0]);
    }

    public function testListWithMaxPriceZeroReturnsEmpty(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/items?maxPrice=0');

        self::assertResponseIsSuccessful();
        $items = json_decode((string) $client->getResponse()->getContent(), true);
        self::assertSame([], $items);
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
