<?php

declare(strict_types=1);

namespace BbbServer\SystemApiConnector\Tests\Integration;

use BbbServer\SystemApiConnector\Tests\Integration\Support\IntegrationTestCase;

/**
 * @group integration
 */
final class UserAttendanceIntegrationTest extends IntegrationTestCase
{
    public function testListUserAttendanceEndpoint(): ?string
    {
        $roomsResponsePayload = $this->callEndpointOrSkipFeature(
            fn (): array => $this->connector()->conferenceRooms()->listConferenceRooms(),
            'GET /conference-rooms/list'
        );

        $rooms = [];
        if (
            isset($roomsResponsePayload['response'])
            && is_array($roomsResponsePayload['response'])
        ) {
            $rooms = $roomsResponsePayload['response'];
        }

        if (empty($rooms)) {
            self::markTestSkipped('No conference rooms available to query for user attendance.');
        }

        $responsePayload = null;
        $roomId = null;

        // Search for a conference room that has user attendance data
        foreach ($rooms as $room) {
            if (
                !isset($room['id'])
                || !is_string($room['id'])
                || $room['id'] === ''
            ) {
                continue;
            }

            $currentRoomId = $room['id'];
            $attendance = $this->connector()->userAttendance()->listUserAttendance(['roomId' => $currentRoomId]);

            if (
                is_array($attendance)
                && isset($attendance['response'])
                && is_array($attendance['response'])
                && !empty($attendance['response'])
                && !empty($attendance['response'][0])
            ) {
                $roomId = $currentRoomId;
                $responsePayload = $attendance['response'][0];
                break;
            }
        }

        if ($roomId === null || $responsePayload === null) {
            self::markTestSkipped('No conference room with user attendance data available.');
        }

        self::assertIsArray($responsePayload);

        return $this->extractIdentifier($responsePayload, ['conferenceUuid']);
    }

    /**
     * @depends testListUserAttendanceEndpoint
     */
    public function testGetUserAttendanceEndpoint(?string $conferenceId): ?string
    {
        if (!is_string($conferenceId) || $conferenceId === '') {
            self::markTestSkipped('No conferenceId available for get user attendance endpoint.');
        }

        $responsePayload = $this->callEndpointOrSkipFeature(
            fn (): array => $this->connector()->userAttendance()->getUserAttendance($conferenceId),
            'GET /user-attendence/get'
        );

        self::assertIsArray($responsePayload);

        return $conferenceId;
    }

    /**
     * @depends testGetUserAttendanceEndpoint
     */
    public function testDeleteUserAttendanceEndpoint(?string $conferenceId): void
    {
        $syntheticConferenceId = '00000000-0000-0000-0000-000000000000';

        $responsePayload = $this->callEndpointOrSkipFeature(
            fn (): array => $this->connector()->userAttendance()->deleteUserAttendance($syntheticConferenceId),
            'GET /user-attendence/delete'
        );

        self::assertIsArray($responsePayload);
    }
}
