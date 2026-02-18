<?php

declare(strict_types=1);

namespace BbbServer\SystemApiConnector\Http;

final readonly class ApiRequest
{
    public function __construct(
        public string $method,
        public string $path,
        public array $headers = [],
        public array $query = [],
        public ?string $body = null
    ) {
    }
}
