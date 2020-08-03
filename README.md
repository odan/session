# Session handler

A session handler for PHP

[![Latest Version on Packagist](https://img.shields.io/github/release/odan/session.svg)](https://github.com/odan/session/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE)
[![Build Status](https://travis-ci.org/odan/session.svg?branch=master)](https://travis-ci.org/odan/session)
[![Build Status](https://github.com/odan/session/workflows/build/badge.svg)](https://github.com/odan/session/actions)
[![Code Coverage](https://scrutinizer-ci.com/g/odan/session/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/odan/session/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/odan/session/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/odan/session/?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/odan/session.svg)](https://packagist.org/packages/odan/session/stats)

## Table of contents

* [Requirements](#requirements)
* [Installation](#installation)
* [Usage](#usage)
* [Methods](#methods)
* [Flash messages](#flash-messages)
  * [Twig flash messages](twig-flash-messages)
* [SameSite Cookies](#samesite-cookies)
* [Adapter](#adapter)
  * [PHP Session](#php-session)
  * [Memory Session](#memory-session)
* [Slim 4 integration](#slim-4-integration)
* [Slim Flash integration](#slim-flash-integration)
* [Similar packages](#similar-packages)
* [License](#license)

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

### Usage

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
* https://www.php.net/manual/en/session.configuration.php#ini.session.cookie-samesite
* https://www.php.net/manual/en/session.configuration.php#ini.session.cookie-httponly
* https://www.php.net/manual/en/session.configuration.php#ini.session.cookie-secure

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
        'cookie_httponly' => true,
        'cookie_secure' => true,
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

## Slim Flash integration

Although this component already comes with its own Flash message implementation, 
you can still integrate other components. 

The [slim/flash](https://github.com/slimphp/Slim-Flash) may be useful integration package to 
add flash massages to your application.

```
composer require slim/flash
```

Add the container definition:

```php

use Slim\Flash\Messages;

return [
    // ...
    Messages::class => function () {
        // Don't use $_SESSION here, because the session is not started at this moment.
        // The middleware changes the storage. 
        $storage = [];

        return new Messages($storage);
    },
];
```

Add a custom Middleware to pass the session storage, e.g. in `src/Middleware/SlimFlashMiddleware.php`:

```php
<?php

namespace App\Middleware;

use Odan\Session\SessionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Flash\Messages;

final class SlimFlashMiddleware implements MiddlewareInterface
{
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var Messages
     */
    private $flash;

    public function __construct(SessionInterface $session, Messages $flash)
    {
        $this->session = $session;
        $this->flash = $flash;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $storage = $this->session->getStorage();

        // Set flash message storage
        $this->flash->__construct($storage);

        return $handler->handle($request);
    }
}
```

The session must be started first. To prevent an error like 
`Fatal error: Uncaught RuntimeException: Flash messages middleware failed. Session not found.`
add the `SessionFlashMiddleware` middleware **before** the `SessionMiddleware`.

```php
$app->add(SessionFlashMiddleware::class); // <--- here
$app->add(SessionMiddleware::class);
```

Action usage:

```php
<?php

namespace App\Action\Auth;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Flash\Messages;

final class FooAction
{
    /**
     * @var Messages
     */
    private $flash;

    public function __construct(Messages $flash)
    {
        $this->flash = $flash;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface {
        // Add flash message for the next request
        $this->flash->addMessage('Test', 'This is a message');


        // or add flash message for current request
        $this->flash->addMessageNow('Test', 'This is a message');

        // Render response
        // ...

        return $response;
    }
}

```

### Using Slim Flash with Twig-View

If you use [Twig-View](https://github.com/slimphp/Twig-View),
then [slim-twig-flash](https://github.com/kanellov/slim-twig-flash) may be a useful integration package.

You could also just add the Slim flash instance as global twig variable:

```php
use Slim\Flash\Messages;
// ...

$twig = Twig::create($settings, $options);
// ...

$flash = $container->get(Messages::class);
$twig->getEnvironment()->addGlobal('flash', $flash);
```

In your Twig templates you can use `flash.getMessages()` or `flash.getMessage('some_key')` 
to fetch messages from the Flash service.

{% raw %}
```twig
{% for message in flash.getMessage('error') %}
    <div class="alert alert-danger" role="alert">
        {{ message }}
    </div>
{% endfor %}
```
{% endraw %}

## Similar packages

* https://github.com/laminas/laminas-session
* https://github.com/psr7-sessions/storageless
* https://github.com/dflydev/dflydev-fig-cookies
* https://github.com/bryanjhv/slim-session
* https://github.com/auraphp/Aura.Session
* https://symfony.com/doc/current/components/http_foundation/sessions.html

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
