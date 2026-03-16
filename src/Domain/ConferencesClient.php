<?php

declare(strict_types=1);

namespace BbbServer\SystemApiConnector\Domain;

use BbbServer\SystemApiConnector\Http\JsonHttpClient;

final class ConferencesClient extends AbstractResourceClient
{
    public function __construct(JsonHttpClient $jsonHttpClient)
    {
        parent::__construct($jsonHttpClient, '/conferences');
    }

    /**
     * GET /conferences/list
     *
     * Get all existing conferences inside a room.
     *
     * Required query keys (API):
     * - roomId: string
     *
     * @param array $query Query parameters. Include at least roomId.
     */
    public function listConferences(array $query = []): array
    {
        return $this->request('GET', '/list', $query);
    }

    /**
     * GET /conferences/get
     *
     * Get a conference by ID.
     *
     * @param string $conferenceId Required conference identifier.
     */
    public function getConference(string $conferenceId): array
    {
        return $this->request('GET', '/get', ['conferenceId' => $conferenceId]);
    }

    /**
     * GET /conferences/find
     *
     * Find a conference by join link or other query string.
     *
     * @param string $query Required search input.
     */
    public function findConference(string $query): array
    {
        return $this->request('GET', '/find', ['query' => $query]);
    }

    /**
     * POST /conferences/create
     *
     * Create a conference inside a room.
     *
     * Required payload keys:
     * - roomId: string
     * - name: string
     * - maxConnections: int > 0
     * - startTime: string (YYYY-MM-DD hh:mm:ss)
     * - duration: int > 0 and < 1440
     * - askModeratorForGuestJoin: bool|string("true"|"false")
     * - advancedSettings: array|json-string
     * - userAttendenceDocumentation: int|bool
     *
     * Optional payload keys:
     * - forceTerminationAfterDurationExceeded: bool|string("true"|"false")
     */
    public function createConference(array $conferencePayload): array
    {
        return $this->request('POST', '/create', [], $conferencePayload);
    }

    /**
     * POST /conferences/update
     *
     * Update an existing conference.
     *
     * Required payload keys:
     * - conferenceId: string
     *
     * Optional payload keys:
     * - maxConnections: int > 0
     * - name: string
     * - startTime: string (YYYY-MM-DD hh:mm:ss)
     * - duration: int > 0 and < 1440
     * - askModeratorForGuestJoin: bool|string("true"|"false")
     * - advancedSettings: array|json-string
     * - userAttendenceDocumentation: int|bool
     */
    public function updateConference(array $conferencePayload): array
    {
        return $this->request('POST', '/update', [], $conferencePayload);
    }

    /**
     * POST /conferences/personal-joins
     *
     * Create personal join links with predefined participant names.
     *
     * Required payload keys:
     * - conferenceId: string
     * - names: string[]|json-string
     * - type: int (0 = guestJoin, 1 = moderatorJoin)
     */
    public function personalJoins(array $personalJoinPayload): array
    {
        return $this->request('POST', '/personal-joins', [], $personalJoinPayload);
    }

    /**
     * GET /conferences/delete
     *
     * Soft-delete a conference by setting terminatedAt.
     *
     * @param string $conferenceId Required conference identifier.
     */
    public function deleteConference(string $conferenceId): array
    {
        return $this->request('GET', '/delete', ['conferenceId' => $conferenceId]);
    }

    /**
     * POST /conferences/upload-slides
     *
     * Upload presentation slides (PDF) for a conference.
     *
     * @param string    $conferenceId Required conference identifier.
     * @param \CURLFile $slides       CURLFile instance pointing to the PDF file.
     */
    public function uploadSlides(string $conferenceId, \CURLFile $slides): array
    {
        return $this->jsonHttpClient->postMultipart('/conferences/upload-slides', [
            'conferenceId' => $conferenceId,
            'slides' => $slides,
        ]);
    }

    /**
     * GET /conferences/remove-slides
     *
     * Remove uploaded presentation slides from a conference.
     *
     * @param string $conferenceId Required conference identifier.
     */
    public function removeSlides(string $conferenceId): array
    {
        return $this->request('GET', '/remove-slides', ['conferenceId' => $conferenceId]);
    }

    /**
     * GET /conferences/download-slides
     *
     * Download uploaded presentation slides for a conference.
     *
     * @param string $conferenceId Required conference identifier.
     */
    public function downloadSlides(string $conferenceId): array
    {
        return $this->request('GET', '/download-slides', ['conferenceId' => $conferenceId]);
    }
}
