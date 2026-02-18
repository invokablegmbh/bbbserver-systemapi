<?php

declare(strict_types=1);

namespace BbbServer\SystemApiConnector\Domain;

use BbbServer\SystemApiConnector\Http\JsonHttpClient;

final class UserAttendanceClient extends AbstractResourceClient
{
    public function __construct(JsonHttpClient $jsonHttpClient)
    {
        parent::__construct($jsonHttpClient, '/user-attendence');
    }

    /**
     * GET /user-attendence/list
     *
     * Get all attendance tracking records inside a room.
     *
     * Required query keys (API):
     * - roomId: string
     *
     * @param array $query Query parameters. Include at least roomId.
     */
    public function listUserAttendance(array $query = []): array
    {
        return $this->request('GET', '/list', $query);
    }

    /**
     * GET /user-attendence/get
     *
     * Get attendance tracking by conference ID.
     *
     * @param string $conferenceId Required conference identifier.
     */
    public function getUserAttendance(string $conferenceId): array
    {
        return $this->request('GET', '/get', ['conferenceId' => $conferenceId]);
    }

    /**
     * GET /user-attendence/delete
     *
     * Delete attendance tracking by conference ID.
     *
     * @param string $conferenceId Required conference identifier.
     */
    public function deleteUserAttendance(string $conferenceId): array
    {
        return $this->request('GET', '/delete', ['conferenceId' => $conferenceId]);
    }
}
