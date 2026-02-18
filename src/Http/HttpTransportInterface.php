<?php

declare(strict_types=1);

namespace BbbServer\SystemApiConnector\Http;

interface HttpTransportInterface
{
    public function send(string $baseUrl, ApiRequest $request): ApiResponse;
}
