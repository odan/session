# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [6.3.0] - 2024-12-15

* Add support for PHP 8.4

## [6.2.0] - 2024-09-20

### Added

* Add support for PHP 8.3
* Add support for psr/http-message 2.x

### Changes

* Upgrade tests to PHPUnit 11

### Removed

* Drop support for PHP 8.0 and 8.1

## [6.1.0] - 2023-02-22

### Added

* Add `has` method to `SessionInterface` #30 #29
* Add PHP 8.2 to build pipeline

### Changed

* Update docs

## [6.0.0] - 2022-12-04

### Changes

* Require PHP 8.0+
* Make session settings "immutable".
* Move all session settings to the `PhpSession` constructor.
* Provide interfaces for each concern (management and session data).
* Change `SessionInterface` to handle session data operations only, e.g. `get`, `set`.
* Rename session method `replace` to `setValues`.
* Rename session method `remove` to `delete`.
* Calling the session `save` method is now optional.
* Rename class `Odan\Session\Middleware\SessionMiddleware` to `Odan\Session\Middleware\SessionStartMiddleware`.

### Added

* Add `SessionManagerInterface` to handle session operations, such as `start`, `save`, `destroy`, `getName`, etc.
* Add `default` parameter to session `get` method.

### Removed

* Remove session method `setOptions` and `getOptions`. Pass all settings into `PhpSession` constructor instead.
* Remove session method `setCookieParams` and `getCookieParams`. The cookie parameters must be 
  defined in the settings and will set in the session `start` method.
* Remove session `setName` method. Use the `name` setting instead.
* Remove session `setId` method. Use the optional `id` setting instead.
* Remove session `count` method.
* Remove `SessionAwareInterface` in favor of dependency injection.

