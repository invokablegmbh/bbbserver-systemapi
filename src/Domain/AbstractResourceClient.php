<?php

declare(strict_types=1);

namespace BbbServer\SystemApiConnector\Domain;

use BbbServer\SystemApiConnector\Http\JsonHttpClient;

abstract class AbstractResourceClient
{
    protected JsonHttpClient $jsonHttpClient;
    private string $resourcePath;

    public function __construct(
        JsonHttpClient $jsonHttpClient,
        string $resourcePath
    ) {
        $this->jsonHttpClient = $jsonHttpClient;
        $this->resourcePath = $resourcePath;
    }

    public function request(string $method, string $path = '', array $query = [], ?array $payload = null): array
    {
        $normalizedResourcePath = '/' . trim($this->resourcePath, '/');
        if ($normalizedResourcePath === '/') {
            $normalizedResourcePath = '';
        }

        $normalizedPath = trim($path) === ''
            ? ($normalizedResourcePath === '' ? '/' : $normalizedResourcePath)
            : ($normalizedResourcePath === '' ? '' : $normalizedResourcePath) . '/' . ltrim($path, '/');

        return $this->jsonHttpClient->request($method, $normalizedPath, $query, $payload);
    }
}
