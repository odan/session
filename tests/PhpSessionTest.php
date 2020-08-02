<?php

namespace Odan\Session\Test;

use Odan\Session\PhpSession;
use Odan\Session\SessionInterface;
use PHPStan\Testing\TestCase;
use RuntimeException;

/**
 * Test.
 *
 * @coversDefaultClass \Odan\Session\PhpSession
 */
class PhpSessionTest extends TestCase
{
    /**
     * @var SessionInterface
     */
    protected $session;

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

        $lifetime = strtotime('20 minutes') - time();
        $this->session->setCookieParams($lifetime, '/', '', false, false);

        $this->session->setName('app');
    }

    /**
     * Test.
     *
     * @covers ::start
     * @covers ::isStarted
     * @covers ::getId
     * @covers ::setId
     * @covers ::destroy
     * @covers ::save
     * @covers ::regenerateId
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

        $this->assertTrue($this->session->destroy());
        $this->session->save();
    }

    /**
     * Test.
     */
    public function testSetId(): void
    {
        $this->session->setId('123');
        $oldId = $this->session->getId();
        $this->session->setId('12345');
        $newId = $this->session->getId();
        $this->assertNotSame($oldId, $newId);
    }

    /**
     * Test.
     */
    public function testSetIdWithError(): void
    {
        $this->expectException(RuntimeException::class);
        $this->assertTrue($this->session->start());
        $this->assertTrue($this->session->isStarted());
        $this->assertNotEmpty($this->session->getId());

        $oldId = $this->session->getId();
        $this->assertTrue($this->session->regenerateId());
        $newId = $this->session->getId();
        $this->assertNotSame($oldId, $newId);

        $this->session->setId($oldId);
        $this->assertSame($oldId, $this->session->getId());
    }

    /**
     * Test.
     *
     * @covers ::setName
     * @covers ::getName
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
     * @covers ::setName
     * @covers ::getName
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
        unset($this->session);
    }

    /**
     * Test.
     */
    public function testInstance(): void
    {
        $this->assertInstanceOf(SessionInterface::class, $this->session);
    }

    /**
     * Test.
     *
     * @covers ::start
     * @covers ::set
     * @covers ::get
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
     * @covers ::all
     */
    public function testAll(): void
    {
        $this->session->start();

        // string
        $this->session->set('key', 'value');
        $this->assertSame(['key' => 'value'], $this->session->all());

        // int
        $this->session->set('key', 1);
        $valueInt = $this->session->all();
        $this->assertSame(['key' => 1], $valueInt);

        // float
        $this->session->set('key', 3.14);
        $this->assertSame(['key' => 3.14], $this->session->all());

        // bool
        $this->session->set('key', true);
        $this->assertSame(['key' => true], $this->session->all());

        $this->session->set('key', false);
        $this->assertSame(['key' => false], $this->session->all());
    }

    /**
     * Test.
     *
     * @covers ::start
     * @covers ::set
     * @covers ::get
     * @covers ::count
     * @covers ::remove
     * @covers ::clear
     * @covers ::has
     * @covers ::replace
     *
     * @return void
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
            'key2' => 'value2-new',
        ]);
        $this->assertSame('value-new', $this->session->get('key'));
        $this->assertSame('value2-new', $this->session->get('key2'));

        $this->session->remove('key');
        $this->assertNull($this->session->get('key'));

        $this->session->clear();
        $this->assertSame(0, $this->session->count());
    }

    /**
     * Test.
     *
     * @covers ::setOptions
     * @covers ::getOptions
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
