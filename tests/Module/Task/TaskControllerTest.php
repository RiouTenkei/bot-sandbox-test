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

        self::assertCount(9, $body['items']);
        self::assertSame(1, $body['page']);
        self::assertSame(20, $body['perPage']);
        self::assertSame(9, $body['total']);
        self::assertSame(
            [
                'id' => 'task-04',
                'title' => 'Wire the repository',
                'isDone' => false,
                'createdAt' => '2026-02-04T10:00:00+00:00',
                'parentId' => null,
                'children' => [
                    [
                        'id' => 'task-05',
                        'title' => 'Add the view layer',
                        'isDone' => false,
                        'createdAt' => '2026-02-05T10:00:00+00:00',
                        'parentId' => 'task-04',
                        'children' => [],
                    ],
                    [
                        'id' => 'task-06',
                        'title' => 'Run the suite',
                        'isDone' => false,
                        'createdAt' => '2026-02-06T10:00:00+00:00',
                        'parentId' => 'task-04',
                        'children' => [],
                    ],
                ],
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

        self::assertCount(4, $body['items']);
        self::assertSame(2, $body['page']);
        self::assertSame(5, $body['perPage']);
        self::assertSame(9, $body['total']);
        self::assertSame('task-09', $body['items'][0]['id']);
    }

    public function testListCapsPerPageAndTreatsLowPageAsOne(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/tasks?page=0&perPage=100');

        self::assertResponseIsSuccessful();
        $body = json_decode((string) $client->getResponse()->getContent(), true);

        self::assertSame(1, $body['page']);
        self::assertSame(50, $body['perPage']);
        self::assertSame(9, $body['total']);
    }

    public function testListWithDoneTrueReturnsOnlyFinishedTasks(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/tasks?done=true');

        self::assertResponseIsSuccessful();
        $body = json_decode((string) $client->getResponse()->getContent(), true);

        self::assertCount(3, $body['items']);
        self::assertSame(3, $body['total']);
        foreach ($body['items'] as $item) {
            self::assertTrue($item['isDone']);
        }
    }

    public function testListWithDoneFalseReturnsOnlyOpenTasks(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/tasks?done=false');

        self::assertResponseIsSuccessful();
        $body = json_decode((string) $client->getResponse()->getContent(), true);

        self::assertCount(9, $body['items']);
        self::assertSame(9, $body['total']);
        foreach ($body['items'] as $item) {
            self::assertFalse($item['isDone']);
        }
    }

    public function testListWithInvalidDoneReturns400(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/tasks?done=maybe');

        self::assertResponseStatusCodeSame(400);
        self::assertSame(['error' => 'done must be true or false'], json_decode((string) $client->getResponse()->getContent(), true));
    }

    public function testListWithDoneRespectsPagination(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/tasks?done=false&page=2&perPage=5');

        self::assertResponseIsSuccessful();
        $body = json_decode((string) $client->getResponse()->getContent(), true);

        self::assertCount(4, $body['items']);
        self::assertSame(2, $body['page']);
        self::assertSame(5, $body['perPage']);
        self::assertSame(9, $body['total']);
        foreach ($body['items'] as $item) {
            self::assertFalse($item['isDone']);
        }
    }

    public function testCountReturnsTotals(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/tasks/count');

        self::assertResponseIsSuccessful();
        self::assertSame(
            ['total' => 12, 'done' => 3],
            json_decode((string) $client->getResponse()->getContent(), true),
        );
    }

    public function testListSortByTitle(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/tasks?sort=title');

        self::assertResponseIsSuccessful();
        $body = json_decode((string) $client->getResponse()->getContent(), true);

        self::assertCount(9, $body['items']);
        self::assertSame('Add the view layer', $body['items'][0]['title']);
        $titles = array_column($body['items'], 'title');
        $sorted = $titles;
        sort($sorted, SORT_STRING);
        self::assertSame($sorted, $titles);
    }

    public function testListSortByTitleDesc(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/tasks?sort=-title');

        self::assertResponseIsSuccessful();
        $body = json_decode((string) $client->getResponse()->getContent(), true);

        self::assertCount(9, $body['items']);
        self::assertSame('Write the retrospective', $body['items'][0]['title']);
        $titles = array_column($body['items'], 'title');
        $sorted = $titles;
        rsort($sorted, SORT_STRING);
        self::assertSame($sorted, $titles);
    }

    public function testListDefaultSortIsId(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/tasks');

        self::assertResponseIsSuccessful();
        $body = json_decode((string) $client->getResponse()->getContent(), true);

        self::assertSame('task-04', $body['items'][0]['id']);
    }

    public function testListUnknownSortIs400(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/tasks?sort=colour');

        self::assertResponseStatusCodeSame(400);
        self::assertStringContainsString('id, title, -title', (string) $client->getResponse()->getContent());
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
                'parentId' => 'task-01',
                'children' => [],
            ],
            $task,
        );
    }

    public function testGetParentTaskIncludesChildren(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/tasks/task-01');

        self::assertResponseIsSuccessful();
        $task = json_decode((string) $client->getResponse()->getContent(), true);

        self::assertSame('task-01', $task['id']);
        self::assertNull($task['parentId']);
        self::assertCount(2, $task['children']);
        self::assertSame('task-02', $task['children'][0]['id']);
        self::assertSame('task-03', $task['children'][1]['id']);
        self::assertSame('task-01', $task['children'][0]['parentId']);
        self::assertSame('task-01', $task['children'][1]['parentId']);
        self::assertSame([], $task['children'][0]['children']);
    }

    public function testGetChildTaskIncludesParentId(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/tasks/task-02');

        self::assertResponseIsSuccessful();
        $task = json_decode((string) $client->getResponse()->getContent(), true);

        self::assertSame(
            [
                'id' => 'task-02',
                'title' => 'Write the tests',
                'isDone' => true,
                'createdAt' => '2026-02-02T10:00:00+00:00',
                'parentId' => 'task-01',
                'children' => [],
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
