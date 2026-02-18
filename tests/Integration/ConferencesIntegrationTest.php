<?php

declare(strict_types=1);

namespace BbbServer\SystemApiConnector\Tests\Integration;

use BbbServer\SystemApiConnector\Tests\Integration\Support\IntegrationTestCase;

/**
 * @group integration
 */
final class ConferencesIntegrationTest extends IntegrationTestCase
{
    private static ?string $createdRoomId = null;
    private static ?string $createdConferenceId = null;
    private static bool $conferenceDeleted = false;
    private static bool $roomDeleted = false;

    public function testCreateConferenceRoomForConferenceEndpoints(): string
    {
        $responsePayload = $this->callEndpointOrSkipFeature(
            fn (): array => $this->connector()->conferenceRooms()->createConferenceRoom([
                'name' => 'SDK Conferences Test Room ' . date('YmdHis'),
            ]),
            'POST /conference-rooms/create'
        );

        $roomId = $this->extractIdentifier($responsePayload, ['roomId', 'id']);
        self::assertNotNull($roomId);

        self::$createdRoomId = $roomId;

        return $roomId;
    }

    /**
     * @depends testCreateConferenceRoomForConferenceEndpoints
     */
    public function testCreateConferenceEndpoint(string $roomId): array
    {
        $responsePayload = $this->callEndpointOrSkipFeature(
            fn (): array => $this->connector()->conferences()->createConference([
                'roomId' => $roomId,
                'name' => 'SDK Conference Test ' . date('YmdHis'),
                'maxConnections' => 2,
                'startTime' => date('Y-m-d H:i:s', strtotime('+1 day')),
                'duration' => 60,
                'askModeratorForGuestJoin' => 'true',
                'advancedSettings' => json_encode([
                    'moderatorOnlyMessage' => null,
                    'welcome' => null,
                    'lockSettingsDisableCam' => false,
                    'lockSettingsDisableMic' => false,
                    'lockSettingsDisablePrivateChat' => false,
                    'lockSettingsDisablePublicChat' => false,
                    'lockSettingsDisableNote' => false,
                    'lockSettingsHideUserList' => false,
                    'webcamsOnlyForModerator' => false,
                    'userdata-bbb_auto_swap_layout' => false,
                ], JSON_THROW_ON_ERROR),
                'userAttendenceDocumentation' => 0,
                'forceTerminationAfterDurationExceeded' => 'false',
            ]),
            'POST /conferences/create'
        );

        $conferenceId = $this->extractIdentifier($responsePayload, ['conferenceId', 'id']);
        self::assertNotNull($conferenceId);

        self::$createdConferenceId = $conferenceId;

        return ['roomId' => $roomId, 'conferenceId' => $conferenceId];
    }

    /**
     * @depends testCreateConferenceEndpoint
     */
    public function testListConferencesEndpoint(array $identifiers): array
    {
        $responsePayload = $this->callEndpointOrSkipFeature(
            fn (): array => $this->connector()->conferences()->listConferences(['roomId' => $identifiers['roomId']]),
            'GET /conferences/list'
        );

        self::assertIsArray($responsePayload);

        return $identifiers;
    }

    /**
     * @depends testListConferencesEndpoint
     */
    public function testGetConferenceEndpoint(array $identifiers): array
    {
        $responsePayload = $this->callEndpointOrSkipFeature(
            fn (): array => $this->connector()->conferences()->getConference($identifiers['conferenceId']),
            'GET /conferences/get'
        );

        self::assertIsArray($responsePayload);

        return $identifiers;
    }

    /**
     * @depends testGetConferenceEndpoint
     */
    public function testFindConferenceEndpoint(array $identifiers): array
    {
        $conferencePayload = $this->connector()->conferences()->getConference($identifiers['conferenceId']);

        $queryCandidate = $this->extractIdentifier($conferencePayload, ['moderatorJoinLink', 'guestJoinLink', 'joinLink']);
        if ($queryCandidate === null) {
            self::markTestSkipped('GET /conferences/find requires a join link in conference payload.');
        }

        $responsePayload = $this->callEndpointOrSkipFeature(
            fn (): array => $this->connector()->conferences()->findConference($queryCandidate),
            'GET /conferences/find'
        );

        self::assertIsArray($responsePayload);

        return $identifiers;
    }

    /**
     * @depends testFindConferenceEndpoint
     */
    public function testPersonalJoinsEndpoint(array $identifiers): array
    {
        $responsePayload = $this->callEndpointOrSkipFeature(
            fn (): array => $this->connector()->conferences()->personalJoins([
                'conferenceId' => $identifiers['conferenceId'],
                'names' => json_encode(['SDK User A', 'SDK User B'], JSON_THROW_ON_ERROR),
                'type' => 0,
            ]),
            'POST /conferences/personal-joins'
        );

        self::assertIsArray($responsePayload);

        return $identifiers;
    }

    /**
     * @depends testPersonalJoinsEndpoint
     */
    public function testUpdateConferenceEndpoint(array $identifiers): array
    {
        $responsePayload = $this->callEndpointOrSkipFeature(
            fn (): array => $this->connector()->conferences()->updateConference([
                'conferenceId' => $identifiers['conferenceId'],
                'name' => 'SDK Conference Test Updated ' . date('YmdHis'),
                'maxConnections' => 2,
                'startTime' => date('Y-m-d H:i:s', strtotime('+1 day +10 minutes')),
                'duration' => 30,
                'askModeratorForGuestJoin' => 'true',
                'advancedSettings' => json_encode([
                    'moderatorOnlyMessage' => 'Updated by integration test',
                ], JSON_THROW_ON_ERROR),
                'userAttendenceDocumentation' => 0,
            ]),
            'POST /conferences/update'
        );

        self::assertIsArray($responsePayload);

        return $identifiers;
    }

    /**
     * @depends testUpdateConferenceEndpoint
     */
    public function testDeleteConferenceEndpoint(array $identifiers): string
    {
        $responsePayload = $this->callEndpointOrSkipFeature(
            fn (): array => $this->connector()->conferences()->deleteConference($identifiers['conferenceId']),
            'GET /conferences/delete'
        );

        self::assertIsArray($responsePayload);
        self::$conferenceDeleted = true;

        return $identifiers['roomId'];
    }

    /**
     * @depends testDeleteConferenceEndpoint
     */
    public function testDeleteRoomAfterConferenceLifecycle(string $roomId): void
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
        $baseUrl = getenv('BBBSERVER_SYSTEMAPI_BASE_URL') ?: '';
        $apiKey = getenv('BBBSERVER_SYSTEMAPI_KEY') ?: '';

        if ($baseUrl === '' || $apiKey === '') {
            return;
        }

        $connector = new \BbbServer\SystemApiConnector\SystemApiConnector($baseUrl, $apiKey);

        if (!self::$conferenceDeleted && self::$createdConferenceId !== null) {
            try {
                $connector->conferences()->deleteConference(self::$createdConferenceId);
            } catch (\Throwable) {
            }
        }

        if (!self::$roomDeleted && self::$createdRoomId !== null) {
            try {
                $connector->conferenceRooms()->deleteConferenceRoom(self::$createdRoomId);
            } catch (\Throwable) {
            }
        }
    }
}
