<?php

declare(strict_types=1);

namespace BbbServer\SystemApiConnector\Tests\Unit;

use BbbServer\SystemApiConnector\Domain\AiSummariesClient;
use BbbServer\SystemApiConnector\Http\ApiResponse;
use BbbServer\SystemApiConnector\Http\JsonHttpClient;
use BbbServer\SystemApiConnector\Tests\Support\FakeHttpTransport;
use PHPUnit\Framework\TestCase;

final class AiSummariesClientTest extends TestCase
{
    private FakeHttpTransport $fakeHttpTransport;
    private AiSummariesClient $aiSummariesClient;

    protected function setUp(): void
    {
        $this->fakeHttpTransport = new FakeHttpTransport();
        $jsonHttpClient = new JsonHttpClient('https://example.test/api', 'api-key-value', $this->fakeHttpTransport);
        $this->aiSummariesClient = new AiSummariesClient($jsonHttpClient);
    }

    public function testListAiSummariesUsesCorrectPathAndMethod(): void
    {
        $this->fakeHttpTransport->queueResponse(new ApiResponse(200, [], '{"success":true,"response":[]}'));

        $this->aiSummariesClient->listAiSummaries(['roomId' => 'room-123']);

        self::assertSame('GET', $this->fakeHttpTransport->lastRequest()->method);
        self::assertSame('/ai-summaries/list', $this->fakeHttpTransport->lastRequest()->path);
        self::assertSame(['roomId' => 'room-123'], $this->fakeHttpTransport->lastRequest()->query);
    }

    public function testGetAiSummaryUsesCorrectPathAndMethod(): void
    {
        $this->fakeHttpTransport->queueResponse(new ApiResponse(200, [], '{"success":true,"response":{}}'));

        $this->aiSummariesClient->getAiSummary('conf-456');

        self::assertSame('GET', $this->fakeHttpTransport->lastRequest()->method);
        self::assertSame('/ai-summaries/get', $this->fakeHttpTransport->lastRequest()->path);
        self::assertSame(['conferenceId' => 'conf-456'], $this->fakeHttpTransport->lastRequest()->query);
    }

    public function testDeleteAiSummaryUsesCorrectPathAndMethod(): void
    {
        $this->fakeHttpTransport->queueResponse(new ApiResponse(200, [], '{"success":true,"response":[]}'));

        $this->aiSummariesClient->deleteAiSummary('conf-789');

        self::assertSame('GET', $this->fakeHttpTransport->lastRequest()->method);
        self::assertSame('/ai-summaries/delete', $this->fakeHttpTransport->lastRequest()->path);
        self::assertSame(['conferenceId' => 'conf-789'], $this->fakeHttpTransport->lastRequest()->query);
    }
}
