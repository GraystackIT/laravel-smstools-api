# Changelog

All notable changes to `graystackit/laravel-smstools-api` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.1] - 2026-06-19

### Fixed
- `AddContactRequest` and `UpdateContactRequest` now send `phone` instead of `number` to match the API field name; previously every contact create/update silently sent the wrong field
- `AddTemplateRequest` and `UpdateTemplateRequest` now send `message` (required) and `title` (optional label) instead of the incorrect `template`/`name` field names; `AddTemplateRequest` also requires `order` (int ≥ 1) which the API mandates
- `ContactResource::add()` signature updated: `$phone` and `$groupid` are now the required positional arguments (API requires both); `$firstname` is optional
- `TemplateResource::add()` signature updated: `add(string $message, int $order, ?string $title = null)`
- `TemplateResource::update()` signature updated: `update(int $id, ?string $message = null, ?string $title = null)`

### Added
- `AddContactRequest` and `UpdateContactRequest` now accept `birthday` (string, `yyyy-MM-dd`), `unsubscribed` (bool), and `extra` (array of `extra1`–`extra8` custom fields)
- Invalid `extra` array keys are rejected with `InvalidArgumentException` at construction time

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
