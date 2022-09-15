<?php

namespace Odan\Session\Test;

use Odan\Session\PhpSession;
use Odan\Session\SessionInterface;
use Odan\Session\SessionManagerInterface;
use PHPUnit\Framework\TestCase;

/**
 * Test.
 *
 * @coversDefaultClass \Odan\Session\PhpSession
 */
class PhpSessionTest extends TestCase
{
    protected SessionInterface $session;
    protected SessionManagerInterface $manager;

    protected function setUp(): void
    {
        $_SESSION = [];

        $this->session = new PhpSession(
            [
                'name' => 'app',
                'lifetime' => 7200,
                'path' => '/',
                'domain' => '',
                'secure' => false,
                'httponly' => true,
                'cache_limiter' => '',
                // init settings
                'gc_probability' => 1,
                'gc_divisor' => 1,
                'gc_maxlifetime' => 30 * 24 * 60 * 60,
                'save_path' => getenv('GITHUB_ACTIONS') ? '/tmp' : '',
            ]
        );

        $this->manager = $this->session;
    }

    protected function tearDown(): void
    {
        $this->manager->destroy();
        unset($this->session);
    }

    public function testStart(): void
    {
        $this->manager->start();
        $this->assertTrue($this->manager->isStarted());
        $this->assertNotEmpty($this->manager->getId());

        $oldId = $this->manager->getId();
        $this->manager->regenerateId();
        $newId = $this->manager->getId();
        $this->assertNotSame($oldId, $newId);

        $this->manager->destroy();
    }

    public function testGetFlash(): void
    {
        $this->manager->start();
        $this->session->set('key', 'value1');

        $flash = $this->session->getFlash();
        $flash->add('key', 'value');
        $this->assertSame([0 => 'value'], $flash->get('key'));
    }

    public function testSetAndGetName(): void
    {
        $this->manager->start();
        $this->assertSame('app', $this->manager->getName());
    }

    public function testSetAndGet(): void
    {
        $this->manager->start();

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

    public function testAll(): void
    {
        $this->manager->start();

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

    public function testRemoveAndClear(): void
    {
        $this->manager->start();
        $this->assertNull($this->session->get('key'));

        $this->session->set('key', 'value');
        $this->assertSame('value', $this->session->get('key'));

        $this->session->setValues(
            [
                'key' => 'value-new',
                'key2' => 'value2-new',
            ]
        );
        $this->assertSame('value-new', $this->session->get('key'));
        $this->assertSame('value2-new', $this->session->get('key2'));

        $this->session->delete('key');
        $this->assertNull($this->session->get('key'));

        $this->session->clear();
        $this->assertEmpty($this->session->all());
    }
}
