<?php

namespace Odan\Test;

use Odan\Slim\Session\Adapter\MemorySessionAdapter;
use Odan\Slim\Session\Session;

/**
 * MemorySessionTest
 *
 * @coversDefaultClass \Odan\Slim\Session\Adapter\MemorySessionAdapter
 */
class MemorySessionAdapterTest extends PhpSessionAdapterTest
{
    /** {@inheritdoc} */
    protected function setUp(): void
    {
        $this->session = new Session(new MemorySessionAdapter());

        $this->session->setOptions([
            'name' => 'app',
            // turn off automatic sending of cache headers entirely
            'cache_limiter' => '',
            // garbage collection
            'gc_probability' => 1,
            'gc_divisor' => 1,
            'gc_maxlifetime' => 30 * 24 * 60 * 60,
        ]);

        $lifetime = strtotime('20 minutes') - time();
        $this->session->setCookieParams($lifetime, '/', '', false, false);

        $this->session->setName('app');
    }
}
