<?php

declare(strict_types=1);

namespace BbbServer\SystemApiConnector\Domain;

use BbbServer\SystemApiConnector\Http\JsonHttpClient;

final class AiSummariesClient extends AbstractResourceClient
{
    public function __construct(JsonHttpClient $jsonHttpClient)
    {
        parent::__construct($jsonHttpClient, '/ai-summaries');
    }

    /**
     * GET /ai-summaries/list
     *
     * Get all AI summaries for conferences inside a room.
     *
     * Required query keys (API):
     * - roomId: string
     *
     * @param array $query Query parameters. Include at least roomId.
     */
    public function listAiSummaries(array $query = []): array
    {
        return $this->request('GET', '/list', $query);
    }

    /**
     * GET /ai-summaries/get
     *
     * Get the AI summary for a specific conference.
     *
     * @param string $conferenceId Required conference identifier.
     */
    public function getAiSummary(string $conferenceId): array
    {
        return $this->request('GET', '/get', ['conferenceId' => $conferenceId]);
    }

    /**
     * GET /ai-summaries/delete
     *
     * Delete the AI summary for a specific conference.
     *
     * @param string $conferenceId Required conference identifier.
     */
    public function deleteAiSummary(string $conferenceId): array
    {
        return $this->request('GET', '/delete', ['conferenceId' => $conferenceId]);
    }
}
