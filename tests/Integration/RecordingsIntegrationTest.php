<?php

declare(strict_types=1);

namespace BbbServer\SystemApiConnector\Tests\Integration;

use BbbServer\SystemApiConnector\Tests\Integration\Support\IntegrationTestCase;

/**
 * @group integration
 */
final class RecordingsIntegrationTest extends IntegrationTestCase
{
    public function testListRecordingsEndpoint(): array
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
            self::markTestSkipped('No conference rooms available to query for recordings.');
        }

        $responsePayload = null;
        $roomId = null;

        // Search for a conference room that has recordings
        foreach ($rooms as $room) {
            if (
                !isset($room['id'])
                || !is_string($room['id'])
                || $room['id'] === ''
            ) {
                continue;
            }

            $currentRoomId = $room['id'];
            $recordings = $this->connector()->recordings()->listRecordings(['roomId' => $currentRoomId]);

            if (
                is_array($recordings)
                && isset($recordings['response'])
                && is_array($recordings['response'])
                && !empty($recordings['response'])
                && !empty($recordings['response'][0])
            ) {
                $roomId = $currentRoomId;
                $responsePayload = $recordings['response'][0];

                break;
            }
        }

        if ($roomId === null || $responsePayload === null) {
            self::markTestSkipped('No conference room with recordings available.');
        }

        self::assertIsArray($responsePayload);

        $recordingId = $this->extractIdentifier($responsePayload, ['recordingId', 'id']);
        $conferenceId = $this->extractIdentifier($responsePayload, ['conferenceId']);
        $downloadFileState = $this->extractIdentifier($responsePayload, ['download_file_state']);

        return [
            'recordingId' => $recordingId,
            'conferenceId' => $conferenceId,
            'downloadFileState' => $downloadFileState,
        ];
    }

    /**
     * @depends testListRecordingsEndpoint
     */
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

    /**
     * @depends testListByConferenceEndpoint
     */
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

    /**
     * @depends testGetRecordingEndpoint
     */
    public function testPrepareDownloadEndpoint(array $state): array
    {
        if (!is_string($state['recordingId']) || $state['recordingId'] === '') {
            self::markTestSkipped('No recordingId available for prepare download endpoint.');
        }

        if (is_string($state['downloadFileState'] ?? null) && ($state['downloadFileState'] === 'PREPARING' || $state['downloadFileState'] === 'READY')) {
            self::markTestSkipped('Recording already in preparing state, skipping prepare download endpoint test to avoid conflicts.');
        }

        $responsePayload = $this->callEndpointOrSkipFeature(
            fn (): array => $this->connector()->recordings()->prepareDownload($state['recordingId']),
            'GET /recordings/prepare-download'
        );

        self::assertIsArray($responsePayload);

        return $state;
    }

    /**
     * @depends testGetRecordingEndpoint
     */
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
