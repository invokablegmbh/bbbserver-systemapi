<?php

declare(strict_types=1);

namespace BbbServer\SystemApiConnector\Exception;

use RuntimeException;

class SystemApiException extends RuntimeException
{
    private int $statusCode;
    private array $responsePayload;

    public function __construct(
        string $message,
        int $statusCode = 0,
        array $responsePayload = [],
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);

        $this->statusCode = $statusCode;
        $this->responsePayload = $responsePayload;
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
