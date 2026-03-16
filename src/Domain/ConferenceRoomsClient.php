<?php

declare(strict_types=1);

namespace BbbServer\SystemApiConnector\Domain;

use BbbServer\SystemApiConnector\Http\JsonHttpClient;

final class ConferenceRoomsClient extends AbstractResourceClient
{
    public function __construct(JsonHttpClient $jsonHttpClient)
    {
        parent::__construct($jsonHttpClient, '/conference-rooms');
    }

    /**
     * GET /conference-rooms/list
     *
     * Get a list of all existing conference rooms.
     *
     * @param array $query Optional query parameters.
     */
    public function listConferenceRooms(array $query = []): array
    {
        return $this->request('GET', '/list', $query);
    }

    /**
     * GET /conference-rooms/get
     *
     * Get a single conference room.
     *
     * @param string $roomId Required room identifier.
     */
    public function getConferenceRoom(string $roomId): array
    {
        return $this->request('GET', '/get', ['roomId' => $roomId]);
    }

    /**
     * POST /conference-rooms/create
     *
     * Create a new conference room for your account.
     *
     * Required payload keys:
     * - name: string
     */
    public function createConferenceRoom(array $conferenceRoomPayload): array
    {
        return $this->request('POST', '/create', [], $conferenceRoomPayload);
    }

    /**
     * POST /conference-rooms/settings
     *
     * Set conference room settings.
     *
     * Required payload keys:
     * - roomId: string
     *
     * Optional payload keys:
     * - name: string
     * - quickConnectAskModeratorForQuickJoin: bool|string("true"|"false")
     * - quickConnectParticipants: int > 0
     * - quickConnectDuration: int > 0 and < 1440
     */
    public function updateConferenceRoomSettings(array $settingsPayload): array
    {
        return $this->request('POST', '/settings', [], $settingsPayload);
    }

    /**
     * POST /conferences/personal-joins
     *
     * Create personal join links with predefined participant names for a QuickConnect conference room.
     *
     * Required payload keys:
     * - roomId: string (UUID of the conference room)
     * - names: string[]|json-string (participant names)
     * - type: int (0 = guestJoin, 1 = moderatorJoin)
     */
    public function personalJoins(array $personalJoinPayload): array
    {
        return $this->jsonHttpClient->post('/conferences/personal-joins', $personalJoinPayload);
    }

    /**
     * GET /conference-rooms/delete
     *
     * Delete a conference room.
     *
     * @param string $roomId Required room identifier.
     */
    public function deleteConferenceRoom(string $roomId): array
    {
        return $this->request('GET', '/delete', ['roomId' => $roomId]);
    }
}
