<?php

declare(strict_types=1);

namespace BbbServer\SystemApiConnector\Tests\Unit;

use BbbServer\SystemApiConnector\Exception\TransportException;
use BbbServer\SystemApiConnector\Http\ApiRequest;
use BbbServer\SystemApiConnector\Http\CurlHttpTransport;
use PHPUnit\Framework\TestCase;

final class CurlHttpTransportTest extends TestCase
{
    public function testSendThrowsTransportExceptionOnConnectionFailure(): void
    {
        $curlHttpTransport = new CurlHttpTransport(1);

        $this->expectException(TransportException::class);
        $curlHttpTransport->send('http://127.0.0.1:1', new ApiRequest('GET', '/'));
    }
}
