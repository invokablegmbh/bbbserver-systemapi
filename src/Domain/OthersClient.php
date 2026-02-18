<?php

declare(strict_types=1);

namespace BbbServer\SystemApiConnector\Domain;

use BbbServer\SystemApiConnector\Http\JsonHttpClient;

final class OthersClient extends AbstractResourceClient
{
    public function __construct(JsonHttpClient $jsonHttpClient)
    {
        parent::__construct($jsonHttpClient, '/');
    }

    /**
     * GET /
     *
     * Test endpoint for API reachability and successful authentication.
     */
    public function root(): array
    {
        return $this->jsonHttpClient->get('/');
    }

    /**
     * GET /others/ipranges
     *
     * Get current bbbserver IP ranges.
     *
     * Optional query parameters:
     * - format: json|text|cleanlist (default json)
     *
     * @param string $format Optional output format.
     */
    public function ipranges(string $format = 'json'): array
    {
        return $this->request('GET', '/others/ipranges', ['format' => $format]);
    }
}
