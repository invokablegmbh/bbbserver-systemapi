<?php

declare(strict_types=1);

namespace BbbServer\SystemApiConnector\Configuration;

use InvalidArgumentException;

final class SystemApiConfiguration
{
    private string $baseUrl;
    private string $apiKey;

    public function __construct(
        string $baseUrl,
        string $apiKey
    ) {
        $normalizedBaseUrl = rtrim(trim($baseUrl), '/');
        if ($normalizedBaseUrl === '') {
            throw new InvalidArgumentException('SystemAPI base URL must not be empty.');
        }

        $normalizedApiKey = trim($apiKey);
        if ($normalizedApiKey === '') {
            throw new InvalidArgumentException('SystemAPI API key must not be empty.');
        }

        $this->baseUrl = $normalizedBaseUrl;
        $this->apiKey = $normalizedApiKey;
    }

    public static function forBbbserver(
        string $apiKey,
        string $language = '',
        string $baseDomain = 'https://app.bbbserver.de'
    ): self {
        $normalizedLanguage = trim($language);
        $normalizedBaseDomain = rtrim(trim($baseDomain), '/');

        if ($normalizedBaseDomain === '') {
            throw new InvalidArgumentException('bbbserver base domain must not be empty.');
        }

        $baseUrl = $normalizedLanguage === ''
            ? sprintf('%s/bbb-system-api', $normalizedBaseDomain)
            : sprintf('%s/%s/bbb-system-api', $normalizedBaseDomain, $normalizedLanguage);

        return new self($baseUrl, $apiKey);
    }

    public function baseUrl(): string
    {
        return $this->baseUrl;
    }

    public function apiKey(): string
    {
        return $this->apiKey;
    }
}
