# Contributing

Thanks for your interest in improving this connector.

## Prerequisites

- PHP 7.4+
- Composer

## Setup

```bash
composer install
```

## Test Commands

Run all tests:

```bash
composer test
```

Run unit tests only:

```bash
composer test:unit
```

Run integration tests (requires credentials):

```bash
export BBBSERVER_SYSTEMAPI_BASE_URL="https://app.bbbserver.de/en/bbb-system-api"
export BBBSERVER_SYSTEMAPI_KEY="YOUR_SYSTEMAPI_KEY"
composer test:integration
```

Integration tests are skipped automatically when credentials are not available.

## Contribution Guidelines

- Keep changes small and focused.
- Follow PSR-4 and strict typing conventions already used in `src/`.
- Add or update unit tests for all code-path changes.
- Avoid undocumented API behavior.

## Pull Requests

- Describe the problem and solution clearly.
- Include relevant test output.
- Ensure `composer validate --strict` and `composer test:unit` pass locally.