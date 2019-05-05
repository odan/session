<?php

namespace Odan\Session\Test;

use Odan\Session\MemorySession;

/**
 * MemorySessionTest.
 *
 * @coversDefaultClass \Odan\Session\MemorySession
 */
class MemorySessionTest extends PhpSessionTest
{
    /** {@inheritdoc} */
    protected function setUp(): void
    {
        $this->session = new MemorySession();

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
