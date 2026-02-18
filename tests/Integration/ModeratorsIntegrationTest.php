<?php

declare(strict_types=1);

namespace BbbServer\SystemApiConnector\Tests\Integration;

use BbbServer\SystemApiConnector\Tests\Integration\Support\IntegrationTestCase;

/**
 * @group integration
 */
final class ModeratorsIntegrationTest extends IntegrationTestCase
{
    public function testListModeratorsEndpoint(): void
    {
        $responsePayload = $this->callEndpointOrSkipFeature(
            fn (): array => $this->connector()->moderators()->listModerators(),
            'GET /moderators/list'
        );

        self::assertIsArray($responsePayload);
    }

    public function testRegisterUserEndpoint(): string
    {
        $emailAddress = $this->randomTestEmail('sdk-mod');

        $createResponsePayload = $this->callEndpointOrSkipFeature(
            fn (): array => $this->connector()->moderators()->registerUser([
                'email' => $emailAddress,
                'name' => 'SDK Moderator ' . date('YmdHis'),
                'sendWelcomeMail' => false,
            ]),
            'POST /moderators/register-user'
        );

        self::assertIsArray($createResponsePayload);

        return $emailAddress;
    }

    /**
     * @depends testRegisterUserEndpoint
     */
    public function testToggleUserCanLoginEndpoint(string $emailAddress): string
    {
        $responsePayload = $this->callEndpointOrSkipFeature(
            fn (): array => $this->connector()->moderators()->toggleUserCanLogin($emailAddress),
            'GET /moderators/toggle-user-can-login'
        );

        self::assertIsArray($responsePayload);

        return $emailAddress;
    }

    /**
     * @depends testToggleUserCanLoginEndpoint
     */
    public function testRefreshInvitationLinkEndpoint(string $emailAddress): string
    {
        $responsePayload = $this->callEndpointOrSkipFeature(
            fn (): array => $this->connector()->moderators()->refreshInvitationLink(['email' => $emailAddress]),
            'GET /moderators/refresh-invitation-link'
        );

        self::assertIsArray($responsePayload);

        return $emailAddress;
    }

    /**
     * @depends testRefreshInvitationLinkEndpoint
     */
    public function testToggleUserIsAdminEndpoint(string $emailAddress): string
    {
        $responsePayload = $this->callEndpointOrSkipFeature(
            fn (): array => $this->connector()->moderators()->toggleUserIsAdmin($emailAddress),
            'GET /moderators/toggle-user-is-admin'
        );

        self::assertIsArray($responsePayload);

        return $emailAddress;
    }

    /**
     * @depends testToggleUserIsAdminEndpoint
     */
    public function testRemoveUserEndpoint(string $emailAddress): void
    {
        $responsePayload = $this->callEndpointOrSkipFeature(
            fn (): array => $this->connector()->moderators()->removeUser($emailAddress),
            'GET /moderators/remove-user'
        );

        self::assertIsArray($responsePayload);
    }
}
