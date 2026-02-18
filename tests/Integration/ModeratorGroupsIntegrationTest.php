<?php

declare(strict_types=1);

namespace BbbServer\SystemApiConnector\Tests\Integration;

use BbbServer\SystemApiConnector\Tests\Integration\Support\IntegrationTestCase;

/**
 * @group integration
 */
final class ModeratorGroupsIntegrationTest extends IntegrationTestCase
{
    public function testListModeratorGroupsEndpoint(): void
    {
        $responsePayload = $this->callEndpointOrSkipFeature(
            fn (): array => $this->connector()->moderatorGroups()->listModeratorGroups(),
            'GET /moderator-groups/list'
        );

        self::assertIsArray($responsePayload);
    }

    public function testCreateModeratorGroupEndpoint(): array
    {
        $groupResponsePayload = $this->callEndpointOrSkipFeature(
            fn (): array => $this->connector()->moderatorGroups()->createModeratorGroup([
                'name' => 'SDK Moderator Group ' . date('YmdHis'),
            ]),
            'POST /moderator-groups/create'
        );

        self::assertIsArray($groupResponsePayload);

        $groupId = $this->extractIdentifier($groupResponsePayload, ['moderatorGroupId', 'groupId', 'id']);
        self::assertNotNull($groupId);

        $moderatorEmail = $this->randomTestEmail('sdk-mod-group');

        $registerResponsePayload = $this->callEndpointOrSkipFeature(
            fn (): array => $this->connector()->moderators()->registerUser([
                'email' => $moderatorEmail,
                'name' => 'SDK Group Moderator ' . date('YmdHis'),
                'sendWelcomeMail' => false,
            ]),
            'POST /moderators/register-user'
        );

        self::assertIsArray($registerResponsePayload);

        return ['groupId' => $groupId, 'moderatorEmail' => $moderatorEmail];
    }

    /**
     * @depends testCreateModeratorGroupEndpoint
     */
    public function testGetModeratorGroupEndpoint(array $state): array
    {
        $responsePayload = $this->callEndpointOrSkipFeature(
            fn (): array => $this->connector()->moderatorGroups()->getModeratorGroup($state['groupId']),
            'GET /moderator-groups/get'
        );

        self::assertIsArray($responsePayload);

        return $state;
    }

    /**
     * @depends testGetModeratorGroupEndpoint
     */
    public function testAddToModeratorGroupEndpoint(array $state): array
    {
        $responsePayload = $this->callEndpointOrSkipFeature(
            fn (): array => $this->connector()->moderatorGroups()->addToModeratorGroup([
                'moderatorGroupId' => $state['groupId'],
                'moderators' => [$state['moderatorEmail'] => true],
            ]),
            'POST /moderator-groups/add-to-moderator-group'
        );

        self::assertIsArray($responsePayload);

        return $state;
    }

    /**
     * @depends testAddToModeratorGroupEndpoint
     */
    public function testToggleUserIsGroupAdminEndpoint(array $state): array
    {
        $responsePayload = $this->callEndpointOrSkipFeature(
            fn (): array => $this->connector()->moderatorGroups()->toggleUserIsGroupAdmin($state['groupId'], $state['moderatorEmail']),
            'GET /moderator-groups/toggle-user-is-group-admin'
        );

        self::assertIsArray($responsePayload);

        return $state;
    }

    /**
     * @depends testToggleUserIsGroupAdminEndpoint
     */
    public function testUnassignUserEndpoint(array $state): array
    {
        $responsePayload = $this->callEndpointOrSkipFeature(
            fn (): array => $this->connector()->moderatorGroups()->unassignUser($state['groupId'], $state['moderatorEmail']),
            'GET /moderator-groups/unassign-user'
        );

        self::assertIsArray($responsePayload);

        return $state;
    }

    /**
     * @depends testUnassignUserEndpoint
     */
    public function testToggleUserCanLoginEndpoint(array $state): array
    {
        $responsePayload = $this->callEndpointOrSkipFeature(
            fn (): array => $this->connector()->moderatorGroups()->toggleUserCanLogin($state['moderatorEmail']),
            'GET /moderator-groups/toggle-user-can-login'
        );

        self::assertIsArray($responsePayload);

        return $state;
    }

    /**
     * @depends testToggleUserCanLoginEndpoint
     */
    public function testRefreshInvitationLinkEndpoint(array $state): array
    {
        $responsePayload = $this->callEndpointOrSkipFeature(
            fn (): array => $this->connector()->moderatorGroups()->refreshInvitationLink($state['groupId'], 'invite_user'),
            'GET /moderator-groups/refresh-invitation-link'
        );

        self::assertIsArray($responsePayload);

        return $state;
    }

    /**
     * @depends testRefreshInvitationLinkEndpoint
     */
    public function testRemoveUserEndpoint(array $state): array
    {
        $responsePayload = $this->callEndpointOrSkipFeature(
            fn (): array => $this->connector()->moderatorGroups()->removeUser($state['moderatorEmail']),
            'GET /moderator-groups/remove-user'
        );

        self::assertIsArray($responsePayload);

        return $state;
    }

    /**
     * @depends testRemoveUserEndpoint
     */
    public function testDeleteModeratorGroupEndpoint(array $state): void
    {
        $responsePayload = $this->callEndpointOrSkipFeature(
            fn (): array => $this->connector()->moderatorGroups()->deleteModeratorGroup($state['groupId']),
            'GET /moderator-groups/delete'
        );

        self::assertIsArray($responsePayload);
    }
}
