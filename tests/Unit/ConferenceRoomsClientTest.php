<?php

declare(strict_types=1);

namespace BbbServer\SystemApiConnector\Tests\Unit;

use BbbServer\SystemApiConnector\Domain\ConferenceRoomsClient;
use BbbServer\SystemApiConnector\Http\ApiResponse;
use BbbServer\SystemApiConnector\Http\JsonHttpClient;
use BbbServer\SystemApiConnector\Tests\Support\FakeHttpTransport;
use PHPUnit\Framework\TestCase;

final class ConferenceRoomsClientTest extends TestCase
{
    public function testPersonalJoinsUsesCorrectPathAndMethod(): void
    {
        $fakeHttpTransport = new FakeHttpTransport();
        $fakeHttpTransport->queueResponse(new ApiResponse(200, [], '{"links":[]}'));

        $jsonHttpClient = new JsonHttpClient('https://example.test/api', 'api-key-value', $fakeHttpTransport);
        $conferenceRoomsClient = new ConferenceRoomsClient($jsonHttpClient);

        $conferenceRoomsClient->personalJoins([
            'roomId' => 'room-id',
            'names' => '["Alice","Bob"]',
            'type' => '0',
        ]);

        self::assertSame('/conferences/personal-joins', $fakeHttpTransport->lastRequest()->path);
        self::assertSame('POST', $fakeHttpTransport->lastRequest()->method);
    }
}
