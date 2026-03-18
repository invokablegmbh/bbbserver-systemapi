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
            self::markTestSkipped('No conference room available to query AI summaries.');
        }

        $responsePayload = $this->callEndpointOrSkipFeature(
            fn (): array => $this->connector()->aiSummaries()->listAiSummaries(['roomId' => $roomId]),
            'GET /ai-summaries/list'
        );

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
