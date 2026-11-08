<?php

declare(strict_types=1);

namespace App\Tests\Module\Task;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class TaskControllerTest extends WebTestCase
{
    public function testListReturnsFirstPageByDefault(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/tasks');

        self::assertResponseIsSuccessful();
        $body = json_decode((string) $client->getResponse()->getContent(), true);

        self::assertCount(10, $body['items']);
        self::assertSame(1, $body['page']);
        self::assertSame(10, $body['perPage']);
        self::assertSame(12, $body['total']);
        self::assertSame(
            [
                'id' => 'task-01',
                'title' => 'Plan the sprint',
                'isDone' => true,
                'createdAt' => '2026-02-01T10:00:00+00:00',
            ],
            $body['items'][0],
        );
    }

    public function testListWithPageAndPerPage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/tasks?page=2&perPage=5');

        self::assertResponseIsSuccessful();
        $body = json_decode((string) $client->getResponse()->getContent(), true);

        self::assertCount(5, $body['items']);
        self::assertSame(2, $body['page']);
        self::assertSame(5, $body['perPage']);
        self::assertSame(12, $body['total']);
        self::assertSame('task-06', $body['items'][0]['id']);
    }

    public function testListCapsPerPageAndTreatsLowPageAsOne(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/tasks?page=0&perPage=100');

        self::assertResponseIsSuccessful();
        $body = json_decode((string) $client->getResponse()->getContent(), true);

        self::assertSame(1, $body['page']);
        self::assertSame(50, $body['perPage']);
        self::assertSame(12, $body['total']);
    }

    public function testGetReturnsOneTask(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/tasks/task-03');

        self::assertResponseIsSuccessful();
        $task = json_decode((string) $client->getResponse()->getContent(), true);

        self::assertSame(
            [
                'id' => 'task-03',
                'title' => 'Build the controller',
                'isDone' => true,
                'createdAt' => '2026-02-03T10:00:00+00:00',
            ],
            $task,
        );
    }

    public function testGetUnknownTaskIs404(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/tasks/nope');

        self::assertResponseStatusCodeSame(404);
        self::assertResponseHeaderSame('content-type', 'application/json');
        self::assertSame(['error' => 'Task not found'], json_decode((string) $client->getResponse()->getContent(), true));
    }
}
