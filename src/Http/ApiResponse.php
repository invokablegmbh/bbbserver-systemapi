<?php

declare(strict_types=1);

namespace BbbServer\SystemApiConnector\Http;

final readonly class ApiResponse
{
    public function __construct(
        public int $statusCode,
        public array $headers,
        public string $body
    ) {
    }
}
