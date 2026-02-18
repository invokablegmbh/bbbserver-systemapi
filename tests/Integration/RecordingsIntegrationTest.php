<?php

declare(strict_types=1);

namespace BbbServer\SystemApiConnector\Tests\Integration;

use BbbServer\SystemApiConnector\Tests\Integration\Support\IntegrationTestCase;
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\Group;

#[Group('integration')]
final class RecordingsIntegrationTest extends IntegrationTestCase
{
    public function testListRecordingsEndpoint(): array
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
            self::markTestSkipped('No conference room available to query recordings.');
        }

        $responsePayload = $this->callEndpointOrSkipFeature(
            fn (): array => $this->connector()->recordings()->listRecordings(['roomId' => $roomId]),
            'GET /recordings/list'
        );

        self::assertIsArray($responsePayload);

        $recordingId = $this->extractIdentifier($responsePayload, ['recordingId', 'id']);
        $conferenceId = $this->extractIdentifier($responsePayload, ['conferenceId']);

        return [
            'recordingId' => $recordingId,
            'conferenceId' => $conferenceId,
        ];
    }

    #[Depends('testListRecordingsEndpoint')]
    public function testListByConferenceEndpoint(array $state): array
    {
        if (!is_string($state['conferenceId']) || $state['conferenceId'] === '') {
            self::markTestSkipped('No conferenceId available in recordings payload for list-by-conference endpoint.');
        }

        $responsePayload = $this->callEndpointOrSkipFeature(
            fn (): array => $this->connector()->recordings()->listByConference($state['conferenceId']),
            'GET /recordings/list-by-conference'
        );

        self::assertIsArray($responsePayload);

        return $state;
    }

    #[Depends('testListByConferenceEndpoint')]
    public function testGetRecordingEndpoint(array $state): array
    {
        if (!is_string($state['recordingId']) || $state['recordingId'] === '') {
            self::markTestSkipped('No recordingId available for get recording endpoint.');
        }

        $responsePayload = $this->callEndpointOrSkipFeature(
            fn (): array => $this->connector()->recordings()->getRecording($state['recordingId']),
            'GET /recordings/get'
        );

        self::assertIsArray($responsePayload);

        return $state;
    }

    #[Depends('testGetRecordingEndpoint')]
    public function testPrepareDownloadEndpoint(array $state): array
    {
        if (!is_string($state['recordingId']) || $state['recordingId'] === '') {
            self::markTestSkipped('No recordingId available for prepare download endpoint.');
        }

        $responsePayload = $this->callEndpointOrSkipFeature(
            fn (): array => $this->connector()->recordings()->prepareDownload($state['recordingId']),
            'GET /recordings/prepare-download'
        );

        self::assertIsArray($responsePayload);

        return $state;
    }

    #[Depends('testPrepareDownloadEndpoint')]
    public function testDeleteRecordingEndpoint(array $state): void
    {
        $recordingId = '00000000-0000-0000-0000-000000000000';

        $responsePayload = $this->callEndpointOrSkipFeature(
            fn (): array => $this->connector()->recordings()->deleteRecording($recordingId),
            'GET /recordings/delete'
        );

        self::assertIsArray($responsePayload);
    }
}
