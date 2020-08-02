<?php

namespace Odan\Session\Test;

use ArrayObject;
use Odan\Session\Flash;
use PHPUnit\Framework\TestCase;

/**
 * Class FlashTest.
 */
class FlashTest extends TestCase
{
    /**
     * Test.
     */
    public function testAddAndGet(): void
    {
        $flash = new Flash(new ArrayObject());
        $flash->add('key1', 'value1');
        $flash->add('key2', 'value2');
        $flash->add('key2', 'value3');

        $this->assertSame([0 => 'value1'], $flash->get('key1'));
        $this->assertSame([], $flash->get('key1'));

        $this->assertSame([0 => 'value2', 1 => 'value3'], $flash->get('key2'));
        $this->assertSame([], $flash->get('key1'));

        $this->assertSame([], $flash->get('nada'));
    }

    /**
     * Test.
     */
    public function testHas(): void
    {
        $flash = new Flash(new ArrayObject());
        $flash->add('key1', 'value1');

        $this->assertTrue($flash->has('key1'));
        $this->assertTrue($flash->has('key1'));
        $this->assertFalse($flash->has('key2'));
    }

    /**
     * Test.
     */
    public function testAll(): void
    {
        $flash = new Flash(new ArrayObject());
        $flash->add('key1', 'value1');
        $flash->add('key1', 'value2');

        $this->assertTrue($flash->has('key1'));
        $this->assertSame(['key1' => [0 => 'value1', 1 => 'value2']], $flash->all());
        $this->assertFalse($flash->has('key1'));
        $this->assertSame([], $flash->all());
    }

    /**
     * Test.
     */
    public function testSet(): void
    {
        $flash = new Flash(new ArrayObject());
        $flash->set('key1', ['value1']);
        $this->assertSame([0 => 'value1'], $flash->get('key1'));

        $flash = new Flash(new ArrayObject());
        $flash->set('key2', ['value1', 'value2']);
        $this->assertSame([0 => 'value1', 1 => 'value2'], $flash->get('key2'));
    }

    /**
     * Test.
     */
    public function testClear(): void
    {
        $flash = new Flash(new ArrayObject());
        $flash->add('key1', 'value1');

        $this->assertTrue($flash->has('key1'));
        $flash->clear();
        $this->assertFalse($flash->has('key1'));
        $this->assertSame([], $flash->all());
    }
}
