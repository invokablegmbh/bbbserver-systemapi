<?php

declare(strict_types=1);

namespace BbbServer\SystemApiConnector\Domain;

use BbbServer\SystemApiConnector\Http\JsonHttpClient;

final class ModeratorsClient extends AbstractResourceClient
{
    public function __construct(JsonHttpClient $jsonHttpClient)
    {
        parent::__construct($jsonHttpClient, '/moderators');
    }

    /**
     * GET /moderators/list
     *
     * Get all moderator users in the customer account.
     *
     * @param array $query Optional query parameters.
     */
    public function listModerators(array $query = []): array
    {
        return $this->request('GET', '/list', $query);
    }

    /**
     * GET /moderators/toggle-user-can-login
     *
     * Enable or disable a moderator account.
     *
     * @param string $email Required moderator email.
     */
    public function toggleUserCanLogin(string $email): array
    {
        return $this->request('GET', '/toggle-user-can-login', ['email' => $email]);
    }

    /**
     * POST /moderators/register-user
     *
     * Register a new moderator user.
     *
     * Required payload keys:
     * - email: valid email address
     * - name: full user name
     *
     * Optional payload keys:
     * - sendWelcomeMail: bool|string("true"|"false"), default false
     */
    public function registerUser(array $moderatorPayload): array
    {
        return $this->request('POST', '/register-user', [], $moderatorPayload);
    }

    /**
     * GET /moderators/remove-user
     *
     * Remove a moderator user and associated data.
     *
     * @param string $email Required moderator email.
     */
    public function removeUser(string $email): array
    {
        return $this->request('GET', '/remove-user', ['email' => $email]);
    }

    /**
     * GET /moderators/refresh-invitation-link
     *
     * Refresh the invitation link used to onboard moderators.
     *
     * @param array $query Optional query parameters.
     */
    public function refreshInvitationLink(array $query = []): array
    {
        return $this->request('GET', '/refresh-invitation-link', $query);
    }

    /**
     * GET /moderators/toggle-user-is-admin
     *
     * Toggle customer admin rights for a moderator.
     *
     * @param string $email Required moderator email.
     */
    public function toggleUserIsAdmin(string $email): array
    {
        return $this->request('GET', '/toggle-user-is-admin', ['email' => $email]);
    }
}
