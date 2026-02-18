<?php

declare(strict_types=1);

namespace BbbServer\SystemApiConnector\Tests\Support;

use BbbServer\SystemApiConnector\Http\ApiRequest;
use BbbServer\SystemApiConnector\Http\ApiResponse;
use BbbServer\SystemApiConnector\Http\HttpTransportInterface;

final class FakeHttpTransport implements HttpTransportInterface
{
    private array $queuedResponses = [];

    private ?string $lastBaseUrl = null;
    private ?ApiRequest $lastRequest = null;

    public function queueResponse(ApiResponse $apiResponse): void
    {
        $this->queuedResponses[] = $apiResponse;
    }

    public function send(string $baseUrl, ApiRequest $request): ApiResponse
    {
        $this->lastBaseUrl = $baseUrl;
        $this->lastRequest = $request;

        if ($this->queuedResponses === []) {
            return new ApiResponse(200, [], '{}');
        }

        return array_shift($this->queuedResponses);
    }

    public function lastBaseUrl(): ?string
    {
        return $this->lastBaseUrl;
    }

    public function lastRequest(): ?ApiRequest
    {
        return $this->lastRequest;
    }
}
