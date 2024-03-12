# Changelog

All notable changes to `laravel-validation-provider` will be documented in this file.

## v3.0.0 - 2024-03-12
### Changed
- Laravel 11 support.
  - Minimum PHP 8.2
- Rely on `\Illuminate\Support\Arr::mapWithKeys` instead of own implementation.

## v2.1.0 - 2024-03-08
### Added
- Ability to define validation `rules`, `messages` and `attributes` by class properties.
- New core validation provider.
    - `MapAttributesValidationProvider`.
- Fluent API.
    - `map()`
- Documentation updates.

## v2.0.0 - 2024-03-05
### Added
- Fluent API.
- Facade.
- New core validation providers.
    - `ArrayValidationProvider`.
    - `CustomValidationProvider`.
    - `ExcludeAttributesValidationProvider`.
- Major documentation updates.

## v1.0.0 - 2024-03-02
- Initial release.
