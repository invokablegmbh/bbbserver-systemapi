# bbbserver BigBlueButton SystemAPI
This is a PHP module connecting to the bbbserver SystemAPI for BigBlueButton.

## What is the SystemAPI for bbbserver?
BigBlueButton offers an official API which contains the most important endpoints for the handling confereces on a BigBlueButton server.
However, default BBB lacks several features that are relevant for business use. This is why premium hosters (as e.g. bbbbserver) offer additional features (scheduled conferences, conference series, extended managing of users, managing of additional meeting features like AI).

bbbserver strives to have 100% feature coverage regarding their APIs. This is why they offer two separate APIs:
- The "IntegrationAPI" is 100% compatible with the BigBlueButton's own API. (https://bbbserver.com/assets/website-bbbserver/pdf/IntegrationAPI.pdf and https://docs.bigbluebutton.org/development/api/)
- The "SystemAPI" offers all things that go beyond the scope of the "IntegrationAPI". (https://documenter.getpostman.com/view/7658590/T1DwdET1?version=latest)

## Installation
Install via Composer:

```bash
composer require bbbserver/systemapiconnector
```

Requirements:
- PHP 7.4+
- `ext-curl` (optional, recommended for best transport performance)

## Configuration
The SystemAPI uses an API key via the `X-API-KEY` header.

You can either:
- pass a full SystemAPI base URL and API key, or
- use the bbbserver factory for language-aware defaults.

```php
use BbbServer\SystemApiConnector\Configuration\SystemApiConfiguration;
use BbbServer\SystemApiConnector\SystemApiConnector;

$configuration = SystemApiConfiguration::forBbbserver(
	'YOUR_SYSTEMAPI_KEY',
	'en',
	'https://app.bbbserver.de'
);

$connector = SystemApiConnector::fromConfiguration($configuration);
```

## Usage
```php
<?php

use BbbServer\SystemApiConnector\SystemApiConnector;

$connector = SystemApiConnector::forBbbserver(
	'YOUR_SYSTEMAPI_KEY',
	'en'
);

$conferenceRooms = $connector->conferenceRooms()->list();

$conferenceRoom = $connector->conferenceRooms()->getConferenceRoom('ROOM_ID');
$conference = $connector->conferences()->createConference([
	'roomId' => 'ROOM_ID',
	'name' => 'Customer Webinar',
]);

$recordings = $connector->recordings()->listRecordings(['roomId' => 'ROOM_ID']);

$rootInfo = $connector->others()->root();
```

Direct request access is available if you need endpoints that are not wrapped yet:

```php
$payload = $connector->request('GET', '/conferences', ['page' => 1]);
```

You can also use resource-local fallback requests for newly released API actions:

```php
$payload = $connector->conferences()->request('GET', '/future-endpoint', ['key' => 'value']);
```

## Development
Install dependencies:

```bash
composer install
```

Run tests:

```bash
composer test
```

Run only unit tests:

```bash
composer test:unit
```

Run integration tests (requires API key):

```bash
export BBBSERVER_SYSTEMAPI_BASE_URL="https://app.bbbserver.de/en/bbb-system-api"
export BBBSERVER_SYSTEMAPI_KEY="YOUR_SYSTEMAPI_KEY"
composer test:integration
```

Integration tests are skipped automatically when credentials are not provided.

The integration suite performs real API lifecycle checks in strict order:
1. create conference room
2. update conference room settings
3. create conference
4. get conference
5. update conference
6. delete conference
7. delete conference room

Cleanup safeguards run in `tearDownAfterClass()` to remove created entities if a test fails mid-sequence.
