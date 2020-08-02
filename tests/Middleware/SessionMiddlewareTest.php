<?php

namespace Odan\Session\Test\Middleware;

use Middlewares\Utils\Dispatcher;
use Odan\Session\Middleware\SessionMiddleware;
use Odan\Session\PhpSession;
use Odan\Session\SessionInterface;
use PHPUnit\Framework\TestCase;

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

        $this->session = new PhpSession();
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
     * @covers ::__construct
     * @covers ::process
     */
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
