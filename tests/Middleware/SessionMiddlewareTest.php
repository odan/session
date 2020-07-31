<?php

namespace Odan\Session\Test\Middleware;

use Middlewares\Utils\Dispatcher;
use Odan\Session\Interfaces\SessionInterface;
use Odan\Session\Middleware\SessionMiddleware;
use Odan\Session\Session;
use PHPUnit\Framework\TestCase;
use Slim\Http\Environment;
use Slim\Http\Headers;
use Slim\Http\Request;
use Slim\Http\RequestBody;
use Slim\Http\UploadedFile;
use Slim\Http\Uri;

/**
 * Test.
 *
 * @coversDefaultClass \Odan\Session\Middleware\SessionMiddleware
 */
class SessionMiddlewareTest extends TestCase
{
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var SessionMiddleware
     */
    private $middleware;

    /** {@inheritdoc} */
    protected function setUp(): void
    {
        $_SESSION = [];

        $this->session = new Session();
        $this->session->setOptions([
            'name' => 'app',
            // turn off automatic sending of cache headers entirely
            'cache_limiter' => '',
            // garbage collection
            'gc_probability' => 1,
            'gc_divisor' => 1,
            'gc_maxlifetime' => 30 * 24 * 60 * 60,
        ]);

        $this->session->setName('app');

        $this->middleware = new SessionMiddleware($this->session);
    }

    /**
     * Test.
     *
     * @return void
     * @covers ::__construct
     * @covers ::process
     */
    public function testInvoke(): void
    {
        // Session must not be started
        $this->assertFalse($this->session->isStarted());

        $response = Dispatcher::run([
            new SessionMiddleware($this->session),
        ]);

        // Session must be closed
        $this->assertFalse($this->session->isStarted());
    }
}
