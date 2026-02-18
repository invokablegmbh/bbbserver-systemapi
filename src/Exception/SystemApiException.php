<?php

declare(strict_types=1);

namespace BbbServer\SystemApiConnector\Exception;

use RuntimeException;

class SystemApiException extends RuntimeException
{
    public function __construct(
        string $message,
        private readonly int $statusCode = 0,
        private readonly array $responsePayload = [],
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function statusCode(): int
    {
        return $this->statusCode;
    }

    public function responsePayload(): array
    {
        return $this->responsePayload;
    }
}
