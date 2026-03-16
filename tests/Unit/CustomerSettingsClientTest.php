<?php

declare(strict_types=1);

namespace BbbServer\SystemApiConnector\Tests\Unit;

use BbbServer\SystemApiConnector\Domain\CustomerSettingsClient;
use BbbServer\SystemApiConnector\Http\ApiResponse;
use BbbServer\SystemApiConnector\Http\JsonHttpClient;
use BbbServer\SystemApiConnector\Tests\Support\FakeHttpTransport;
use PHPUnit\Framework\TestCase;

final class CustomerSettingsClientTest extends TestCase
{
    private FakeHttpTransport $fakeHttpTransport;
    private CustomerSettingsClient $customerSettingsClient;

    protected function setUp(): void
    {
        $this->fakeHttpTransport = new FakeHttpTransport();
        $jsonHttpClient = new JsonHttpClient('https://example.test/api', 'api-key-value', $this->fakeHttpTransport);
        $this->customerSettingsClient = new CustomerSettingsClient($jsonHttpClient);
    }

    public function testConferenceListUsesCorrectPathAndMethod(): void
    {
        $this->fakeHttpTransport->queueResponse(new ApiResponse(200, [], '{"data":[]}'));

        $this->customerSettingsClient->conferenceList();

        self::assertSame('/customer-settings/conference-list', $this->fakeHttpTransport->lastRequest()->path);
        self::assertSame('GET', $this->fakeHttpTransport->lastRequest()->method);
    }

    public function testIntegrationApiUsesCorrectPathAndMethod(): void
    {
        $this->fakeHttpTransport->queueResponse(new ApiResponse(200, [], '{"data":{}}'));

        $this->customerSettingsClient->integrationApi();

        self::assertSame('/customer-settings/integration-api', $this->fakeHttpTransport->lastRequest()->path);
        self::assertSame('GET', $this->fakeHttpTransport->lastRequest()->method);
    }

    public function testToggleIntegrationApiUsesCorrectPathAndMethod(): void
    {
        $this->fakeHttpTransport->queueResponse(new ApiResponse(200, [], '{"toggled":true}'));

        $this->customerSettingsClient->toggleIntegrationApi();

        self::assertSame('/customer-settings/integration-api/toggle', $this->fakeHttpTransport->lastRequest()->path);
        self::assertSame('POST', $this->fakeHttpTransport->lastRequest()->method);
    }

    public function testConferenceRecordingUsesCorrectPathAndMethod(): void
    {
        $this->fakeHttpTransport->queueResponse(new ApiResponse(200, [], '{"enabled":true}'));

        $this->customerSettingsClient->conferenceRecording();

        self::assertSame('/customer-settings/conference-recording', $this->fakeHttpTransport->lastRequest()->path);
        self::assertSame('GET', $this->fakeHttpTransport->lastRequest()->method);
    }

    public function testToggleConferenceRecordingUsesCorrectPathAndMethod(): void
    {
        $this->fakeHttpTransport->queueResponse(new ApiResponse(200, [], '{"toggled":true}'));

        $this->customerSettingsClient->toggleConferenceRecording();

        self::assertSame('/customer-settings/conference-recording/toggle', $this->fakeHttpTransport->lastRequest()->path);
        self::assertSame('POST', $this->fakeHttpTransport->lastRequest()->method);
    }

    public function testPluginsUsesCorrectPathAndMethod(): void
    {
        $this->fakeHttpTransport->queueResponse(new ApiResponse(200, [], '{"plugins":[]}'));

        $this->customerSettingsClient->plugins();

        self::assertSame('/customer-settings/plugins', $this->fakeHttpTransport->lastRequest()->path);
        self::assertSame('GET', $this->fakeHttpTransport->lastRequest()->method);
    }

    public function testSetPluginPoliciesUsesCorrectPathAndMethod(): void
    {
        $this->fakeHttpTransport->queueResponse(new ApiResponse(200, [], '{"updated":true}'));

        $this->customerSettingsClient->setPluginPolicies(['policies' => '{"key":"value"}']);

        self::assertSame('/customer-settings/plugins/set-policies', $this->fakeHttpTransport->lastRequest()->path);
        self::assertSame('POST', $this->fakeHttpTransport->lastRequest()->method);
    }

    public function testGetBrandingLogoUsesCorrectPathAndMethod(): void
    {
        $this->fakeHttpTransport->queueResponse(new ApiResponse(200, [], 'binary-logo-content'));

        $result = $this->customerSettingsClient->getBrandingLogo();

        self::assertSame('/customer-settings/branding-logo/get', $this->fakeHttpTransport->lastRequest()->path);
        self::assertSame('GET', $this->fakeHttpTransport->lastRequest()->method);
        self::assertSame('binary-logo-content', $result);
    }

