---
layout: default
title: Version 6
nav_order: 4
description: "Version 6"
---

# Session v6 Documentation

## Table of contents

* [Requirements](#requirements)
* [Installation](#installation)
* [Features](#features)
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

* PHP 8.0+

## Installation

```
composer require odan/session
```

## Features

* PSR-7 and PSR-15 (middleware) support
* DI container (PSR-11) support
* Lazy session start

## Usage

```php
$config = [
    'name' => 'app',
];

// Create a standard session handler
$session = new \Odan\Session\PhpSession($config);

// Start the session
$session->start();

// Set session value
$session->set('bar', 'foo');

// Get session value
echo $session->get('bar'); // foo

// Add flash message
$session->getFlash()->add('error', 'My flash message')
```

## Methods

```php
// Get session variable
$foo = $session->get('foo');

// Get session variable or the default value
$bar = $session->get('bar', 'my default value');

// Set session variable
$session->set('bar', 'new value');

// Sets multiple values at once
$session->setValues(['foo' => 'value1', 'bar' => 'value2']);

// Get all session variables
$values = $session->all();

// Returns true if the attribute exists
$hasKey = $session->has('foo');

// Delete a session variable
$session->delete('key');

// Clear all session variables
$session->clear();

// Generate a new session ID
$session->regenerateId();

// Get the current session ID
$sessionId = $session->getId();

// Get the session name
$sessionName = $session->getName();

// Force the session to be saved and closed
$session->save();
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

To display the Flash messages, you can pass the Flash 
object in the array of options as the second argument:

```php
$flash = $session->getFlash();
$html = $twig->render('filename.html.twig', ['flash' => $flash]);
```

Another approach would be to add the Flash instance 
as global Twig variable within the DI container definition of `Twig::class`:

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

$options = [
    'name' => 'app',
    // Lax will send the cookie for cross-domain GET requests
    'cookie_samesite' => 'Lax',   
    // Optional: Send cookie only over https
    'cookie_secure' => true,
    // Optional: Additional XSS protection
    // Note: This cookie is not accessible in JavaScript!
    'cookie_httponly' => false,
];

$session = new PhpSession($options);
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
$settings['session'] = [
    'name' => 'app',
    'lifetime' => 7200,
    'path' => null,
    'domain' => null,
    'secure' => false,
    'httponly' => true,
    'cache_limiter' => 'nocache',
];
```

For this example we use the [PHP-DI](http://php-di.org/) package.

Add the container definitions as follows:

```php
<?php

use Odan\Session\PhpSession;
use Odan\Session\SessionInterface;
use Odan\Session\SessionManagerInterface;
use Psr\Container\ContainerInterface;

return [
    // ...

    SessionManagerInterface::class => function (ContainerInterface $container) {
        return $container->get(SessionInterface::class);
    },

    SessionInterface::class => function (ContainerInterface $container) {
        $options = $container->get('settings')['session'];

        return new PhpSession($options);
    },
];
```

### Session middleware

**Lazy session start**

The DI container should (must) never start a session automatically because:

* The DI container is not responsible for the HTTP context.
* In some use cases an API call from a REST client generates a session.
* Only an HTTP middleware or an action handler should start the session.

Register the session middleware for all routes:

```php
use Odan\Session\Middleware\SessionStartMiddleware;
//...

$app->add(SessionStartMiddleware::class);
```

Register middleware for a routing group:

```php
use Odan\Session\Middleware\SessionStartMiddleware;
use Slim\Routing\RouteCollectorProxy;

// Protect the whole group
$app->group('/admin', function (RouteCollectorProxy $group) {
    // ...
})->add(SessionStartMiddleware::class);
```

Register middleware for a single route:

```php
use Odan\Session\Middleware\SessionStartMiddleware;

$app->post('/example', \App\Action\ExampleAction::class)
    ->add(SessionStartMiddleware::class);
```
