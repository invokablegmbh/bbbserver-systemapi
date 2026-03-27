<?php

declare(strict_types=1);

namespace BbbServer\SystemApiConnector\Tests\Integration;

use BbbServer\SystemApiConnector\Tests\Integration\Support\IntegrationTestCase;

/**
 * @group integration
 */
final class AiSummariesIntegrationTest extends IntegrationTestCase
{
    public function testListAiSummariesEndpoint(): array
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
            self::markTestSkipped('No conference rooms available to query for AI summaries.');
        }

        $responsePayload = null;
        $roomId = null;

        // Search for a conference room that has AI summaries
        foreach ($rooms as $room) {
            if (
                !isset($room['id'])
                || !is_string($room['id'])
                || $room['id'] === ''
            ) {
                continue;
            }

            $currentRoomId = $room['id'];
            $summaries = $this->connector()->aiSummaries()->listAiSummaries(['roomId' => $currentRoomId]);

            if (
                is_array($summaries)
                && isset($summaries['response'])
                && is_array($summaries['response'])
                && !empty($summaries['response'])
                && !empty($summaries['response'][0])
            ) {
                $roomId = $currentRoomId;
                $responsePayload = $summaries['response'][0];
                break;
            }
        }

        if ($roomId === null || $responsePayload === null) {
            self::markTestSkipped('No conference room with AI summaries available.');
        }

        self::assertIsArray($responsePayload);

        $conferenceId = $this->extractIdentifier($responsePayload, ['conferenceId']);

        return [
            'conferenceId' => $conferenceId,
        ];
    }

    /**
     * @depends testListAiSummariesEndpoint
     */
    public function testGetAiSummaryEndpoint(array $state): array
    {
        if (!is_string($state['conferenceId']) || $state['conferenceId'] === '') {
            self::markTestSkipped('No conferenceId available for get AI summary endpoint.');
        }

        $responsePayload = $this->callEndpointOrSkipFeature(
            fn (): array => $this->connector()->aiSummaries()->getAiSummary($state['conferenceId']),
            'GET /ai-summaries/get'
        );

        self::assertIsArray($responsePayload);

        return $state;
    }

    /**
     * @depends testGetAiSummaryEndpoint
     */
    public function testDeleteAiSummaryEndpoint(array $state): void
    {
        $conferenceId = '00000000-0000-0000-0000-000000000000';

        $responsePayload = $this->callEndpointOrSkipFeature(
            fn (): array => $this->connector()->aiSummaries()->deleteAiSummary($conferenceId),
            'GET /ai-summaries/delete'
        );

        self::assertIsArray($responsePayload);
    }
}
