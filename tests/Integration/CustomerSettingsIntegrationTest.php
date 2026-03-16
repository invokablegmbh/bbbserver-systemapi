<?php

declare(strict_types=1);

namespace BbbServer\SystemApiConnector\Tests\Integration;

use BbbServer\SystemApiConnector\Tests\Integration\Support\IntegrationTestCase;

/**
 * @group integration
 */
final class CustomerSettingsIntegrationTest extends IntegrationTestCase
{
    public function testConferenceListEndpoint(): void
    {
        $responsePayload = $this->callEndpointOrSkipFeature(
            fn (): array => $this->connector()->customerSettings()->conferenceList(),
            'GET /customer-settings/conference-list'
        );

        self::assertIsArray($responsePayload);
    }

    public function testIntegrationApiEndpoint(): void
    {
        $responsePayload = $this->callEndpointOrSkipFeature(
            fn (): array => $this->connector()->customerSettings()->integrationApi(),
            'GET /customer-settings/integration-api'
        );

        self::assertIsArray($responsePayload);
    }

    public function testConferenceRecordingEndpoint(): void
    {
        $responsePayload = $this->callEndpointOrSkipFeature(
            fn (): array => $this->connector()->customerSettings()->conferenceRecording(),
            'GET /customer-settings/conference-recording'
        );

        self::assertIsArray($responsePayload);
    }

    public function testPluginsEndpoint(): void
    {
        $responsePayload = $this->callEndpointOrSkipFeature(
            fn (): array => $this->connector()->customerSettings()->plugins(),
            'GET /customer-settings/plugins'
        );

        self::assertIsArray($responsePayload);
    }

    public function testGetBrandingLogoEndpoint(): void
    {
        $responsePayload = $this->callEndpointOrSkipFeature(
            fn (): string => $this->connector()->customerSettings()->getBrandingLogo(),
            'GET /customer-settings/branding-logo/get'
        );

        self::assertIsString($responsePayload);
    }

    public function testGetBrandingColorEndpoint(): void
    {
        $responsePayload = $this->callEndpointOrSkipFeature(
            fn (): array => $this->connector()->customerSettings()->getBrandingColor(),
            'GET /customer-settings/branding-color/get'
        );

        self::assertIsArray($responsePayload);
    }

    public function testSetBrandingPresentationEndpoint(): void
    {
        $fixturePath = __DIR__ . '/../Fixtures/test-presentation.pdf';
        self::assertFileExists($fixturePath);

        $responsePayload = $this->callEndpointOrSkipFeature(
            fn (): array => $this->connector()->customerSettings()->setBrandingPresentation(
                new \CURLFile($fixturePath, 'application/pdf', 'test-presentation.pdf')
            ),
            'POST /customer-settings/branding-presentation/set'
        );

        self::assertIsArray($responsePayload);
    }

    /**
     * @depends testSetBrandingPresentationEndpoint
     */
    public function testGetBrandingPresentationEndpoint(): void
    {
        $responsePayload = $this->callEndpointOrSkipFeature(
            fn (): string => $this->connector()->customerSettings()->getBrandingPresentation(),
            'GET /customer-settings/branding-presentation/get'
        );

        self::assertIsString($responsePayload);
        self::assertNotEmpty($responsePayload);
    }

    /**
     * @depends testGetBrandingPresentationEndpoint
     */
    public function testRemoveBrandingPresentationEndpoint(): void
    {
        $responsePayload = $this->callEndpointOrSkipFeature(
            fn (): array => $this->connector()->customerSettings()->removeBrandingPresentation(),
            'POST /customer-settings/branding-presentation/remove'
        );

        self::assertIsArray($responsePayload);
    }
}
