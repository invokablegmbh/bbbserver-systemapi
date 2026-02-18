# AGENTS.md

## Mission
Build and maintain a **Composer-installable PHP connector module** for the **bbbserver SystemAPI** (BigBlueButton ecosystem), with professional, modular code and comprehensive automated tests.

## Source of Truth
Use these references as canonical API documentation:
- bbbserver SystemAPI Postman docs: https://documenter.getpostman.com/view/7658590/T1DwdET1?version=latest

Prefer SystemAPI endpoints for advanced functionality beyond BigBlueButton core API scope.

## Required Deliverables
1. Composer package with PSR-4 autoloading.
2. Modular PHP connector architecture (clean separation of concerns).
3. Strongly typed public API where practical (PHP 8+), predictable exceptions.
4. Full PHPUnit test coverage for implemented code paths.
5. Integration/e2e tests that run when an API key is provided via environment variable.
6. Minimal but complete documentation for install, configuration, usage, and testing.

## Design Principles
- Follow Clean Code principles.
- Prefer expressive, long-speaking variable and method names.
- Keep classes focused and cohesive.
- Avoid unnecessary comments; code should be self-explanatory.
- Avoid God classes; split by domain (e.g., meetings, users, series, scheduling).
- Depend on abstractions for HTTP transport and serialization.

## Suggested Package Structure
- `src/SystemApiConnector.php` (entry facade)
- `src/Http/*` (HTTP client abstractions and implementations)
- `src/Domain/*` (resource clients and DTOs)
- `src/Exception/*` (typed exception hierarchy)
- `tests/Unit/*` (full unit coverage)
- `tests/Integration/*` (real API checks, gated by env vars)

## Testing Contract
- Unit tests must not require network access.
- Integration tests must be skipped unless required credentials are present.
- Required env vars for integration tests:
  - `BBBSERVER_SYSTEMAPI_BASE_URL`
  - `BBBSERVER_SYSTEMAPI_KEY`
- CI should run unit tests always; integration tests optional via explicit flag.

## Quality Gates
- `composer validate`
- `composer test`
- Optional static analysis: `composer analyse`
- Optional code style check: `composer cs`

## Non-Goals
- Do not implement undocumented behavior.
- Do not add unrelated framework dependencies.
- Do not hardcode credentials or environment-specific URLs.

## Versioning and Compatibility
- Follow semantic versioning.
- Document supported PHP versions in `composer.json`.
- Keep backward-compatible public APIs within minor versions.
