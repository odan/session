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

// Get session variable or the default value
$bar = $session->get('bar') ?? 'my default value';

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

Add your application-specific settings. 

These are stored in the `settings` configuration key of Slim.

```php
// Session
$config['session'] = [
    'name' => 'webapp',
    'cache_expire' => 0,
    'cookie_httponly' => true,
    'cookie_secure' => true,
];
```

Add the session factory:

```php
use Odan\Session\PhpSession;
use Odan\Session\SessionInterface;
use Odan\Session\SessionMiddleware;
use Psr\Container\ContainerInterface as Container;

$container[SessionInterface::class] = function (Container $container) {
    $session = new PhpSession();
    
    // Optional settings
    $settings = $container->get('settings');
    $session->setOptions($settings['session']);
    
    return $session;
};
```

#### Middleware setup

Add the middleware factory:

```php
$container[SessionMiddleware::class] = function (Container $container) {
    return new SessionMiddleware($container->get(SessionInterface::class));
};
```

**Add the Slim application middleware**

Register middleware for all routes:

```php
$app->add(\Odan\Session\SessionMiddleware::class);
```

Register middleware for a single route:

```php
$this->get('/', \App\Action\HomeIndexAction::class)
    ->add(\Odan\Session\SessionMiddleware::class);
```

Register the middleware for a group of routes:

```php
$app->group('/users', function () {
    $this->post('/login', \App\Action\UserLoginSubmitAction::class);
    $this->get('/login', \App\Action\UserLoginIndexAction::class);
    $this->get('/logout', \App\Action\UserLogoutAction::class);
})->add(\Odan\Session\SessionMiddleware::class);
```

Get SessionInterface from Request object:

```php
/**
 * @var ResponseInterface $request
 * @var SessionInterface $session
 */
$session = $request->getAttribute('session');
```

## Similar packages

* https://symfony.com/doc/current/components/http_foundation/sessions.html
* https://github.com/auraphp/Aura.Session
