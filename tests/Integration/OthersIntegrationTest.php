<?php

declare(strict_types=1);

namespace BbbServer\SystemApiConnector\Tests\Integration;

use BbbServer\SystemApiConnector\Tests\Integration\Support\IntegrationTestCase;

/**
 * @group integration
 */
final class OthersIntegrationTest extends IntegrationTestCase
{
    public function testIprangesEndpoint(): void
    {
        $responsePayload = $this->callEndpointOrSkipFeature(
            fn (): array => $this->connector()->others()->ipranges('json'),
            'GET /others/ipranges'
        );

        self::assertIsArray($responsePayload);
    }
}
