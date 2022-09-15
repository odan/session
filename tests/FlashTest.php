<?php

namespace Odan\Session\Test;

use Odan\Session\Flash;
use PHPUnit\Framework\TestCase;

class FlashTest extends TestCase
{
    public function testAddAndGet(): void
    {
        $session = [];
        $flash = new Flash($session);
        $flash->add('key1', 'value1');
        $flash->add('key2', 'value2');
        $flash->add('key2', 'value3');

        $this->assertSame([0 => 'value1'], $flash->get('key1'));
        $this->assertSame([], $flash->get('key1'));

        $this->assertSame([0 => 'value2', 1 => 'value3'], $flash->get('key2'));
        $this->assertSame([], $flash->get('key1'));

        $this->assertSame([], $flash->get('nada'));
    }

    public function testHas(): void
    {
        $session = [];
        $flash = new Flash($session);
        $flash->add('key1', 'value1');

        $this->assertTrue($flash->has('key1'));
        $this->assertTrue($flash->has('key1'));
        $this->assertFalse($flash->has('key2'));
    }

    public function testAll(): void
    {
        $session = [];
        $flash = new Flash($session);
        $flash->add('key1', 'value1');
        $flash->add('key1', 'value2');

        $this->assertTrue($flash->has('key1'));
        $this->assertSame(['key1' => [0 => 'value1', 1 => 'value2']], $flash->all());
        $this->assertFalse($flash->has('key1'));
        $this->assertSame([], $flash->all());
    }

    public function testSet(): void
    {
        $session = [];
        $flash = new Flash($session);
        $flash->set('key1', ['value1']);
        $this->assertSame([0 => 'value1'], $flash->get('key1'));

        $session = [];
        $flash = new Flash($session);
        $flash->set('key2', ['value1', 'value2']);
        $this->assertSame([0 => 'value1', 1 => 'value2'], $flash->get('key2'));
    }

    public function testClear(): void
    {
        $session = [];
        $flash = new Flash($session);
        $flash->add('key1', 'value1');

        $this->assertTrue($flash->has('key1'));
        $flash->clear();
        $this->assertFalse($flash->has('key1'));
        $this->assertSame([], $flash->all());
    }
}
