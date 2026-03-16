<?php

declare(strict_types=1);

namespace BbbServer\SystemApiConnector\Domain;

use BbbServer\SystemApiConnector\Http\JsonHttpClient;

final class CustomerSettingsClient extends AbstractResourceClient
{
    public function __construct(JsonHttpClient $jsonHttpClient)
    {
        parent::__construct($jsonHttpClient, '/customer-settings');
    }

    /**
     * GET /customer-settings/conference-list
     *
     * Get conference overview table data for customer admins.
     */
    public function conferenceList(array $query = []): array
    {
        return $this->request('GET', '/conference-list', $query);
    }

    /**
     * GET /customer-settings/integration-api
     *
     * Get Integration API info (secret + URL).
     */
    public function integrationApi(): array
    {
        return $this->request('GET', '/integration-api');
    }

    /**
     * POST /customer-settings/integration-api/toggle
     *
     * Enable/disable Integration API.
     */
    public function toggleIntegrationApi(): array
    {
        return $this->request('POST', '/integration-api/toggle', [], []);
    }

    /**
     * GET /customer-settings/conference-recording
     *
     * Get if conference recording is enabled.
     */
    public function conferenceRecording(): array
    {
        return $this->request('GET', '/conference-recording');
    }

    /**
     * POST /customer-settings/conference-recording/toggle
     *
     * Toggle conference recording setting.
     */
    public function toggleConferenceRecording(): array
    {
        return $this->request('POST', '/conference-recording/toggle', [], []);
    }

    /**
     * GET /customer-settings/plugins
     *
     * List available plugins and current customer policies.
     */
    public function plugins(): array
    {
        return $this->request('GET', '/plugins');
    }

    /**
     * POST /customer-settings/plugins/set-policies
     *
     * Set multiple plugin policies at once for the customer account.
     *
     * Required payload keys:
     * - policies: string (JSON string with policy key-value pairs)
     */
    public function setPluginPolicies(array $policiesPayload): array
    {
        return $this->request('POST', '/plugins/set-policies', [], $policiesPayload);
    }

    /**
     * GET /customer-settings/branding-logo/get
     *
     * Download branding logo. Returns raw binary content.
     */
    public function getBrandingLogo(): string
    {
        return $this->jsonHttpClient->getRaw('/customer-settings/branding-logo/get');
    }

    /**
     * POST /customer-settings/branding-logo/set
     *
     * Upload branding logo (PNG 580x400).
     *
     * @param \CURLFile $logo CURLFile instance pointing to the logo file.
     */
    public function setBrandingLogo(\CURLFile $logo): array
    {
        return $this->jsonHttpClient->postMultipart('/customer-settings/branding-logo/set', [
            'logo' => $logo,
        ]);
    }

    /**
     * POST /customer-settings/branding-logo/remove
     *
     * Remove branding logo.
     */
    public function removeBrandingLogo(): array
    {
        return $this->request('POST', '/branding-logo/remove', [], []);
    }

    /**
     * GET /customer-settings/branding-color/get
     *
     * Get branding color.
     */
    public function getBrandingColor(): array
    {
        return $this->request('GET', '/branding-color/get');
    }

    /**
     * GET /customer-settings/branding-color/set
     *
     * Set branding color.
     *
     * @param string $color Hex color code (e.g. "#ffffff").
     */
    public function setBrandingColor(string $color): array
    {
        return $this->request('GET', '/branding-color/set', ['color' => $color]);
    }

    /**
     * POST /customer-settings/branding-color/remove
     *
     * Remove branding color.
     */
    public function removeBrandingColor(): array
    {
        return $this->request('POST', '/branding-color/remove', [], []);
    }

    /**
     * GET /customer-settings/branding-presentation/get
     *
     * Download branding presentation. Returns raw binary content.
     */
    public function getBrandingPresentation(): string
    {
        return $this->jsonHttpClient->getRaw('/customer-settings/branding-presentation/get');
    }

    /**
     * POST /customer-settings/branding-presentation/set
     *
     * Upload branding presentation (PDF).
     *
     * @param \CURLFile $slides CURLFile instance pointing to the presentation file.
     */
    public function setBrandingPresentation(\CURLFile $slides): array
    {
        return $this->jsonHttpClient->postMultipart('/customer-settings/branding-presentation/set', [
            'slides' => $slides,
        ]);
    }

    /**
     * POST /customer-settings/branding-presentation/remove
     *
     * Remove branding presentation.
     */
    public function removeBrandingPresentation(): array
    {
        return $this->request('POST', '/branding-presentation/remove', [], []);
    }
}