    /**
     * @requires extension curl
     */
    public function testSetBrandingLogoUsesCorrectPathAndMultipart(): void
    {
        $this->fakeHttpTransport->queueResponse(new ApiResponse(200, [], '{"uploaded":true}'));

        $tempFile = tempnam(sys_get_temp_dir(), 'logo');
        file_put_contents($tempFile, 'fake-image-content');
        $curlFile = new \CURLFile($tempFile, 'image/png', 'logo.png');

        try {
            $this->customerSettingsClient->setBrandingLogo($curlFile);

            self::assertSame('/customer-settings/branding-logo/set', $this->fakeHttpTransport->lastRequest()->path);
            self::assertSame('POST', $this->fakeHttpTransport->lastRequest()->method);
            self::assertNotNull($this->fakeHttpTransport->lastRequest()->multipartBody);
            self::assertArrayHasKey('logo', $this->fakeHttpTransport->lastRequest()->multipartBody);
        } finally {
            @unlink($tempFile);
        }
    }

    public function testRemoveBrandingLogoUsesCorrectPathAndMethod(): void
    {
        $this->fakeHttpTransport->queueResponse(new ApiResponse(200, [], '{"removed":true}'));

        $this->customerSettingsClient->removeBrandingLogo();

        self::assertSame('/customer-settings/branding-logo/remove', $this->fakeHttpTransport->lastRequest()->path);
        self::assertSame('POST', $this->fakeHttpTransport->lastRequest()->method);
    }

    public function testGetBrandingColorUsesCorrectPathAndMethod(): void
    {
        $this->fakeHttpTransport->queueResponse(new ApiResponse(200, [], '{}'));

        $this->customerSettingsClient->getBrandingColor();

        self::assertSame('/customer-settings/branding-color/get', $this->fakeHttpTransport->lastRequest()->path);
        self::assertSame('GET', $this->fakeHttpTransport->lastRequest()->method);
    }

    public function testSetBrandingColorUsesCorrectPathAndQueryParam(): void
    {
        $this->fakeHttpTransport->queueResponse(new ApiResponse(200, [], '{"set":true}'));

        $this->customerSettingsClient->setBrandingColor('#ff0000');

        self::assertSame('/customer-settings/branding-color/set', $this->fakeHttpTransport->lastRequest()->path);
        self::assertSame('GET', $this->fakeHttpTransport->lastRequest()->method);
        self::assertSame(['color' => '#ff0000'], $this->fakeHttpTransport->lastRequest()->query);
    }

    public function testRemoveBrandingColorUsesCorrectPathAndMethod(): void
    {
        $this->fakeHttpTransport->queueResponse(new ApiResponse(200, [], '{"removed":true}'));

        $this->customerSettingsClient->removeBrandingColor();

        self::assertSame('/customer-settings/branding-color/remove', $this->fakeHttpTransport->lastRequest()->path);
        self::assertSame('POST', $this->fakeHttpTransport->lastRequest()->method);
    }

    public function testGetBrandingPresentationUsesCorrectPathAndMethod(): void
    {
        $this->fakeHttpTransport->queueResponse(new ApiResponse(200, [], 'binary-pdf-content'));

        $result = $this->customerSettingsClient->getBrandingPresentation();

        self::assertSame('/customer-settings/branding-presentation/get', $this->fakeHttpTransport->lastRequest()->path);
        self::assertSame('GET', $this->fakeHttpTransport->lastRequest()->method);
        self::assertSame('binary-pdf-content', $result);
    }

    /**
     * @requires extension curl
     */
    public function testSetBrandingPresentationUsesCorrectPathAndMultipart(): void
    {
        $this->fakeHttpTransport->queueResponse(new ApiResponse(200, [], '{"uploaded":true}'));

        $tempFile = tempnam(sys_get_temp_dir(), 'slides');
        file_put_contents($tempFile, 'fake-pdf-content');
        $curlFile = new \CURLFile($tempFile, 'application/pdf', 'slides.pdf');

        try {
            $this->customerSettingsClient->setBrandingPresentation($curlFile);

            self::assertSame('/customer-settings/branding-presentation/set', $this->fakeHttpTransport->lastRequest()->path);
            self::assertSame('POST', $this->fakeHttpTransport->lastRequest()->method);
            self::assertNotNull($this->fakeHttpTransport->lastRequest()->multipartBody);
            self::assertArrayHasKey('slides', $this->fakeHttpTransport->lastRequest()->multipartBody);
        } finally {
            @unlink($tempFile);
        }
    }

    public function testRemoveBrandingPresentationUsesCorrectPathAndMethod(): void
    {
        $this->fakeHttpTransport->queueResponse(new ApiResponse(200, [], '{"removed":true}'));

        $this->customerSettingsClient->removeBrandingPresentation();

        self::assertSame('/customer-settings/branding-presentation/remove', $this->fakeHttpTransport->lastRequest()->path);
        self::assertSame('POST', $this->fakeHttpTransport->lastRequest()->method);
    }
}
