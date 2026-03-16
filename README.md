# bbbserver SystemAPI Connector

A Composer-installable PHP connector for the [bbbserver SystemAPI](https://documenter.getpostman.com/view/7658590/T1DwdET1?version=latest) (BigBlueButton ecosystem).

## What is the SystemAPI?

BigBlueButton offers an official API for managing conferences on a BBB server. However, standard BigBlueButton lacks features relevant for business use — scheduled conferences, user management, recordings, branding, and more.

[bbbserver](https://bbbserver.com) provides two APIs with full feature coverage:

- **Integration API** — 100% compatible with BigBlueButton's native API, enabling drop-in support for existing plugins (Moodle, Nextcloud, WordPress, etc.). [Docs](https://bbbserver.com/assets/website-bbbserver/pdf/IntegrationAPI.pdf) | [BBB API Reference](https://docs.bigbluebutton.org/development/api/)
- **System API** — covers everything beyond the Integration API scope: room management, scheduled conferences, moderator groups, customer settings, branding, recordings, and more. [Docs](https://documenter.getpostman.com/view/7658590/T1DwdET1?version=latest)

This connector wraps the **System API**.

## Installation

```bash
composer require invokablegmbh/bbbserver-systemapi
```

**Requirements:** PHP 7.4+ and `ext-curl`.

## Configuration

Obtain an API key from your bbbserver account under "My Profil". The key is sent via the `X-API-KEY` header on every request.

```php
use BbbServer\SystemApiConnector\SystemApiConnector;

// Simplest — uses default base URL https://app.bbbserver.de/bbb-system-api
$connector = SystemApiConnector::forBbbserver('YOUR_API_KEY');

// With explicit language prefix (affects server-generated texts like emails):
$connector = SystemApiConnector::forBbbserver('YOUR_API_KEY', 'en');

// With a fully custom base URL:
$connector = new SystemApiConnector('https://custom.example.com/bbb-system-api', 'YOUR_API_KEY');
```

## Usage

### Conference Rooms

```php
$rooms = $connector->conferenceRooms()->listConferenceRooms();

$room = $connector->conferenceRooms()->getConferenceRoom('ROOM_ID');

$connector->conferenceRooms()->createConferenceRoom(['name' => 'Team Room']);

$connector->conferenceRooms()->updateConferenceRoomSettings([
    'roomId' => 'ROOM_ID',
    'name'   => 'Renamed Room',
]);

$connector->conferenceRooms()->personalJoins([
    'roomId' => 'ROOM_ID',
    'names'  => json_encode(['Alice', 'Bob']),
    'type'   => 0, // 0 = guest, 1 = moderator
]);

$connector->conferenceRooms()->deleteConferenceRoom('ROOM_ID');
```

### Conferences

```php
$conferences = $connector->conferences()->listConferences(['roomId' => 'ROOM_ID']);

$conference = $connector->conferences()->getConference('CONFERENCE_ID');

$connector->conferences()->findConference('JOIN_LINK_OR_QUERY');

$connector->conferences()->createConference([
    'roomId'                    => 'ROOM_ID',
    'name'                      => 'Customer Webinar',
    'maxConnections'            => 50,
    'startTime'                 => '2026-04-01 10:00:00',
    'duration'                  => 60,
    'askModeratorForGuestJoin'  => 'true',
    'advancedSettings'          => json_encode(['webcamsOnlyForModerator' => false]),
    'userAttendenceDocumentation' => 0,
]);

$connector->conferences()->updateConference([
    'conferenceId' => 'CONFERENCE_ID',
    'name'         => 'Updated Webinar',
]);

$connector->conferences()->personalJoins([
    'conferenceId' => 'CONFERENCE_ID',
    'names'        => json_encode(['Alice', 'Bob']),
    'type'         => 0,
]);

$connector->conferences()->deleteConference('CONFERENCE_ID');
```

### Slides (per conference)

```php
$connector->conferences()->uploadSlides('CONFERENCE_ID', new CURLFile('/path/to/slides.pdf'));

$connector->conferences()->downloadSlides('CONFERENCE_ID');

$connector->conferences()->removeSlides('CONFERENCE_ID');
```

### Moderators

```php
$connector->moderators()->listModerators();

$connector->moderators()->registerUser([
    'email' => 'new@example.com',
    'name'  => 'Jane Doe',
]);

$connector->moderators()->toggleUserCanLogin('user@example.com');
$connector->moderators()->toggleUserIsAdmin('user@example.com');
$connector->moderators()->refreshInvitationLink(['email' => 'user@example.com']);
$connector->moderators()->removeUser('user@example.com');
```

### Moderator Groups (Premium)

```php
$connector->moderatorGroups()->listModeratorGroups();
$connector->moderatorGroups()->getModeratorGroup('GROUP_ID');
$connector->moderatorGroups()->createModeratorGroup(['name' => 'Sales Team']);

$connector->moderatorGroups()->addToModeratorGroup([
    'moderatorGroupId' => 'GROUP_ID',
    'moderators'       => json_encode(['user@example.com']),
]);

$connector->moderatorGroups()->toggleUserIsGroupAdmin('GROUP_ID', 'user@example.com');
$connector->moderatorGroups()->unassignUser('GROUP_ID', 'user@example.com');
$connector->moderatorGroups()->refreshInvitationLink('GROUP_ID', 'moderator');
$connector->moderatorGroups()->deleteModeratorGroup('GROUP_ID');
```

### Customer Settings

```php
$connector->customerSettings()->conferenceList();
$connector->customerSettings()->plugins();
$connector->customerSettings()->setPluginPolicies(['policies' => json_encode(['key' => 'value'])]);

// Integration API
$connector->customerSettings()->integrationApi();
$connector->customerSettings()->toggleIntegrationApi();

// Conference recording
$connector->customerSettings()->conferenceRecording();
$connector->customerSettings()->toggleConferenceRecording();

// Branding — color
$connector->customerSettings()->getBrandingColor();
$connector->customerSettings()->setBrandingColor('#1a73e8');
$connector->customerSettings()->removeBrandingColor();

// Branding — logo (PNG, 580x400)
$logoContent = $connector->customerSettings()->getBrandingLogo();   // returns raw binary string
$connector->customerSettings()->setBrandingLogo(new CURLFile('/path/to/logo.png'));
$connector->customerSettings()->removeBrandingLogo();

// Branding — presentation (PDF)
$pdfContent = $connector->customerSettings()->getBrandingPresentation(); // returns raw binary string
$connector->customerSettings()->setBrandingPresentation(new CURLFile('/path/to/slides.pdf'));
$connector->customerSettings()->removeBrandingPresentation();
```

### Recordings

```php
$connector->recordings()->listRecordings(['roomId' => 'ROOM_ID']);
$connector->recordings()->listByConference('CONFERENCE_ID');
$connector->recordings()->getRecording('RECORDING_ID');
$connector->recordings()->prepareDownload('RECORDING_ID');
$connector->recordings()->deleteRecording('RECORDING_ID');
```

### Invoices

```php
$connector->invoices()->listInvoices();
```

### User Attendance

```php
$connector->userAttendance()->listUserAttendance(['roomId' => 'ROOM_ID']);
$connector->userAttendance()->getUserAttendance('CONFERENCE_ID');
$connector->userAttendance()->deleteUserAttendance('CONFERENCE_ID');
```

### Partner

```php
$connector->partner()->clients();
$connector->partner()->turnovers(2026, 3);
$connector->partner()->creditInvoices();
```

### Others

```php
$connector->others()->root();
$connector->others()->ipranges();
```

### Direct / Fallback Requests

For endpoints not yet wrapped by a typed method:

```php
// Global
$connector->request('GET', '/some/new-endpoint', ['key' => 'value']);

// Scoped to a resource prefix
$connector->conferences()->request('GET', '/future-endpoint', ['key' => 'value']);
```

## Architecture

```
src/
  SystemApiConnector.php            # Entry facade — exposes all resource clients
  Configuration/
    SystemApiConfiguration.php      # Base URL + API key holder
  Http/
    HttpTransportInterface.php      # Transport abstraction
    CurlHttpTransport.php           # cURL implementation
    JsonHttpClient.php              # JSON request/response handling, error mapping
    ApiRequest.php                  # Request value object
    ApiResponse.php                 # Response value object
  Domain/
    AbstractResourceClient.php      # Base class for resource clients
    ConferenceRoomsClient.php       # /conference-rooms/*
    ConferencesClient.php           # /conferences/*
    CustomerSettingsClient.php      # /customer-settings/*
    ModeratorGroupsClient.php       # /moderator-groups/*
    ModeratorsClient.php            # /moderators/*
    RecordingsClient.php            # /recordings/*
    UserAttendanceClient.php        # /user-attendence/*
    InvoicesClient.php              # /invoices/*
    PartnerClient.php               # /partner/*
    OthersClient.php                # / and /others/*
  Exception/
    SystemApiException.php          # Base exception (carries HTTP status + response payload)
    AuthenticationException.php     # 401 / 403
    TransportException.php          # Network / cURL failures
    UnexpectedResponseException.php # Non-JSON responses on JSON endpoints
```

## Error Handling

All API errors throw typed exceptions extending `SystemApiException`:

```php
use BbbServer\SystemApiConnector\Exception\AuthenticationException;
use BbbServer\SystemApiConnector\Exception\SystemApiException;

try {
    $connector->conferenceRooms()->listConferenceRooms();
} catch (AuthenticationException $e) {
    // Invalid or missing API key (HTTP 401/403)
    echo $e->statusCode();       // 401
    echo $e->responsePayload();  // decoded JSON error body
} catch (SystemApiException $e) {
    // Any other API error (4xx/5xx)
    echo $e->getMessage();
}
```

## Development

Install dependencies:

```bash
composer install
```

Run all tests:

```bash
composer test
```

Run only unit tests (no network access required):

```bash
composer test:unit
```

Run integration tests (requires a live API key):

```bash
export BBBSERVER_SYSTEMAPI_BASE_URL="https://app.bbbserver.de/bbb-system-api"
export BBBSERVER_SYSTEMAPI_KEY="YOUR_API_KEY"
composer test:integration
```

Integration tests are **skipped automatically** when credentials are not set. Tests that require premium features (partner endpoints, moderator groups management) are skipped when the account does not support them.

Generate a coverage report:

```bash
composer test:coverage
```

## License

MIT
