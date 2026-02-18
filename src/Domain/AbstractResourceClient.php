<?php

declare(strict_types=1);

namespace BbbServer\SystemApiConnector\Domain;

use BbbServer\SystemApiConnector\Http\JsonHttpClient;

abstract class AbstractResourceClient
{
    public function __construct(
        protected readonly JsonHttpClient $jsonHttpClient,
        private readonly string $resourcePath
    ) {
    }

    public function list(array $query = []): array
    {
        return $this->jsonHttpClient->get($this->resourcePath, $query);
    }

    public function get(int|string $identifier, array $query = []): array
    {
        return $this->jsonHttpClient->get($this->resourcePath . '/' . $identifier, $query);
    }

    public function create(array $payload, array $query = []): array
    {
        return $this->jsonHttpClient->post($this->resourcePath, $payload, $query);
    }

    public function update(int|string $identifier, array $payload, array $query = []): array
    {
        return $this->jsonHttpClient->put($this->resourcePath . '/' . $identifier, $payload, $query);
    }

    public function patch(int|string $identifier, array $payload, array $query = []): array
    {
        return $this->jsonHttpClient->patch($this->resourcePath . '/' . $identifier, $payload, $query);
    }

    public function delete(int|string $identifier, array $query = []): array
    {
        return $this->jsonHttpClient->delete($this->resourcePath . '/' . $identifier, $query);
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
