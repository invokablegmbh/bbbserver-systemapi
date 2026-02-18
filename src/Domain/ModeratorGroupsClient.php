<?php

declare(strict_types=1);

namespace BbbServer\SystemApiConnector\Domain;

use BbbServer\SystemApiConnector\Http\JsonHttpClient;

final class ModeratorGroupsClient extends AbstractResourceClient
{
    public function __construct(JsonHttpClient $jsonHttpClient)
    {
        parent::__construct($jsonHttpClient, '/moderator-groups');
    }

    /**
     * GET /moderator-groups/list
     *
     * List all moderator groups.
     *
     * @param array $query Optional query parameters.
     */
    public function listModeratorGroups(array $query = []): array
    {
        return $this->request('GET', '/list', $query);
    }

    /**
     * GET /moderator-groups/get
     *
     * Get a moderator group by ID.
     *
     * @param string $moderatorGroupId Required moderator group identifier.
     */
    public function getModeratorGroup(string $moderatorGroupId): array
    {
        return $this->request('GET', '/get', ['moderatorGroupId' => $moderatorGroupId]);
    }

    /**
     * POST /moderator-groups/create
     *
     * Create a new moderator group.
     *
     * Required payload keys:
     * - name: string
     */
    public function createModeratorGroup(array $moderatorGroupPayload): array
    {
        return $this->request('POST', '/create', [], $moderatorGroupPayload);
    }

    /**
     * POST /moderator-groups/add-to-moderator-group
     *
     * Assign moderators to a moderator group.
     *
     * Required payload keys:
     * - moderatorGroupId: string
     * - moderators: map<string,bool>|json-string
     */
    public function addToModeratorGroup(array $assignPayload): array
    {
        return $this->request('POST', '/add-to-moderator-group', [], $assignPayload);
    }

    /**
     * GET /moderator-groups/toggle-user-is-group-admin
     *
     * Toggle admin rights for a moderator inside a group.
     *
     * @param string $moderatorGroupId Required moderator group identifier.
     * @param string $moderatorEmail Required moderator email.
     */
    public function toggleUserIsGroupAdmin(string $moderatorGroupId, string $moderatorEmail): array
    {
        return $this->request('GET', '/toggle-user-is-group-admin', [
            'moderatorGroupId' => $moderatorGroupId,
            'moderatorEmail' => $moderatorEmail,
        ]);
    }

    /**
     * GET /moderator-groups/unassign-user
     *
     * Unassign a moderator from a group.
     *
     * @param string $moderatorGroupId Required moderator group identifier.
     * @param string $moderatorEmail Required moderator email.
     */
    public function unassignUser(string $moderatorGroupId, string $moderatorEmail): array
    {
        return $this->request('GET', '/unassign-user', [
            'moderatorGroupId' => $moderatorGroupId,
            'moderatorEmail' => $moderatorEmail,
        ]);
    }

    /**
     * GET /moderator-groups/toggle-user-can-login
     *
     * Toggle whether a moderator can log in.
     *
     * @param string $moderatorEmail Required moderator email.
     */
    public function toggleUserCanLogin(string $moderatorEmail): array
    {
        return $this->request('GET', '/toggle-user-can-login', ['moderatorEmail' => $moderatorEmail]);
    }

    /**
     * GET /moderator-groups/remove-user
     *
     * Remove a moderator user.
     *
     * @param string $moderatorEmail Required moderator email.
     */
    public function removeUser(string $moderatorEmail): array
    {
        return $this->request('GET', '/remove-user', ['moderatorEmail' => $moderatorEmail]);
    }

    /**
     * GET /moderator-groups/refresh-invitation-link
     *
     * Refresh invitation link for group onboarding.
     *
     * @param string $moderatorGroupId Required moderator group identifier.
     * @param string $scope Required scope: invite_admin or invite_user.
     */
    public function refreshInvitationLink(string $moderatorGroupId, string $scope): array
    {
        return $this->request('GET', '/refresh-invitation-link', [
            'moderatorGroupId' => $moderatorGroupId,
            'scope' => $scope,
        ]);
    }

    /**
     * GET /moderator-groups/delete
     *
     * Delete a moderator group.
     *
     * @param string $moderatorGroupId Required moderator group identifier.
     */
    public function deleteModeratorGroup(string $moderatorGroupId): array
    {
        return $this->request('GET', '/delete', ['moderatorGroupId' => $moderatorGroupId]);
    }
}
