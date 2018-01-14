<?php

namespace Odan\Test;

use Odan\Slim\Session\Adapter\PhpSessionAdapter;
use Odan\Slim\Session\Session;

/**
 * MemorySessionTest
 *
 * @coversDefaultClass \Odan\Slim\Session\Session
 */
class PhpSessionAdapterTest extends AbstractTestCase
{
    /**
     * @var Session
     */
    private $session;

    private $settings = [];

    public static function setUpBeforeClass()
    {
        ini_set('session.gc_probability', 1);
        ini_set('session.gc_divisor', 1);
        ini_set('session.gc_maxlifetime', 30 * 24 * 60 * 60);
    }

    public static function tearDownAfterClass()
    {

    }

    /** {@inheritdoc} */
    protected function setUp()
    {
        $_SESSION = [];

        $settings = [
            'lifetime' => '20 minutes',
            'path' => '/',
            'domain' => null,
            'secure' => false,
            'httponly' => false,
            'name' => 'slim_session',
            'autorefresh' => false,
            'handler' => null,
        ];

        if (is_string($lifetime = $settings['lifetime'])) {
            $settings['lifetime'] = strtotime($lifetime) - time();
        }

        $this->settings = $settings;

        session_set_cookie_params(
            $settings['lifetime'],
            $settings['path'],
            $settings['domain'],
            $settings['secure'],
            $settings['httponly']
        );
        session_cache_limiter(false);

        //$this->session = new Session(new MemorySessionAdapter());
        $this->session = new Session(new PhpSessionAdapter());

        $name = $settings['name'];
        $this->session->setName($name);
    }

    /** {@inheritdoc} */
    protected function tearDown()
    {
        $this->session = null;
    }

    /**
     * Test.
     *
     * @return void
     * @covers ::__construct
     */
    public function testInstance()
    {
        $this->assertInstanceOf(Session::class, $this->session);
    }

    /**
     * Test.
     *
     * @return void
     * @covers ::__construct
     * @covers ::start
     * @covers ::isStarted
     * @covers ::destroy
     * @covers ::save
     * @covers \Odan\Slim\Session\Adapter\PhpSessionAdapter::start
     * @covers \Odan\Slim\Session\Adapter\PhpSessionAdapter::isStarted
     * @covers \Odan\Slim\Session\Adapter\PhpSessionAdapter::destroy
     * @covers \Odan\Slim\Session\Adapter\PhpSessionAdapter::save
     */
    public function testStart()
    {
        $this->assertTrue($this->session->start());
        $this->assertTrue($this->session->isStarted());
        $this->assertTrue($this->session->destroy());
        $this->session->save();
    }

}
