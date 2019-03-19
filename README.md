# Session handler

A session handler for PHP

[![Latest Version on Packagist](https://img.shields.io/github/release/odan/session.svg)](https://github.com/odan/session/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE)
[![Build Status](https://travis-ci.org/odan/session.svg?branch=master)](https://travis-ci.org/odan/session)
[![Code Coverage](https://scrutinizer-ci.com/g/odan/session/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/odan/session/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/odan/session/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/odan/session/?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/odan/session.svg)](https://packagist.org/packages/odan/session)

## Requirements

* PHP 7.2+

## Installation

```
composer require odan/session
```

## Usage

```php
use Odan\Session\PhpSession;

// Set session options before we start
// You can use all the standard PHP session configuration options
// https://secure.php.net/manual/en/session.configuration.php

$session->setOptions([
    'name' => 'app',
    // turn off automatic sending of cache headers entirely
    'cache_limiter' => '',
    // garbage collection
    'gc_probability' => 1,
    'gc_divisor' => 1,
    'gc_maxlifetime' => 30 * 24 * 60 * 60,
    // security on
    'cookie_httponly' => true,
    'cookie_secure' => true,
]);

// Create a standard session hanndler
$session = new PhpSession();

// Start the session
$session->start();

// Set session value
$session->set('bar', 'foo');

// Get session value
echo $session->get('bar'); // foo

// Optional: Force the session to be saved and closed
$session->save();
```

## Methods

```php
// Get session variable
$foo = $session->get('foo');

// Set session variable
$session->set('bar', 'that');

// Get all session variables
$all = $session->all();

// Delete a session variable
$session->remove('key');

// Clear all session variables
$session->clear();

// Generate a new session ID
$session->regenerateId();

// Clears all session data and regenerates session ID
$session->destroy();

// Get the current session ID
$session->getId();

// Set the session ID
$session->setId('...');

// Get the session name
$session->getName();

// Set the session name
$session->setName('my-app');

// Returns true if the attribute exists
$session->has('foo');

// Sets multiple values at once
$session->replace(['foo' => 'value1', 'bar' => 'value2']);

// Get the number of values.
$session->count();

// Force the session to be saved and closed
$session->save();

// Set session runtime configuration
// All supported keys: http://php.net/manual/en/session.configuration.php
$session->setOptions($options);

// Get session runtime configuration
$session->getOptions();

// Set cookie parameters
$session->setCookieParams(4200, '/', '', false, false);

// Get cookie parameters
$session->getCookieParams();
```

## Adapter

### PHP Session

* The default PHP session handler
* Uses the native PHP session functions

Example:

```php
use Odan\Session\PhpSession;

$session = new PhpSession();
```

### Memory Session

* Optimized for integration tests (with phpunit)
* Prevent output buffer issues
* Run sessions only in memory

```php
use Odan\Session\MemorySession;

$session = new MemorySession();
```

## Integration

### Slim 3 framework integration

#### Configuration

Add your application-specific settings. These are stored in the `settings` configuration key of Slim.

```php
// Session
$config['session'] = [
    'name' => 'webapp',
    'cache_expire' => 0,
    'cookie_httponly' => true,
    'cookie_secure' => true,
];
```

#### Container setup

In your `config/container.php` or wherever you add your service factories:

```php
use Odan\Session\PhpSession;
use Odan\Session\SessionInterface;

$container[SessionInterface::class] = function (Container $container) {
    $settings = $container->get('settings');
    $session = new PhpSession();
    $session->setOptions($settings['session']);
    
    return $session;
};
```

#### Middleware setup

Register the middleware:

```php
use Odan\Session\SessionInterface;

// Session middleware
$app->add(function (Request $request, Response $response, $next) {
    /* @var Container $this */
    $session = $this->get(SessionInterface::class);
    $session->start();
    $response = $next($request, $response);
    $session->save();
    
    return $response;
});
```
