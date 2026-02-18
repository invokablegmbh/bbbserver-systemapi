<?php

declare(strict_types=1);

namespace BbbServer\SystemApiConnector\Tests\Unit;

use BbbServer\SystemApiConnector\Exception\AuthenticationException;
use BbbServer\SystemApiConnector\Exception\SystemApiException;
use BbbServer\SystemApiConnector\Exception\UnexpectedResponseException;
use BbbServer\SystemApiConnector\Http\ApiResponse;
use BbbServer\SystemApiConnector\Http\JsonHttpClient;
use BbbServer\SystemApiConnector\Tests\Support\FakeHttpTransport;
use PHPUnit\Framework\TestCase;

final class JsonHttpClientTest extends TestCase
{
    public function testGetBuildsHeadersAndQueryParameters(): void
    {
        $fakeHttpTransport = new FakeHttpTransport();
        $fakeHttpTransport->queueResponse(new ApiResponse(200, [], '{"ok":true}'));

        $jsonHttpClient = new JsonHttpClient('https://app.bbbserver.de/en/bbb-system-api', 'api-key-value', $fakeHttpTransport);

        $responsePayload = $jsonHttpClient->get('/conferences', ['page' => 2]);

        self::assertTrue($responsePayload['ok']);
        self::assertSame('https://app.bbbserver.de/en/bbb-system-api', $fakeHttpTransport->lastBaseUrl());
        self::assertSame('/conferences', $fakeHttpTransport->lastRequest()?->path);
        self::assertSame(['page' => 2], $fakeHttpTransport->lastRequest()?->query);
        self::assertSame('api-key-value', $fakeHttpTransport->lastRequest()?->headers['X-API-KEY']);
    }

    public function testPostEncodesRequestPayloadAsFormUrlEncoded(): void
    {
        $fakeHttpTransport = new FakeHttpTransport();
        $fakeHttpTransport->queueResponse(new ApiResponse(200, [], '{"created":true}'));

        $jsonHttpClient = new JsonHttpClient('https://example.test/api', 'api-key-value', $fakeHttpTransport);
        $jsonHttpClient->post('/conferences', ['name' => 'Room Name']);

        self::assertSame('name=Room+Name', $fakeHttpTransport->lastRequest()?->body);
        self::assertSame(['name' => 'Room Name'], $fakeHttpTransport->lastRequest()?->query);
        self::assertSame('application/x-www-form-urlencoded', $fakeHttpTransport->lastRequest()?->headers['Content-Type']);
    }

    public function testRequestThrowsAuthenticationExceptionForUnauthorizedResponse(): void
    {
        $fakeHttpTransport = new FakeHttpTransport();
        $fakeHttpTransport->queueResponse(new ApiResponse(401, [], '{"error":"unauthorized"}'));

        $jsonHttpClient = new JsonHttpClient('https://example.test/api', 'wrong-key', $fakeHttpTransport);

        try {
            $jsonHttpClient->get('/conferences');
            self::fail('Expected AuthenticationException was not thrown.');
        } catch (AuthenticationException $authenticationException) {
            self::assertSame(401, $authenticationException->statusCode());
            self::assertSame(['error' => 'unauthorized'], $authenticationException->responsePayload());
            self::assertStringContainsString('unauthorized', $authenticationException->getMessage());
        }
    }

    public function testRequestThrowsSystemApiExceptionForHttpErrorResponse(): void
    {
        $fakeHttpTransport = new FakeHttpTransport();
        $fakeHttpTransport->queueResponse(new ApiResponse(422, [], 'validation failed'));

        $jsonHttpClient = new JsonHttpClient('https://example.test/api', 'api-key-value', $fakeHttpTransport);

        try {
            $jsonHttpClient->get('/conferences');
            self::fail('Expected SystemApiException was not thrown.');
        } catch (SystemApiException $systemApiException) {
            self::assertSame(422, $systemApiException->statusCode());
            self::assertSame(['rawBody' => 'validation failed'], $systemApiException->responsePayload());
            self::assertStringContainsString('validation failed', $systemApiException->getMessage());
        }
    }

    public function testRequestThrowsUnexpectedResponseExceptionForInvalidSuccessJson(): void
    {
        $fakeHttpTransport = new FakeHttpTransport();
        $fakeHttpTransport->queueResponse(new ApiResponse(200, [], 'not-json'));

        $jsonHttpClient = new JsonHttpClient('https://example.test/api', 'api-key-value', $fakeHttpTransport);

        $this->expectException(UnexpectedResponseException::class);
        $jsonHttpClient->get('/conferences');
    }

    public function testRequestReturnsEmptyArrayForBlankBody(): void
    {
        $fakeHttpTransport = new FakeHttpTransport();
        $fakeHttpTransport->queueResponse(new ApiResponse(204, [], ''));

        $jsonHttpClient = new JsonHttpClient('https://example.test/api', 'api-key-value', $fakeHttpTransport);

        self::assertSame([], $jsonHttpClient->delete('/conferences/1'));
    }

    public function testPatchAndPutMethodsAreRoutedCorrectly(): void
    {
        $fakeHttpTransport = new FakeHttpTransport();
        $fakeHttpTransport->queueResponse(new ApiResponse(200, [], '{"ok":true}'));
        $fakeHttpTransport->queueResponse(new ApiResponse(200, [], '{"ok":true}'));

        $jsonHttpClient = new JsonHttpClient('https://example.test/api', 'api-key-value', $fakeHttpTransport);

        $jsonHttpClient->patch('/conferences/1', ['title' => 'A']);
        self::assertSame('PATCH', $fakeHttpTransport->lastRequest()?->method);

        $jsonHttpClient->put('/conferences/1', ['title' => 'B']);
        self::assertSame('PUT', $fakeHttpTransport->lastRequest()?->method);
    }
}
