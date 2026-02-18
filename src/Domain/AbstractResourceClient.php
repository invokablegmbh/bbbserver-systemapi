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

    public function list(array $query = []): array
    {
        return $this->jsonHttpClient->get($this->resourcePath, $query);
    }

    /**
     * @param int|string $identifier
     */
    public function get($identifier, array $query = []): array
    {
        return $this->jsonHttpClient->get($this->resourcePath . '/' . $identifier, $query);
    }

    public function create(array $payload, array $query = []): array
    {
        return $this->jsonHttpClient->post($this->resourcePath, $payload, $query);
    }

    /**
     * @param int|string $identifier
     */
    public function update($identifier, array $payload, array $query = []): array
    {
        return $this->jsonHttpClient->put($this->resourcePath . '/' . $identifier, $payload, $query);
    }

    /**
     * @param int|string $identifier
     */
    public function patch($identifier, array $payload, array $query = []): array
    {
        return $this->jsonHttpClient->patch($this->resourcePath . '/' . $identifier, $payload, $query);
    }

    /**
     * @param int|string $identifier
     */
    public function delete($identifier, array $query = []): array
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
