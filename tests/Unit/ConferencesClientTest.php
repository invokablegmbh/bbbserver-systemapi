<?php

declare(strict_types=1);

namespace BbbServer\SystemApiConnector\Tests\Unit;

use BbbServer\SystemApiConnector\Domain\ConferencesClient;
use BbbServer\SystemApiConnector\Http\ApiResponse;
use BbbServer\SystemApiConnector\Http\JsonHttpClient;
use BbbServer\SystemApiConnector\Tests\Support\FakeHttpTransport;
use PHPUnit\Framework\TestCase;

final class ConferencesClientTest extends TestCase
{
    private FakeHttpTransport $fakeHttpTransport;
    private ConferencesClient $conferencesClient;

    protected function setUp(): void
    {
        $this->fakeHttpTransport = new FakeHttpTransport();
        $jsonHttpClient = new JsonHttpClient('https://example.test/api', 'api-key-value', $this->fakeHttpTransport);
        $this->conferencesClient = new ConferencesClient($jsonHttpClient);
    }

    public function testRemoveSlidesUsesCorrectPathAndMethod(): void
    {
        $this->fakeHttpTransport->queueResponse(new ApiResponse(200, [], '{"removed":true}'));

        $this->conferencesClient->removeSlides('conf-id');

        self::assertSame('/conferences/remove-slides', $this->fakeHttpTransport->lastRequest()->path);
        self::assertSame('GET', $this->fakeHttpTransport->lastRequest()->method);
        self::assertSame(['conferenceId' => 'conf-id'], $this->fakeHttpTransport->lastRequest()->query);
    }

    public function testDownloadSlidesUsesCorrectPathAndMethod(): void
    {
        $this->fakeHttpTransport->queueResponse(new ApiResponse(200, [], '{"url":"https://example.test/slides.pdf"}'));

        $this->conferencesClient->downloadSlides('conf-id');

        self::assertSame('/conferences/download-slides', $this->fakeHttpTransport->lastRequest()->path);
        self::assertSame('GET', $this->fakeHttpTransport->lastRequest()->method);
        self::assertSame(['conferenceId' => 'conf-id'], $this->fakeHttpTransport->lastRequest()->query);
    }

    /**
     * @requires extension curl
     */
    public function testUploadSlidesUsesCorrectPathAndMultipart(): void
    {
        $this->fakeHttpTransport->queueResponse(new ApiResponse(200, [], '{"uploaded":true}'));

        $tempFile = tempnam(sys_get_temp_dir(), 'slides');
        file_put_contents($tempFile, 'fake-pdf-content');
        $curlFile = new \CURLFile($tempFile, 'application/pdf', 'slides.pdf');

        try {
            $this->conferencesClient->uploadSlides('conf-id', $curlFile);

            self::assertSame('/conferences/upload-slides', $this->fakeHttpTransport->lastRequest()->path);
            self::assertSame('POST', $this->fakeHttpTransport->lastRequest()->method);
            self::assertNotNull($this->fakeHttpTransport->lastRequest()->multipartBody);
            self::assertSame('conf-id', $this->fakeHttpTransport->lastRequest()->multipartBody['conferenceId']);
            self::assertInstanceOf(\CURLFile::class, $this->fakeHttpTransport->lastRequest()->multipartBody['slides']);
        } finally {
            @unlink($tempFile);
        }
    }
}
