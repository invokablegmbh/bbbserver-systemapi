<?php

declare(strict_types=1);

namespace BbbServer\SystemApiConnector\Http;

final class ApiRequest
{
    public string $method;
    public string $path;
    public array $headers;
    public array $query;
    public ?string $body;
    public ?array $multipartBody;

    public function __construct(
        string $method,
        string $path,
        array $headers = [],
        array $query = [],
        ?string $body = null,
        ?array $multipartBody = null
    ) {
        $this->method = $method;
        $this->path = $path;
        $this->headers = $headers;
        $this->query = $query;
        $this->body = $body;
        $this->multipartBody = $multipartBody;
    }
}
