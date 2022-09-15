<?php

namespace Odan\Session\Test;

use Middlewares\Utils\Dispatcher;
use Odan\Session\Middleware\SessionMiddleware;
use Odan\Session\PhpSession;
use PHPUnit\Framework\TestCase;

/**
 * Test.
 *
 * @coversDefaultClass \Odan\Session\Middleware\SessionMiddleware
 */
class SessionMiddlewareTest extends TestCase
{
    private PhpSession $session;

    private SessionMiddleware $middleware;

    protected function setUp(): void
    {
        $_SESSION = [];

        $this->session = new PhpSession([
            'name' => 'app',
            // turn off automatic sending of cache headers entirely
            'cache_limiter' => '',
            // garbage collection
            'gc_probability' => 1,
            'gc_divisor' => 1,
            'gc_maxlifetime' => 30 * 24 * 60 * 60,
            'save_path' => getenv('GITHUB_ACTIONS') ? '/tmp' : '',
        ]);

        $this->middleware = new SessionMiddleware($this->session);
    }

    public function testInvoke(): void
    {
        // Session must not be started
        $this->assertFalse($this->session->isStarted());

        Dispatcher::run([
            $this->middleware,
        ]);

        // Session must be closed
        $this->assertFalse($this->session->isStarted());
    }
}
