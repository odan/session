# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

v6.x

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

## [1.1.0] - 2019-02-15

### Added

- Danish translation from [@frederikspang](https://github.com/frederikspang).
- Georgian translation from [@tatocaster](https://github.com/tatocaster).
- Changelog inconsistency section in Bad Practices

### Changed

- Fixed typos in Italian translation from [@lorenzo-arena](https://github.com/lorenzo-arena).
- Fixed typos in Indonesian translation from [@ekojs](https://github.com/ekojs).

## [1.0.0] - 2017-06-20

### Added
