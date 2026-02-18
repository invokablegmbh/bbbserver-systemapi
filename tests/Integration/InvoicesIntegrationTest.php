<?php

declare(strict_types=1);

namespace BbbServer\SystemApiConnector\Tests\Integration;

use BbbServer\SystemApiConnector\Tests\Integration\Support\IntegrationTestCase;

/**
 * @group integration
 */
final class InvoicesIntegrationTest extends IntegrationTestCase
{
    public function testListInvoicesEndpoint(): void
    {
        $responsePayload = $this->callEndpointOrSkipFeature(
            fn (): array => $this->connector()->invoices()->listInvoices(),
            'GET /invoices/list'
        );

        self::assertIsArray($responsePayload);
    }
}
