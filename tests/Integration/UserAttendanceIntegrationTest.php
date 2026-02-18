<?php

declare(strict_types=1);

namespace BbbServer\SystemApiConnector\Tests\Integration;

use BbbServer\SystemApiConnector\Tests\Integration\Support\IntegrationTestCase;
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\Group;

#[Group('integration')]
final class UserAttendanceIntegrationTest extends IntegrationTestCase
{
    public function testListUserAttendanceEndpoint(): ?string
    {
        $roomsResponsePayload = $this->callEndpointOrSkipFeature(
            fn (): array => $this->connector()->conferenceRooms()->listConferenceRooms(),
            'GET /conference-rooms/list'
        );

        $roomId = null;
        if (
            isset($roomsResponsePayload['response'])
            && is_array($roomsResponsePayload['response'])
            && isset($roomsResponsePayload['response'][0])
            && is_array($roomsResponsePayload['response'][0])
            && isset($roomsResponsePayload['response'][0]['id'])
            && is_string($roomsResponsePayload['response'][0]['id'])
            && $roomsResponsePayload['response'][0]['id'] !== ''
        ) {
            $roomId = $roomsResponsePayload['response'][0]['id'];
        }

        if ($roomId === null) {
            $roomId = $this->extractIdentifier($roomsResponsePayload, ['roomId', 'id']);
        }

        if ($roomId === null) {
            self::markTestSkipped('No conference room available to query user attendance.');
        }

        $responsePayload = $this->callEndpointOrSkipFeature(
            fn (): array => $this->connector()->userAttendance()->listUserAttendance(['roomId' => $roomId]),
            'GET /user-attendence/list'
        );

        self::assertIsArray($responsePayload);

        return $this->extractIdentifier($responsePayload, ['conferenceId']);
    }

    #[Depends('testListUserAttendanceEndpoint')]
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

    #[Depends('testGetUserAttendanceEndpoint')]
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
