<?php

declare(strict_types=1);

namespace BbbServer\SystemApiConnector\Http;

use BbbServer\SystemApiConnector\Exception\AuthenticationException;
use BbbServer\SystemApiConnector\Exception\SystemApiException;
use BbbServer\SystemApiConnector\Exception\UnexpectedResponseException;

final class JsonHttpClient
{
    private string $baseUrl;
    private string $apiKey;
    private HttpTransportInterface $httpTransport;

    public function __construct(
        string $baseUrl,
        string $apiKey,
        HttpTransportInterface $httpTransport
    ) {
        $this->baseUrl = $baseUrl;
        $this->apiKey = $apiKey;
        $this->httpTransport = $httpTransport;
    }

    public function get(string $path, array $query = []): array
    {
        return $this->request('GET', $path, $query);
    }

    public function post(string $path, array $payload = [], array $query = []): array
    {
        return $this->request('POST', $path, $query, $payload);
    }

    public function put(string $path, array $payload = [], array $query = []): array
    {
        return $this->request('PUT', $path, $query, $payload);
    }

    public function patch(string $path, array $payload = [], array $query = []): array
    {
        return $this->request('PATCH', $path, $query, $payload);
    }

    public function delete(string $path, array $query = []): array
    {
        return $this->request('DELETE', $path, $query);
    }

    public function request(string $method, string $path, array $query = [], ?array $payload = null): array
    {
        $body = null;
        $headers = [
            'Accept' => 'application/json',
            'X-API-KEY' => $this->apiKey,
        ];

        if ($payload !== null) {
            $body = http_build_query($payload);
            $headers['Content-Type'] = 'application/x-www-form-urlencoded';
            $query = array_merge($payload, $query);
        }

        $response = $this->httpTransport->send(
            $this->baseUrl,
            new ApiRequest($method, $path, $headers, $query, $body)
        );

        $decodedErrorPayload = $this->decodeResponseBodySafely($response->body);

        if ($response->statusCode === 401 || $response->statusCode === 403) {
            throw new AuthenticationException(
                $this->buildErrorMessage('Authentication with bbbserver SystemAPI failed.', $response->statusCode, $decodedErrorPayload),
                $response->statusCode,
                $decodedErrorPayload
            );
        }

        if ($response->statusCode >= 400) {
            throw new SystemApiException(
                $this->buildErrorMessage('SystemAPI request failed', $response->statusCode, $decodedErrorPayload),
                $response->statusCode,
                $decodedErrorPayload
            );
        }

        if (trim($response->body) === '') {
            return [];
        }

        $decodedResponse = json_decode($response->body, true);
        if (!is_array($decodedResponse)) {
            throw new UnexpectedResponseException('Expected a JSON object/array response from SystemAPI.', $response->statusCode);
        }

        return $decodedResponse;
    }

    private function decodeResponseBodySafely(string $responseBody): array
    {
        if (trim($responseBody) === '') {
            return [];
        }

        $decodedResponse = json_decode($responseBody, true);

        return is_array($decodedResponse) ? $decodedResponse : ['rawBody' => $responseBody];
    }

    private function buildErrorMessage(string $baseMessage, int $statusCode, array $responsePayload): string
    {
        $details = $this->extractHumanReadableErrorDetails($responsePayload);

        if ($details === null) {
            return sprintf('%s with HTTP status %d', $baseMessage, $statusCode);
        }

        return sprintf('%s with HTTP status %d: %s', $baseMessage, $statusCode, $details);
    }

    private function extractHumanReadableErrorDetails(array $responsePayload): ?string
    {
        $candidatePaths = [
            ['response', 'message'],
            ['message'],
            ['error'],
            ['response', 'messageKey'],
            ['rawBody'],
        ];

        foreach ($candidatePaths as $candidatePath) {
            $current = $responsePayload;
            foreach ($candidatePath as $pathSegment) {
                if (!is_array($current) || !array_key_exists($pathSegment, $current)) {
                    continue 2;
                }

                $current = $current[$pathSegment];
            }

            if (is_string($current) && trim($current) !== '') {
                return trim($current);
            }
        }

        if ($responsePayload === []) {
            return null;
        }

        $encodedPayload = json_encode($responsePayload);

        return is_string($encodedPayload) ? $encodedPayload : null;
    }
}
