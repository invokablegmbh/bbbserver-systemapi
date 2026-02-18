<?php

declare(strict_types=1);

namespace BbbServer\SystemApiConnector\Tests\Unit;

use BbbServer\SystemApiConnector\Domain\ConferenceRoomsClient;
use BbbServer\SystemApiConnector\Http\ApiResponse;
use BbbServer\SystemApiConnector\Http\JsonHttpClient;
use BbbServer\SystemApiConnector\Tests\Support\FakeHttpTransport;
use PHPUnit\Framework\TestCase;

final class AbstractResourceClientTest extends TestCase
{
    public function testConferenceRoomsTypedMethodsUseExpectedPathsAndHttpMethods(): void
    {
        $fakeHttpTransport = new FakeHttpTransport();
        $fakeHttpTransport->queueResponse(new ApiResponse(200, [], '{"list":[]}'));
        $fakeHttpTransport->queueResponse(new ApiResponse(200, [], '{"item":{"id":7}}'));
        $fakeHttpTransport->queueResponse(new ApiResponse(200, [], '{"created":true}'));
        $fakeHttpTransport->queueResponse(new ApiResponse(200, [], '{"updated":true}'));
        $fakeHttpTransport->queueResponse(new ApiResponse(204, [], ''));
        $fakeHttpTransport->queueResponse(new ApiResponse(200, [], '{"fallback":true}'));

        $jsonHttpClient = new JsonHttpClient('https://example.test/api', 'api-key-value', $fakeHttpTransport);
        $conferenceRoomsClient = new ConferenceRoomsClient($jsonHttpClient);

        $conferenceRoomsClient->listConferenceRooms(['page' => 1]);
        self::assertSame('/conference-rooms/list', $fakeHttpTransport->lastRequest()?->path);
        self::assertSame('GET', $fakeHttpTransport->lastRequest()?->method);

        $conferenceRoomsClient->getConferenceRoom('room-id');
        self::assertSame('/conference-rooms/get', $fakeHttpTransport->lastRequest()?->path);
        self::assertSame(['roomId' => 'room-id'], $fakeHttpTransport->lastRequest()?->query);

        $conferenceRoomsClient->createConferenceRoom(['name' => 'My Room']);
        self::assertSame('POST', $fakeHttpTransport->lastRequest()?->method);
        self::assertSame('/conference-rooms/create', $fakeHttpTransport->lastRequest()?->path);

        $conferenceRoomsClient->updateConferenceRoomSettings(['roomId' => 'room-id', 'name' => 'New Name']);
        self::assertSame('POST', $fakeHttpTransport->lastRequest()?->method);
        self::assertSame('/conference-rooms/settings', $fakeHttpTransport->lastRequest()?->path);

        $conferenceRoomsClient->deleteConferenceRoom('room-id');
        self::assertSame('GET', $fakeHttpTransport->lastRequest()?->method);
        self::assertSame('/conference-rooms/delete', $fakeHttpTransport->lastRequest()?->path);

        $conferenceRoomsClient->request('GET', '/new-endpoint', ['flag' => 1]);
        self::assertSame('/conference-rooms/new-endpoint', $fakeHttpTransport->lastRequest()?->path);
        self::assertSame('GET', $fakeHttpTransport->lastRequest()?->method);
    }
}
