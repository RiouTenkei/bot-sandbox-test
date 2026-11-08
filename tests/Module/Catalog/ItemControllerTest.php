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
    }
}
