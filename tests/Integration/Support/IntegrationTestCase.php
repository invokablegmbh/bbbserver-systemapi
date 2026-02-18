<?php

declare(strict_types=1);

namespace BbbServer\SystemApiConnector\Tests\Integration\Support;

use BbbServer\SystemApiConnector\Exception\SystemApiException;
use BbbServer\SystemApiConnector\Http\CurlHttpTransport;
use BbbServer\SystemApiConnector\SystemApiConnector;
use PHPUnit\Framework\TestCase;

abstract class IntegrationTestCase extends TestCase
{
    protected function connector(): SystemApiConnector
    {
        $baseUrl = getenv('BBBSERVER_SYSTEMAPI_BASE_URL') ?: '';
        $apiKey = getenv('BBBSERVER_SYSTEMAPI_KEY') ?: '';

        if ($baseUrl === '' || $apiKey === '') {
            self::markTestSkipped('Integration test skipped: define BBBSERVER_SYSTEMAPI_BASE_URL and BBBSERVER_SYSTEMAPI_KEY.');
        }

        return new SystemApiConnector($baseUrl, $apiKey, new CurlHttpTransport(90));
    }

    protected function callEndpointOrSkipFeature(callable $operation, string $endpointName)
    {
        try {
            return $operation();
        } catch (SystemApiException $systemApiException) {
            if ($this->isLikelyCapabilityRestriction($systemApiException)) {
                $responsePayload = $systemApiException->responsePayload();
                $encodedPayload = $responsePayload === [] ? '{}' : (json_encode($responsePayload) ?: '{}');

                self::markTestSkipped(
                    sprintf(
                        'Endpoint %s skipped due account capability/permission limits (status %d). Error: %s Payload: %s',
                        $endpointName,
                        $systemApiException->statusCode(),
                        $systemApiException->getMessage(),
                        $encodedPayload
                    )
                );
            }

            throw $systemApiException;
        }
    }

    protected function extractIdentifier(array $payload, array $candidateKeys): ?string
    {
        foreach ($candidateKeys as $candidateKey) {
            if (isset($payload[$candidateKey]) && is_string($payload[$candidateKey]) && $payload[$candidateKey] !== '') {
                return $payload[$candidateKey];
            }

            if (
                isset($payload['response'])
                && is_array($payload['response'])
                && isset($payload['response'][$candidateKey])
                && is_string($payload['response'][$candidateKey])
                && $payload['response'][$candidateKey] !== ''
            ) {
                return $payload['response'][$candidateKey];
            }
        }

        return null;
    }

    protected function randomTestEmail(string $prefix): string
    {
        return sprintf('%s_%s@jcdn.de', $prefix, strtolower(bin2hex(random_bytes(4))));
    }

    private function isLikelyCapabilityRestriction(SystemApiException $systemApiException): bool
    {
        $statusCode = $systemApiException->statusCode();
        $responsePayload = $systemApiException->responsePayload();
        $responseString = json_encode($responsePayload);
        $haystack = strtolower((string) $responseString . ' ' . $systemApiException->getMessage());

        if (in_array($statusCode, [401, 403, 404, 500, 501], true)) {
            return true;
        }

        if ($statusCode === 412) {
            if (preg_match('/required|missing|invalid|validation|must\s+be/', $haystack) === 1) {
                return false;
            }

            return true;
        }

        if ($statusCode === 0 && strpos($haystack, 'transport error') !== false) {
            return true;
        }

        return is_string($responseString)
            && preg_match('/premium|partner|permission|forbidden|not\s+available|not\s+enabled|not\s+allowed|precondition|capability|feature/i', $responseString) === 1;
    }
}
