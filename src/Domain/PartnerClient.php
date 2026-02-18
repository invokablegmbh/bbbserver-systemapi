<?php

declare(strict_types=1);

namespace BbbServer\SystemApiConnector\Domain;

use BbbServer\SystemApiConnector\Http\JsonHttpClient;

final class PartnerClient extends AbstractResourceClient
{
    public function __construct(JsonHttpClient $jsonHttpClient)
    {
        parent::__construct($jsonHttpClient, '/partner');
    }

    /**
     * GET /partner/clients
     *
     * Get all connected clients for a partner account.
     *
     * @param array $query Optional query parameters.
     */
    public function clients(array $query = []): array
    {
        return $this->request('GET', '/clients', $query);
    }

    /**
     * GET /partner/turnovers
     *
     * Get turnover breakdown for a specific year and month.
     *
     * @param int $year Required year.
     * @param int $month Required month (1-12).
     */
    public function turnovers(int $year, int $month): array
    {
        return $this->request('GET', '/turnovers', [
            'year' => $year,
            'month' => $month,
        ]);
    }

    /**
     * GET /partner/credit-invoices
     *
     * Get credit invoices for a partner account.
     *
     * @param array $query Optional query parameters.
     */
    public function creditInvoices(array $query = []): array
    {
        return $this->request('GET', '/credit-invoices', $query);
    }
}
