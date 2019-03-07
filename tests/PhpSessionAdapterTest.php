<?php

namespace Odan\Test;

use Odan\Session\Adapter\PhpSessionAdapter;
use Odan\Session\Session;
use RuntimeException;

/**
 * MemorySessionTest
 *
 * @coversDefaultClass \Odan\Session\Session
 */
class PhpSessionAdapterTest extends AbstractTestCase
{
    /**
     * @var Session|null
     */
    protected $session;

    /** {@inheritdoc} */
    protected function setUp(): void
    {
        $_SESSION = [];

        $this->session = new Session(new PhpSessionAdapter());

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

    /**
     * Test.
     *
     * @return void
     * @covers ::__construct
     * @covers ::start
     * @covers ::isStarted
     * @covers ::getId
     * @covers ::setId
     * @covers ::destroy
     * @covers ::save
     * @covers ::regenerateId
     * @covers \Odan\Session\Adapter\PhpSessionAdapter::start
     * @covers \Odan\Session\Adapter\PhpSessionAdapter::isStarted
     * @covers \Odan\Session\Adapter\PhpSessionAdapter::getId
     * @covers \Odan\Session\Adapter\PhpSessionAdapter::destroy
     * @covers \Odan\Session\Adapter\PhpSessionAdapter::save
     * @covers \Odan\Session\Adapter\PhpSessionAdapter::regenerateId
     * @covers \Odan\Session\Adapter\PhpSessionAdapter::setId
     */
    public function testStart(): void
    {
        $this->assertTrue($this->session->start());
        $this->assertTrue($this->session->isStarted());
        $this->assertNotEmpty($this->session->getId());

        $oldId = $this->session->getId();
        $this->assertTrue($this->session->regenerateId());
        $newId = $this->session->getId();
        $this->assertNotSame($oldId, $newId);

        $this->session->setId($oldId);
        $this->assertSame($oldId, $this->session->getId());

        $this->assertTrue($this->session->destroy());
        $this->session->save();
    }

    /**
     * Test.
     *
     * @return void
     * @covers ::__construct
     * @covers ::setName
     * @covers ::getName
     * @covers \Odan\Session\Adapter\PhpSessionAdapter::start
     * @covers \Odan\Session\Adapter\PhpSessionAdapter::setName
     * @covers \Odan\Session\Adapter\PhpSessionAdapter::getName
     */
    public function testSetAndGetName(): void
    {
        $this->session->setName('app');
        $this->session->start();
        $this->assertSame('app', $this->session->getName());
    }

    /**
     * Test.
     *
     * session_name(): Cannot change session name when session is active
     *
     * @return void
     * @covers ::__construct
     * @covers ::setName
     * @covers ::getName
     * @covers \Odan\Session\Adapter\PhpSessionAdapter::start
     * @covers \Odan\Session\Adapter\PhpSessionAdapter::setName
     * @covers \Odan\Session\Adapter\PhpSessionAdapter::getName
     */
    public function testSetAndGetNameError(): void
    {
        $this->expectException(RuntimeException::class);
        $this->session->start();
        $this->session->setName('app');
    }

    /** {@inheritdoc} */
    protected function tearDown(): void
    {
        $this->session->destroy();
        $this->session = null;
    }

    /**
     * Test.
     *
     * @return void
     * @covers ::__construct
     */
    public function testInstance(): void
    {
        $this->assertInstanceOf(Session::class, $this->session);
    }

    /**
     * Test.
     *
     * @return void
     * @covers ::__construct
     * @covers ::start
     * @covers ::set
     * @covers ::get
     * @covers \Odan\Session\Adapter\PhpSessionAdapter::start
     * @covers \Odan\Session\Adapter\PhpSessionAdapter::set
     * @covers \Odan\Session\Adapter\PhpSessionAdapter::get
     */
    public function testSetAndGet(): void
    {
        $this->session->start();

        // string
        $this->session->set('key', 'value');
        $this->assertSame('value', $this->session->get('key'));

        // int
        $valueInt = 1;
        $this->session->set('key', $valueInt);
        $valueInt = $this->session->get('key');
        $this->assertSame($valueInt, $valueInt);

        // float
        $this->session->set('key', 3.14);
        $this->assertSame(3.14, $this->session->get('key'));

        // bool
        $this->session->set('key', true);
        $this->assertTrue($this->session->get('key'));

        $this->session->set('key', false);
        $this->assertFalse($this->session->get('key'));
    }

    /**
     * Test.
     *
     * @return void
     * @covers ::__construct
     * @covers ::start
     * @covers ::set
     * @covers ::get
     * @covers ::count
     * @covers ::remove
     * @covers ::clear
     * @covers ::has
     * @covers ::replace
     * @covers \Odan\Session\Adapter\PhpSessionAdapter::start
     * @covers \Odan\Session\Adapter\PhpSessionAdapter::set
     * @covers \Odan\Session\Adapter\PhpSessionAdapter::get
     * @covers \Odan\Session\Adapter\PhpSessionAdapter::count
     * @covers \Odan\Session\Adapter\PhpSessionAdapter::remove
     * @covers \Odan\Session\Adapter\PhpSessionAdapter::clear
     * @covers \Odan\Session\Adapter\PhpSessionAdapter::has
     * @covers \Odan\Session\Adapter\PhpSessionAdapter::replace
     */
    public function testRemoveAndClear(): void
    {
        $this->session->start();
        $this->assertFalse($this->session->has('key'));

        $this->session->set('key', 'value');
        $this->assertSame('value', $this->session->get('key'));

        $this->assertTrue($this->session->has('key'));
        $this->assertFalse($this->session->has('nada'));

        $this->assertSame(1, $this->session->count());
        $this->session->set('key2', 'value');
        $this->assertSame(2, $this->session->count());

        $this->session->replace([
            'key' => 'value-new',
            'key2' => 'value2-new'
        ]);
        $this->assertSame('value-new', $this->session->get('key'));
        $this->assertSame('value2-new', $this->session->get('key2'));

        $this->session->remove('key');
        $this->assertSame(null, $this->session->get('key'));
        $this->assertSame(false, $this->session->get('key', false));

        $this->session->clear();
        $this->assertSame(0, $this->session->count());
    }

    /**
     * Test.
     *
     * @return void
     * @covers ::__construct
     * @covers ::setOptions
     * @covers ::getOptions
     * @covers \Odan\Session\Adapter\PhpSessionAdapter::setOptions
     * @covers \Odan\Session\Adapter\PhpSessionAdapter::getOptions
     */
    public function testConfig(): void
    {
        $config = [
            'name' => 'app',
            'cache_limiter' => '',
            'gc_probability' => 1,
            'gc_divisor' => 1,
            'gc_maxlifetime' => 60,
        ];

        $this->session->setOptions($config);
        $actual = $this->session->getOptions();
        $this->assertNotEmpty($actual);
        $this->assertSame('app', $actual['name']);
    }

    /**
     * Test.
     *
     * @return void
     * @covers ::__construct
     * @covers ::setCookieParams
     * @covers ::getCookieParams
     * @covers \Odan\Session\Adapter\PhpSessionAdapter::setCookieParams
     * @covers \Odan\Session\Adapter\PhpSessionAdapter::getCookieParams
     */
    public function testCookieParams(): void
    {
        $this->session->setCookieParams(60, '/', '', false, false);
        $actual = $this->session->getCookieParams();
        $this->assertNotEmpty($actual);
        $this->assertSame(60, $actual['lifetime']);
        $this->assertSame('/', $actual['path']);
        $this->assertSame('', $actual['domain']);
        $this->assertSame(false, $actual['secure']);
        $this->assertSame(false, $actual['httponly']);
    }
}
