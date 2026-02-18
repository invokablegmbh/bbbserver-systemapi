<?php

declare(strict_types=1);

namespace BbbServer\SystemApiConnector;

use BbbServer\SystemApiConnector\Configuration\SystemApiConfiguration;
use BbbServer\SystemApiConnector\Domain\ConferenceRoomsClient;
use BbbServer\SystemApiConnector\Domain\ConferencesClient;
use BbbServer\SystemApiConnector\Domain\InvoicesClient;
use BbbServer\SystemApiConnector\Domain\ModeratorGroupsClient;
use BbbServer\SystemApiConnector\Domain\ModeratorsClient;
use BbbServer\SystemApiConnector\Domain\OthersClient;
use BbbServer\SystemApiConnector\Domain\PartnerClient;
use BbbServer\SystemApiConnector\Domain\RecordingsClient;
use BbbServer\SystemApiConnector\Domain\UserAttendanceClient;
use BbbServer\SystemApiConnector\Http\CurlHttpTransport;
use BbbServer\SystemApiConnector\Http\HttpTransportInterface;
use BbbServer\SystemApiConnector\Http\JsonHttpClient;
use InvalidArgumentException;

final class SystemApiConnector
{
    private readonly SystemApiConfiguration $systemApiConfiguration;
    private readonly JsonHttpClient $jsonHttpClient;

    private ?ConferenceRoomsClient $conferenceRoomsClient = null;
    private ?ModeratorGroupsClient $moderatorGroupsClient = null;
    private ?ConferencesClient $conferencesClient = null;
    private ?UserAttendanceClient $userAttendanceClient = null;
    private ?InvoicesClient $invoicesClient = null;
    private ?ModeratorsClient $moderatorsClient = null;
    private ?RecordingsClient $recordingsClient = null;
    private ?PartnerClient $partnerClient = null;
    private ?OthersClient $othersClient = null;

    public function __construct(
        SystemApiConfiguration|string $configurationOrBaseUrl,
        ?string $apiKey = null,
        ?HttpTransportInterface $httpTransport = null
    ) {
        $configuration = $configurationOrBaseUrl instanceof SystemApiConfiguration
            ? $configurationOrBaseUrl
            : $this->createConfigurationFromLegacyArguments($configurationOrBaseUrl, $apiKey);

        $this->systemApiConfiguration = $configuration;

        $this->jsonHttpClient = new JsonHttpClient(
            $configuration->baseUrl(),
            $configuration->apiKey(),
            $httpTransport ?? new CurlHttpTransport()
        );
    }

    public static function fromConfiguration(
        SystemApiConfiguration $systemApiConfiguration,
        ?HttpTransportInterface $httpTransport = null
    ): self {
        return new self($systemApiConfiguration, null, $httpTransport);
    }

    public static function forBbbserver(
        string $apiKey,
        string $language = 'de',
        ?HttpTransportInterface $httpTransport = null,
        string $baseDomain = 'https://app.bbbserver.de'
    ): self {
        $configuration = SystemApiConfiguration::forBbbserver($apiKey, $language, $baseDomain);

        return self::fromConfiguration($configuration, $httpTransport);
    }

    public function configuration(): SystemApiConfiguration
    {
        return $this->systemApiConfiguration;
    }

    public function conferenceRooms(): ConferenceRoomsClient
    {
        return $this->conferenceRoomsClient ??= new ConferenceRoomsClient($this->jsonHttpClient);
    }

    public function moderatorGroups(): ModeratorGroupsClient
    {
        return $this->moderatorGroupsClient ??= new ModeratorGroupsClient($this->jsonHttpClient);
    }

    public function conferences(): ConferencesClient
    {
        return $this->conferencesClient ??= new ConferencesClient($this->jsonHttpClient);
    }

    public function userAttendance(): UserAttendanceClient
    {
        return $this->userAttendanceClient ??= new UserAttendanceClient($this->jsonHttpClient);
    }

    public function invoices(): InvoicesClient
    {
        return $this->invoicesClient ??= new InvoicesClient($this->jsonHttpClient);
    }

    public function moderators(): ModeratorsClient
    {
        return $this->moderatorsClient ??= new ModeratorsClient($this->jsonHttpClient);
    }

    public function recordings(): RecordingsClient
    {
        return $this->recordingsClient ??= new RecordingsClient($this->jsonHttpClient);
    }

    public function partner(): PartnerClient
    {
        return $this->partnerClient ??= new PartnerClient($this->jsonHttpClient);
    }

    public function others(): OthersClient
    {
        return $this->othersClient ??= new OthersClient($this->jsonHttpClient);
    }

    public function request(string $method, string $path, array $query = [], ?array $payload = null): array
    {
        return $this->jsonHttpClient->request($method, $path, $query, $payload);
    }

    private function createConfigurationFromLegacyArguments(string $baseUrl, ?string $apiKey): SystemApiConfiguration
    {
        if ($apiKey === null) {
            throw new InvalidArgumentException('API key is required when constructing with a base URL string.');
        }

        return new SystemApiConfiguration($baseUrl, $apiKey);
    }
}
