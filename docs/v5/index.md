---
layout: default
title: Version 5
nav_order: 3
description: "Version 5"
---

# Session v5 Documentation

## Table of contents

* [Requirements](#requirements)
* [Installation](#installation)
* [Usage](#usage)
* [Methods](#methods)
* [Flash messages](#flash-messages)
  * [Twig flash messages](#twig-flash-messages)
* [SameSite Cookies](#samesite-cookies)
* [Adapter](#adapter)
  * [PHP Session](#php-session)
  * [Memory Session](#memory-session)
* [Slim 4 integration](#slim-4-integration)

## Requirements

* PHP 7.3+

## Installation

```
composer require odan/session
```

## Usage

```php
use Odan\Session\PhpSession;

// Create a standard session hanndler
$session = new PhpSession();

// Set session options before you start the session
// You can use all the standard PHP session configuration options
// https://secure.php.net/manual/en/session.configuration.php

$session->setOptions([
    'name' => 'app',
]);

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

// Clears all session
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

## Flash messages

The library provides its own implementation of Flash messages.

```php
// Get flash object
$flash = $session->getFlash();

// Clear all flash messages
$flash->clear();

// Add flash message
$flash->add('error', 'Login failed');

// Get flash messages
$messages = $flash->get('error');

// Has flash message
$has = $flash->has('error');

// Set all messages
$flash->set('error', ['Message 1', 'Message 2']);

// Gets all flash messages
$messages = $flash->all();
```

### Twig flash messages

Add the Flash instance as global twig variable within the `Twig::class` container definiton:

```php
use Odan\Session\SessionInterface;

// ...

$flash = $container->get(SessionInterface::class)->getFlash();
$twig->getEnvironment()->addGlobal('flash', $flash);
```

Twig template example:

{% raw %}
```twig
{% for message in flash.get('error') %}
    <div class="alert alert-danger" role="alert">
        {{ message }}
    </div>
{% endfor %}
```
{% endraw %}

## SameSite Cookies

A SameSite cookie that tells browser to send the cookie to the server only 
when the request is made from the same domain of the website.

```php
use Odan\Session\PhpSession;

$session = new PhpSession();

$session->setOptions([
    'name' => 'app',
    // Lax will sent the cookie for cross-domain GET requests
    'cookie_samesite' => 'Lax',   
    // Optional: Sent cookie only over https
    'cookie_secure' => true,
    // Optional: Additional XSS protection
    // Note: The cookie is not accessible for JavaScript!
    'cookie_httponly' => false,
]);

$session->start();
```

Read more:

* [SameSite cookie middleware](https://github.com/selective-php/samesite-cookie)
* <https://www.php.net/manual/en/session.configuration.php#ini.session.cookie-samesite>
* <https://www.php.net/manual/en/session.configuration.php#ini.session.cookie-httponly>
* <https://www.php.net/manual/en/session.configuration.php#ini.session.cookie-secure>

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

## Slim 4 Integration

### Configuration

Add your application-specific settings:

```php
// config/settings.php

return [

    // ...

    'session' => [
        'name' => 'webapp',
        'cache_expire' => 0,
    ],
];
```

For this example we use the [PHP-DI](http://php-di.org/) package.

Add the container definitions as follows:

```php
<?php

use Odan\Session\PhpSession;
use Odan\Session\SessionInterface;
use Odan\Session\Middleware\SessionMiddleware;
use Psr\Container\ContainerInterface;

return [
    // ...

    SessionInterface::class => function (ContainerInterface $container) {
        $settings = $container->get('settings');
        $session = new PhpSession();
        $session->setOptions((array)$settings['session']);

        return $session;
    },

    SessionMiddleware::class => function (ContainerInterface $container) {
        return new SessionMiddleware($container->get(SessionInterface::class));
    },
];
```

### Session middleware

Register the session middleware for all routes:

```php
use Odan\Session\Middleware\SessionMiddleware;

$app->add(SessionMiddleware::class);
```

Register middleware for a routing group:

```php
use Odan\Session\Middleware\SessionMiddleware;
use Slim\Routing\RouteCollectorProxy;

// Protect the whole group
$app->group('/admin', function (RouteCollectorProxy $group) {
    // ...
})->add(SessionMiddleware::class);
```

Register middleware for a single route:

```php
use Odan\Session\Middleware\SessionMiddleware;

$app->post('/example', \App\Action\ExampleAction::class)
    ->add(SessionMiddleware::class);
```

## Similar packages

* <https://github.com/laminas/laminas-session>
* <https://github.com/psr7-sessions/storageless>
* <https://github.com/dflydev/dflydev-fig-cookies>
* <https://github.com/bryanjhv/slim-session>
* <https://github.com/auraphp/Aura.Session>
* <https://symfony.com/doc/current/components/http_foundation/sessions.html>