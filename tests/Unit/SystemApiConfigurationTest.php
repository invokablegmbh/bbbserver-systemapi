<?php

declare(strict_types=1);

namespace BbbServer\SystemApiConnector\Tests\Unit;

use BbbServer\SystemApiConnector\Configuration\SystemApiConfiguration;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class SystemApiConfigurationTest extends TestCase
{
    public function testForBbbserverFactoryBuildsConfigFromLanguageAndBaseDomain(): void
    {
        $configuration = SystemApiConfiguration::forBbbserver('api-key-value', 'en', 'https://app.bbbserver.de');

        self::assertSame('https://app.bbbserver.de/en/bbb-system-api', $configuration->baseUrl());
        self::assertSame('api-key-value', $configuration->apiKey());
    }

    public function testConstructorRejectsEmptyBaseUrl(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new SystemApiConfiguration('', 'api-key-value');
    }

    public function testConstructorRejectsEmptyApiKey(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new SystemApiConfiguration('https://app.bbbserver.de/en/bbb-system-api', '');
    }
}
