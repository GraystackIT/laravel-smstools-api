# Changelog

All notable changes to `graystack/laravel-smstools-api` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2025-04-07

### Added
- Initial release
- `SmstoolsClient` with `messages()` resource accessor
- `MessageResource::send()` supporting single and bulk recipients, scheduled sending, custom references, test mode, and subaccount routing
- `SmstoolsConnector` built on Saloon 4 with `X-Client-Id` / `X-Client-Secret` header authentication
- `SendMessageRequest` Saloon 4 request with `HasJsonBody`
- `SmstoolsException` with 30+ mapped API error codes, HTTP status exposure via `getStatusCode()`, and API error code exposure via `getApiErrorCode()`
- `Smstools` facade for convenient static access
- Laravel service provider with auto-discovery
- Config validation: clear `RuntimeException` when `SMSTOOLS_CLIENT_ID` or `SMSTOOLS_CLIENT_SECRET` is missing
