# Session handler

A session handler for PHP

[![Latest Version on Packagist](https://img.shields.io/github/release/odan/session.svg)](https://github.com/odan/session/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE)
[![Build Status](https://travis-ci.org/odan/session.svg?branch=master)](https://travis-ci.org/odan/session)
[![Code Coverage](https://scrutinizer-ci.com/g/odan/session/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/odan/session/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/odan/session/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/odan/session/?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/odan/session.svg)](https://packagist.org/packages/odan/session/stats)

## Table of contents

* [Requirements](#requirements)
* [Installation](#installation)
* [Usage](#usage)
* [Methods](#methods)
* [Adapter](#adapter)
  * [PHP Session](#php-session)
  * [Memory Session](#memory-session)
* [Slim 4 integration](#slim-4-integration)
* [Flash messages](#flash-messages)
* [Similar packages](#similar-packages)
* [License](#license)

## Requirements

* PHP 7.2+

## Installation

```
composer require odan/session
```

## Usage

```php
use Odan\Session\PhpSession;

// Set session options before you start the session
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
use Odan\Session\SessionMiddleware;
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

### Registering middleware routes

Register middleware for all routes:

```php
use Odan\Session\SessionMiddleware;

$app->add(SessionMiddleware::class);
```

Register middleware for a routing group:

```php
use Odan\Session\SessionMiddleware;
use Slim\Routing\RouteCollectorProxy;

// Protect the whole group
$app->group('/admin', function (RouteCollectorProxy $group) {
    // ...
})->add(SessionMiddleware::class);
```

Register middleware for a single route:

```php
use Odan\Session\SessionMiddleware;

$app->post('/example', \App\Action\ExampleAction::class)
    ->add(SessionMiddleware::class);
```

## Flash messages

The [slim/flash](https://github.com/slimphp/Slim-Flash) may be a useful integration package to 
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
        return new Messages();
    },
];
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

### Using with Twig-View

If you use [Twig-View](https://github.com/slimphp/Twig-View),
then [slim-twig-flash](https://github.com/kanellov/slim-twig-flash) may be a useful integration package.

You could also just add the Slim flash instance as global twig variable:

```php
use Slim\Flash\Messages;
// ...

$twig = Twig::create($settings, $options);
// ...

$flash = $container->get(Messages::class);
$environment = $twig->getEnvironment()->addGlobal('flash', $flash);
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
* https://github.com/dflydev/dflydev-fig-cookies
* https://github.com/bryanjhv/slim-session
* https://symfony.com/doc/current/components/http_foundation/sessions.html

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
