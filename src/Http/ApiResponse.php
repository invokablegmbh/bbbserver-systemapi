<?php

declare(strict_types=1);

namespace BbbServer\SystemApiConnector\Http;

final class ApiResponse
{
    public int $statusCode;
    public array $headers;
    public string $body;

    public function __construct(
        int $statusCode,
        array $headers,
        string $body
    ) {
        $this->statusCode = $statusCode;
        $this->headers = $headers;
        $this->body = $body;
    }
}
