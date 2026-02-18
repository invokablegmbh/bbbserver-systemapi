<?php

declare(strict_types=1);

namespace BbbServer\SystemApiConnector\Domain;

use BbbServer\SystemApiConnector\Http\JsonHttpClient;

final class RecordingsClient extends AbstractResourceClient
{
    public function __construct(JsonHttpClient $jsonHttpClient)
    {
        parent::__construct($jsonHttpClient, '/recordings');
    }

    /**
     * GET /recordings/list
     *
     * Get recordings for a room.
     *
     * Required query keys (API):
     * - roomId: string
     *
     * @param array $query Query parameters. Include at least roomId.
     */
    public function listRecordings(array $query = []): array
    {
        return $this->request('GET', '/list', $query);
    }

    /**
     * GET /recordings/list-by-conference
     *
     * Get recordings for a specific conference.
     *
     * @param string $conferenceId Required conference identifier.
     */
    public function listByConference(string $conferenceId): array
    {
        return $this->request('GET', '/list-by-conference', ['conferenceId' => $conferenceId]);
    }

    /**
     * GET /recordings/get
     *
     * Get a single recording by ID.
     *
     * @param string $recordingId Required recording identifier.
     */
    public function getRecording(string $recordingId): array
    {
        return $this->request('GET', '/get', ['recordingId' => $recordingId]);
    }

    /**
     * GET /recordings/delete
     *
     * Delete a recording by ID.
     *
     * @param string $recordingId Required recording identifier.
     */
    public function deleteRecording(string $recordingId): array
    {
        return $this->request('GET', '/delete', ['recordingId' => $recordingId]);
    }

    /**
     * GET /recordings/prepare-download
     *
     * Prepare recording download file conversion.
     *
     * @param string $recordingId Required recording identifier.
     */
    public function prepareDownload(string $recordingId): array
    {
        return $this->request('GET', '/prepare-download', ['recordingId' => $recordingId]);
    }
}
