<?php

declare(strict_types=1);

namespace BbbServer\SystemApiConnector\Tests\Integration;

use BbbServer\SystemApiConnector\Tests\Integration\Support\IntegrationTestCase;
use PHPUnit\Framework\Attributes\Group;

#[Group('integration')]
final class PartnerIntegrationTest extends IntegrationTestCase
{
    public function testClientsEndpoint(): void
    {
        $responsePayload = $this->callEndpointOrSkipFeature(
            fn (): array => $this->connector()->partner()->clients(),
            'GET /partner/clients'
        );

        self::assertIsArray($responsePayload);
    }

    public function testTurnoversEndpoint(): void
    {
        $responsePayload = $this->callEndpointOrSkipFeature(
            fn (): array => $this->connector()->partner()->turnovers((int) date('Y'), (int) date('n')),
            'GET /partner/turnovers'
        );

        self::assertIsArray($responsePayload);
    }

    public function testCreditInvoicesEndpoint(): void
    {
        $responsePayload = $this->callEndpointOrSkipFeature(
            fn (): array => $this->connector()->partner()->creditInvoices(),
            'GET /partner/credit-invoices'
        );

        self::assertIsArray($responsePayload);
    }
}
