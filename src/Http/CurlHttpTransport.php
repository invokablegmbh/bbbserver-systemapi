<?php

declare(strict_types=1);

namespace BbbServer\SystemApiConnector\Http;

use BbbServer\SystemApiConnector\Exception\TransportException;

final class CurlHttpTransport implements HttpTransportInterface
{
    public function __construct(
        private readonly int $timeoutSeconds = 20
    ) {
    }

    public function send(string $baseUrl, ApiRequest $request): ApiResponse
    {
        if (!function_exists('curl_init')) {
            return $this->sendWithoutCurl($baseUrl, $request);
        }

        $requestUrl = $this->buildRequestUrl($baseUrl, $request->path, $request->query);
        $curlHandle = curl_init($requestUrl);

        if ($curlHandle === false) {
            throw new TransportException('Unable to initialize cURL request.');
        }

        $responseHeaders = [];
        $headerCollector = static function (mixed $curlHandle, string $headerLine) use (&$responseHeaders): int {
            $headerLength = strlen($headerLine);
            $parts = explode(':', $headerLine, 2);
            if (count($parts) === 2) {
                $responseHeaders[strtolower(trim($parts[0]))] = trim($parts[1]);
            }

            return $headerLength;
        };

        $curlHeaders = [];
        foreach ($request->headers as $headerName => $headerValue) {
            $curlHeaders[] = sprintf('%s: %s', $headerName, $headerValue);
        }

        curl_setopt_array($curlHandle, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => strtoupper($request->method),
            CURLOPT_HTTPHEADER => $curlHeaders,
            CURLOPT_TIMEOUT => $this->timeoutSeconds,
            CURLOPT_HEADERFUNCTION => $headerCollector,
        ]);

        if ($request->body !== null) {
            curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $request->body);
        }

        $rawResponseBody = curl_exec($curlHandle);
        $curlError = curl_error($curlHandle);
        $httpStatusCode = (int) curl_getinfo($curlHandle, CURLINFO_HTTP_CODE);
        curl_close($curlHandle);

        if ($rawResponseBody === false) {
            throw new TransportException('Transport error while calling SystemAPI: ' . $curlError);
        }

        return new ApiResponse($httpStatusCode, $responseHeaders, $rawResponseBody);
    }

    private function sendWithoutCurl(string $baseUrl, ApiRequest $request): ApiResponse
    {
        $requestUrl = $this->buildRequestUrl($baseUrl, $request->path, $request->query);

        $requestHeaderLines = [];
        foreach ($request->headers as $headerName => $headerValue) {
            $requestHeaderLines[] = sprintf('%s: %s', $headerName, $headerValue);
        }

        $streamContext = stream_context_create([
            'http' => [
                'method' => strtoupper($request->method),
                'header' => implode("\r\n", $requestHeaderLines),
                'content' => $request->body ?? '',
                'timeout' => $this->timeoutSeconds,
                'ignore_errors' => true,
            ],
        ]);

        $lastStreamErrorMessage = null;
        set_error_handler(static function (int $severity, string $message) use (&$lastStreamErrorMessage): bool {
            $lastStreamErrorMessage = $message;

            return true;
        });
        try {
            $responseBody = file_get_contents($requestUrl, false, $streamContext);
        } finally {
            restore_error_handler();
        }

        if ($responseBody === false) {
            $details = $lastStreamErrorMessage !== null && trim($lastStreamErrorMessage) !== ''
                ? ' ' . trim($lastStreamErrorMessage)
                : '';

            throw new TransportException(
                sprintf('Transport error while calling SystemAPI (stream fallback) for %s.%s', $requestUrl, $details)
            );
        }

        $responseHeaderLines = isset($http_response_header) && is_array($http_response_header)
            ? $http_response_header
            : [];

        $statusCode = $this->extractStatusCode($responseHeaderLines);
        $headers = $this->extractHeaders($responseHeaderLines);

        return new ApiResponse($statusCode, $headers, $responseBody);
    }

    private function buildRequestUrl(string $baseUrl, string $path, array $query): string
    {
        $normalizedBaseUrl = rtrim($baseUrl, '/');
        $normalizedPath = '/' . ltrim($path, '/');
        $queryString = $query === [] ? '' : '?' . http_build_query($query);

        return $normalizedBaseUrl . $normalizedPath . $queryString;
    }

    private function extractStatusCode(array $responseHeaderLines): int
    {
        if ($responseHeaderLines === []) {
            return 0;
        }

        if (preg_match('/^HTTP\/\S+\s+(\d{3})\b/', $responseHeaderLines[0], $matches) === 1) {
            return (int) $matches[1];
        }

        return 0;
    }

    private function extractHeaders(array $responseHeaderLines): array
    {
        $headers = [];
        foreach ($responseHeaderLines as $responseHeaderLine) {
            $parts = explode(':', $responseHeaderLine, 2);
            if (count($parts) === 2) {
                $headers[strtolower(trim($parts[0]))] = trim($parts[1]);
            }
        }

        return $headers;
    }
}
