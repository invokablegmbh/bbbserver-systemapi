<?php

declare(strict_types=1);

namespace BbbServer\SystemApiConnector\Tests\Integration;

use BbbServer\SystemApiConnector\Tests\Integration\Support\IntegrationTestCase;

/**
 * @group integration
 */
final class ConferenceRoomsIntegrationTest extends IntegrationTestCase
{
    private static ?string $createdRoomId = null;
    private static bool $roomDeleted = false;

    public function testListConferenceRoomsEndpoint(): void
    {
        $responsePayload = $this->callEndpointOrSkipFeature(
            fn (): array => $this->connector()->conferenceRooms()->listConferenceRooms(),
            'GET /conference-rooms/list'
        );

        self::assertIsArray($responsePayload);
    }

    public function testCreateConferenceRoomEndpoint(): string
    {
        $responsePayload = $this->callEndpointOrSkipFeature(
            fn (): array => $this->connector()->conferenceRooms()->createConferenceRoom([
                'name' => 'SDK Room Test ' . date('YmdHis'),
            ]),
            'POST /conference-rooms/create'
        );

        self::assertIsArray($responsePayload);

        $roomId = $this->extractIdentifier($responsePayload, ['roomId', 'id']);
        self::assertNotNull($roomId);

        self::$createdRoomId = $roomId;

        return $roomId;
    }

    /**
     * @depends testCreateConferenceRoomEndpoint
     */
    public function testGetConferenceRoomEndpoint(string $roomId): string
    {
        $responsePayload = $this->callEndpointOrSkipFeature(
            fn (): array => $this->connector()->conferenceRooms()->getConferenceRoom($roomId),
            'GET /conference-rooms/get'
        );

        self::assertIsArray($responsePayload);

        return $roomId;
    }

    /**
     * @depends testGetConferenceRoomEndpoint
     */
    public function testUpdateConferenceRoomSettingsEndpoint(string $roomId): string
    {
        $responsePayload = $this->callEndpointOrSkipFeature(
            fn (): array => $this->connector()->conferenceRooms()->updateConferenceRoomSettings([
                'roomId' => $roomId,
                'name' => 'SDK Room Test Updated ' . date('YmdHis'),
                'quickConnectAskModeratorForQuickJoin' => 'true',
            ]),
            'POST /conference-rooms/settings'
        );

        self::assertIsArray($responsePayload);

        return $roomId;
    }

    /**
     * @depends testUpdateConferenceRoomSettingsEndpoint
     */
    public function testDeleteConferenceRoomEndpoint(string $roomId): void
    {
        $responsePayload = $this->callEndpointOrSkipFeature(
            fn (): array => $this->connector()->conferenceRooms()->deleteConferenceRoom($roomId),
            'GET /conference-rooms/delete'
        );

        self::assertIsArray($responsePayload);
        self::$roomDeleted = true;
    }

    public static function tearDownAfterClass(): void
    {
        if (self::$createdRoomId === null || self::$roomDeleted) {
            return;
        }

        $baseUrl = getenv('BBBSERVER_SYSTEMAPI_BASE_URL') ?: '';
        $apiKey = getenv('BBBSERVER_SYSTEMAPI_KEY') ?: '';

        if ($baseUrl === '' || $apiKey === '') {
            return;
        }

        try {
            (new \BbbServer\SystemApiConnector\SystemApiConnector($baseUrl, $apiKey))
                ->conferenceRooms()
                ->deleteConferenceRoom(self::$createdRoomId);
        } catch (\Throwable) {
        }
    }
}
