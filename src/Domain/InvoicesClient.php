<?php

declare(strict_types=1);

namespace BbbServer\SystemApiConnector\Domain;

use BbbServer\SystemApiConnector\Http\JsonHttpClient;

final class InvoicesClient extends AbstractResourceClient
{
    public function __construct(JsonHttpClient $jsonHttpClient)
    {
        parent::__construct($jsonHttpClient, '/invoices');
    }

    /**
     * GET /invoices/list
     *
     * Get all existing invoices.
     *
     * @param array $query Optional query parameters.
     */
    public function listInvoices(array $query = []): array
    {
        return $this->request('GET', '/list', $query);
    }
}
