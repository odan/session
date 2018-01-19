<?php

namespace Odan\Test;

use Odan\Slim\Session\Adapter\PhpSecureSessionAdapter;
use Odan\Slim\Session\Session;

/**
 * MemorySessionTest
 *
 * @coversDefaultClass \Odan\Slim\Session\Adapter\PhpSecureSessionAdapter
 */
class PhpSecureSessionAdapterTest extends PhpSessionAdapterTest
{
    /** {@inheritdoc} */
    protected function setUp()
    {
        $key = random_bytes(64);
        $this->session = new Session(new PhpSecureSessionAdapter($key));

        $this->session->setOptions([
            'name' => 'slim_app',
            //'encryption_key' => random_bytes(64),
            // turn off automatic sending of cache headers entirely
            'cache_limiter' => '',
            // garbage collection
            'gc_probability' => 1,
            'gc_divisor' => 1,
            'gc_maxlifetime' => 30 * 24 * 60 * 60,
        ]);

        $lifetime = strtotime('20 minutes') - time();
        $this->session->setCookieParams($lifetime, '/', '', false, false);

        $this->session->setName('slim_app');
    }

    /**
     * Test.
     *
     * @return void
     * @covers ::__construct
     * @covers ::set
     * @covers ::get
     * @covers ::encrypt
     * @covers ::decrypt
     */
    public function testSetAndGet()
    {
        parent::testSetAndGet();
    }

    /**
     * Test.
     *
     * @return void
     * @covers ::__construct
     * @covers ::set
     * @covers ::get
     * @covers ::replace
     * @covers ::encrypt
     * @covers ::decrypt
     */
    public function testRemoveAndClear()
    {
        parent::testRemoveAndClear();
    }
}
