# Slim 3 Session

[![Latest Version on Packagist](https://img.shields.io/github/release/odan/slim-session.svg)](https://github.com/odan/slim-session/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE)
[![Build Status](https://travis-ci.org/odan/slim-session.svg?branch=master)](https://travis-ci.org/odan/slim-session)
[![Code Coverage](https://scrutinizer-ci.com/g/odan/slim-session/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/odan/slim-session/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/odan/slim-session/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/odan/slim-session/?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/odan/slim-session.svg)](https://packagist.org/packages/odan/slim-session)


## Installation

```
composer require odan/slim-session
```

## Integration

In your `config/container.php` or wherever you add your service factories:

```php
use Odan\Slim\Session\Adapter\MemorySessionAdapter;
use Odan\Slim\Session\Adapter\PhpSessionAdapter;
use Odan\Slim\Session\Session;

$container[Session::class] = function (Container $container) {
    $settings = $container->get('settings');
    $adapter = php_sapi_name() === 'cli' ? new MemorySessionAdapter() : new PhpSessionAdapter();
    $session = new Session($adapter);
    $session->setOptions($settings['session']);
    return $session;
};
```

Register the middleware:

```php
// Session middleware
$app->add(function (Request $request, Response $response, $next) {
    /* @var Container $this */
    $session = $this->get(Session::class);
    $session->start();
    $response = $next($request, $response);
    $session->save();
    return $response;
});
```

## Usage

```php
// Get session variable:
$foo = $session->get('foo', 'some-default');

// Set session variable:
$session->set('bar', 'that');

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

### PhpSessionAdapter

* The default PHP session handler
* Uses the native PHP session functions

Example:

```php
use Odan\Slim\Session\Adapter\PhpSessionAdapter;
use Odan\Slim\Session\Session;

$session = new Session(new PhpSessionAdapter());
```

### PhpSecureSessionAdapter

* Longer and more secure session id's
* Session data encryption
* Set session cookie path, domain and secure values automatically

Example:

```php
use Odan\Slim\Session\Adapter\PhpSecureSessionAdapter;
use Odan\Slim\Session\Session;

// Generate a random encryption key.
// Load this key from your settings.
$key = random_bytes(64);

// Create a secure session instance
$session = new Session(new PhpSecureSessionAdapter($key));
```

### MemorySessionAdapter

* Optimized for integration tests (with phpunit)
* Prevent output buffer issues
* Run sessions only in memory

```php
use Odan\Slim\Session\Adapter\MemorySessionAdapter;
use Odan\Slim\Session\Session;

$session = new Session(new MemorySessionAdapter());
```

## Options

You can use all the standard PHP session configuration options: 

http://php.net/manual/en/session.configuration.php

Example:

```php
$session = new Session(new PhpSessionAdapter());

$session->setOptions([
    'name' => 'slim_app',
    // turn off automatic sending of cache headers entirely
    'cache_limiter' => '',
    // garbage collection
    'gc_probability' => 1,
    'gc_divisor' => 1,
    'gc_maxlifetime' => 30 * 24 * 60 * 60,
]);

$session->start();
```
