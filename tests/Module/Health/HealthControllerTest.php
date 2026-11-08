<?php

declare(strict_types=1);

namespace App\Tests\Module\Health;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class HealthControllerTest extends WebTestCase
{
    /**
     * The health endpoint must report that the API is up so monitoring can use it.
     */
    public function testHealthReturnsOk(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/health');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');
        self::assertSame(['status' => 'ok'], json_decode((string) $client->getResponse()->getContent(), true));
    }
}
