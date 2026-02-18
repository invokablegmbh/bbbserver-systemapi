<?php

declare(strict_types=1);

namespace BbbServer\SystemApiConnector\Tests\Unit;

use BbbServer\SystemApiConnector\Configuration\SystemApiConfiguration;
use BbbServer\SystemApiConnector\Domain\ConferenceRoomsClient;
use BbbServer\SystemApiConnector\Domain\ConferencesClient;
use BbbServer\SystemApiConnector\Domain\InvoicesClient;
use BbbServer\SystemApiConnector\Domain\ModeratorGroupsClient;
use BbbServer\SystemApiConnector\Domain\ModeratorsClient;
use BbbServer\SystemApiConnector\Domain\OthersClient;
use BbbServer\SystemApiConnector\Domain\PartnerClient;
use BbbServer\SystemApiConnector\Domain\RecordingsClient;
use BbbServer\SystemApiConnector\Domain\UserAttendanceClient;
use BbbServer\SystemApiConnector\Http\ApiResponse;
use BbbServer\SystemApiConnector\SystemApiConnector;
use BbbServer\SystemApiConnector\Tests\Support\FakeHttpTransport;
use PHPUnit\Framework\TestCase;

final class SystemApiConnectorTest extends TestCase
{
    public function testConnectorExposesAllResourceClients(): void
    {
        $fakeHttpTransport = new FakeHttpTransport();
        $connector = new SystemApiConnector('https://example.test/api', 'api-key-value', $fakeHttpTransport);

        self::assertInstanceOf(ConferenceRoomsClient::class, $connector->conferenceRooms());
        self::assertInstanceOf(ModeratorGroupsClient::class, $connector->moderatorGroups());
        self::assertInstanceOf(ConferencesClient::class, $connector->conferences());
        self::assertInstanceOf(UserAttendanceClient::class, $connector->userAttendance());
        self::assertInstanceOf(InvoicesClient::class, $connector->invoices());
        self::assertInstanceOf(ModeratorsClient::class, $connector->moderators());
        self::assertInstanceOf(RecordingsClient::class, $connector->recordings());
        self::assertInstanceOf(PartnerClient::class, $connector->partner());
        self::assertInstanceOf(OthersClient::class, $connector->others());
        self::assertSame($connector->conferenceRooms(), $connector->conferenceRooms());
    }

    public function testForBbbserverFactoryBuildsLanguageAwareBaseUrl(): void
    {
        $fakeHttpTransport = new FakeHttpTransport();
        $fakeHttpTransport->queueResponse(new ApiResponse(200, [], '{"ok":true}'));

        $connector = SystemApiConnector::forBbbserver('api-key-value', 'en', $fakeHttpTransport, 'https://app.bbbserver.de');
        $connector->request('GET', '/');

        self::assertSame('https://app.bbbserver.de/en/bbb-system-api', $fakeHttpTransport->lastBaseUrl());
    }

    public function testForBbbserverFactoryFallsBackToGermanLanguage(): void
    {
        $fakeHttpTransport = new FakeHttpTransport();
        $fakeHttpTransport->queueResponse(new ApiResponse(200, [], '{"ok":true}'));

        $connector = SystemApiConnector::forBbbserver('api-key-value', '', $fakeHttpTransport, 'https://app.bbbserver.de');
        $connector->request('GET', '/');

        self::assertSame('https://app.bbbserver.de/de/bbb-system-api', $fakeHttpTransport->lastBaseUrl());
    }

    public function testConnectorCanBeCreatedFromConfigurationObject(): void
    {
        $fakeHttpTransport = new FakeHttpTransport();
        $fakeHttpTransport->queueResponse(new ApiResponse(200, [], '{"ok":true}'));

        $configuration = new SystemApiConfiguration('https://custom.example.test/system-api', 'api-key-value');
        $connector = SystemApiConnector::fromConfiguration($configuration, $fakeHttpTransport);
        $connector->request('GET', '/');

        self::assertSame('https://custom.example.test/system-api', $fakeHttpTransport->lastBaseUrl());
        self::assertSame('https://custom.example.test/system-api', $connector->configuration()->baseUrl());
    }

    public function testOthersRootUsesBasePath(): void
    {
        $fakeHttpTransport = new FakeHttpTransport();
        $fakeHttpTransport->queueResponse(new ApiResponse(200, [], '{"name":"bbbserver"}'));

        $connector = new SystemApiConnector('https://example.test/api', 'api-key-value', $fakeHttpTransport);
        $payload = $connector->others()->root();

        self::assertSame('bbbserver', $payload['name']);
        self::assertSame('/', $fakeHttpTransport->lastRequest()->path);
    }
}
