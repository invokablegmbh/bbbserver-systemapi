<?php

declare(strict_types=1);

namespace BbbServer\SystemApiConnector\Tests\Integration;

use BbbServer\SystemApiConnector\Tests\Integration\Support\IntegrationTestCase;
use PHPUnit\Framework\Attributes\Group;

#[Group('integration')]
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
